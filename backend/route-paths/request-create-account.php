<?php
include "../components/helper-functions.php";

class AccountManager {
    private $conn;
    private $helperFunctions;
    private $curr_date = "";
    private $curr_time = "";
    
    private $const_userid_length = 7;
    private $is_otp_allowed = false;
    private $is_signup_allowed = false;
    private $signup_bonus = 0;
    
    private $const_inp_user_id = "";
    private $const_inp_mobile = "";
    private $const_inp_password = "";
    private $const_inp_otp = "";
    private $const_inp_refercode = "";
    
    private $const_empty_val = "";
    private $const_zero_val = "0";
    private $const_true_val = "true";
    private $const_account_level = "1";
    
    private $const_default_invite_code = "";
    
    private $resArr = [
        "data" => [],
        "status_code" => "failed"
    ];

    public function __construct($conn, HelperFunctions $helperFunctions, $curr_date, $curr_time, $default_referal) {
        $this->conn = $conn;
        $this->helperFunctions = $helperFunctions;
        $this->curr_date = $curr_date;
        $this->curr_time = $curr_time;
        $this->const_inp_refercode = $default_referal;
        $this->const_default_invite_code = $default_referal;
    }

    public function __destruct() {
        $this->conn->close();
    }
    
    private function returnRequest() {
        echo json_encode($this->resArr);
        exit();
    }
    
    private function getParams() {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if (is_object($data) && property_exists($data, 'SIGNUP_MOBILE') && property_exists($data, 'SIGNUP_PASSWORD') && property_exists($data, 'SIGNUP_OTP')) {
            $this->const_inp_mobile = $data->SIGNUP_MOBILE;
            $this->const_inp_password = $data->SIGNUP_PASSWORD;
            $this->const_inp_otp = $data->SIGNUP_OTP;
            
            if(property_exists($data, 'SIGNUP_INVITE_CODE')){
                if($data->SIGNUP_INVITE_CODE!=""){
                   $this->const_inp_refercode = $data->SIGNUP_INVITE_CODE; 
                }
            }
        } else {
            $this->resArr['status_code'] = "invalid_params";
            $this->returnRequest();
        }
    }
    
    private function getServiceDetails(){
        $returnVal = true;
    
        $sql = "SELECT * FROM tblservices WHERE tbl_service_value!='' ";
        $sql_query = mysqli_query($this->conn, $sql);
    
        if (mysqli_num_rows($sql_query) > 0) {
        
        while($resp_data = mysqli_fetch_array($sql_query)){
            if($resp_data['tbl_service_name']=="SIGNUP_ALLOWED"){
              $this->is_signup_allowed = $this->helperFunctions->stringToBoolean($resp_data['tbl_service_value']);
            }else if($resp_data['tbl_service_name']=="SIGNUP_BONUS"){
              $this->signup_bonus = $resp_data['tbl_service_value'];
            }else if($resp_data['tbl_service_name']=="OTP_ALLOWED"){
              $this->is_otp_allowed = $this->helperFunctions->stringToBoolean($resp_data['tbl_service_value']);
            }
        }
        
        }else{
          $returnVal = false;  
        }
        
        return $returnVal;
    }
    
    private function filterMobileNumber(){
        $reponse = true;
        
        $bannedMobNumList = "1234567890,1234567899,0987654321";
        $bannedMobNumber = explode(",", $bannedMobNumList);
        if (strlen($this->const_inp_mobile) != 10 || in_array($this->const_inp_mobile, $bannedMobNumber)) {
          $reponse = false;
        }
        
        return $reponse;
    }
    
    function isIncrementingArray($array) {
        $response = true;

        for ($i = 1; $i < count($array); $i++) {
            // Check if the difference between the current and previous element is greater than 5
            if (($array[$i] - $array[$i - 1]) > 5) {
              $response = false;
              break;
            }
        }

        return $response;
    }
    
    function checkForDuplicateId($unique_id){
        $returnVal = false;
        
        $sql = "SELECT tbl_mobile_num FROM tblusersdata WHERE tbl_mobile_num='{$this->const_inp_mobile}' OR tbl_uniq_id='{$unique_id}' ";
        $sql_query = mysqli_query($this->conn, $sql);
    
        if (mysqli_num_rows($sql_query) <= 0) {
          $sql_records = "SELECT tbl_mobile_num FROM tblusersdata ORDER BY id DESC LIMIT 5";
          $sql_records_query = mysqli_query($this->conn, $sql_records);
          
          $last_records_array=array();
          if (mysqli_num_rows($sql_records_query) >= 5) {
              
            while($row = mysqli_fetch_array($sql_records_query)){
              array_push($last_records_array,$row['tbl_mobile_num']);
            }
        
            if($this->isIncrementingArray($last_records_array)){
              $returnVal = false;
            }else{
              $returnVal = true;
            }
          }else{
            $returnVal = true;  
          }
        }
        
        return $returnVal;
    }
    
    private function checkForNewID(){
        $return = "";
          
        for ($x = 0; $x <= 3; $x++) {
            $temp_unique_id = $this->helperFunctions->generateRandNumber($this->const_userid_length);
            
            if($this->checkForDuplicateId($temp_unique_id)==true){
              $return = $temp_unique_id;
              break;
            }
        }
          
        if($return==""){
            $return = "failed"; 
        }
        
        return $return;
    }
    
    private function checkAccountInfo(){
        $return = true;
        
        $sql = "SELECT * FROM tblusersdata WHERE tbl_mobile_num='{$this->const_inp_mobile}' ";
        $sql_query = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($sql_query) > 0) {
            $resp_data = mysqli_fetch_assoc($sql_query);
            $account_status = $resp_data['tbl_account_status'];
            
            if($account_status=="true"){
                $return = false;
                $this->resArr["status_code"] = "already_registered";
            }else if($account_status=="ban"){
                $return = false;
                $this->resArr["status_code"] = "account_suspended";
            }
            
        }else{
            
          $response = $this->checkForNewID();
          
          if($response!="failed"){
            $this->const_inp_user_id = $response;
          }  
        }
        
        return $return;
    }
    
    private function checkInviteCode($invite_code){
        $return = false;
        
        if($invite_code==""){
           return false;
        }
        
        if($invite_code==$this->const_default_invite_code){
            return true;
        }
        
        
        $sql = "SELECT * FROM tblusersdata WHERE tbl_uniq_id='{$invite_code}' ";
        $sql_query = mysqli_query($this->conn, $sql);
        
        if (mysqli_num_rows($sql_query) > 0) {
            $resp_data = mysqli_fetch_assoc($sql_query);
            $account_status = $resp_data['tbl_account_status'];
            
            if($account_status=="true"){
                $return = true;
            }
        }
        
        return $return;
    }
    
    private function checkOTP(){
        $returnVal = false;
        
        if($this->is_otp_allowed){
            $sql = "SELECT * FROM tblrecentotp WHERE tbl_mobile_num='{$this->const_inp_mobile}' AND tbl_otp_date='{$this->curr_date}' AND tbl_otp='{$this->const_inp_otp}' ORDER BY id DESC LIMIT 1 ";
            $sql_query = mysqli_query($this->conn, $sql);
            
            if (mysqli_num_rows($sql_query) > 0) {
                $returnVal = true;
            }
        }else{
           $returnVal = true;  
        }
        
        return $returnVal;
    }
    
    private function addNewTransaction($transaction_amount, $transaction_note = ''){
        if($transaction_amount <= 0){
            return;
        }
        
        $receive_from = "app";
        $transaction_type = "signupbonus";
        $transaction_date_time = $this->curr_date." ".$this->curr_time;
        
        $insert_sql = $this->conn->prepare("INSERT INTO tblotherstransactions(tbl_user_id,tbl_received_from,tbl_transaction_type,tbl_transaction_amount,tbl_transaction_note,tbl_time_stamp) VALUES(?,?,?,?,?,?)");
        $insert_sql->bind_param("ssssss", $this->const_inp_user_id, $receive_from, $transaction_type,$transaction_amount,$transaction_note,$transaction_date_time);
        $insert_sql->execute();
    }
    
    
    private function createNewAccount(){
        $account_avatar = $this->helperFunctions->generateRandInt(1,9);
        $account_name = "MEMBER".$this->helperFunctions->generateRandNumber(4);
        $curr_date_time = $this->curr_date." ".$this->curr_time;
        $account_auth_secret = $this->helperFunctions->generateRandString(30);
        $account_password = password_hash($this->const_inp_password,PASSWORD_BCRYPT);
        
        if($this->signup_bonus > 0){
            $const_account_balance = $this->signup_bonus;
        }else{
            $const_account_balance = $this->const_zero_val;
        }
        
        $insert_sql = $this->conn->prepare(
            "INSERT INTO tblusersdata(tbl_uniq_id,tbl_auth_secret,tbl_avatar_id,tbl_mobile_num,tbl_email_id,tbl_full_name,tbl_password,tbl_balance,tbl_requiredplay_balance,tbl_withdrawl_balance,tbl_commission_balance,tbl_freezed_balance,tbl_joined_under,tbl_last_active_date,tbl_last_active_time,tbl_account_level,tbl_account_status,tbl_user_joined) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $insert_sql->bind_param(
            "ssssssssssssssssss",
            $this->const_inp_user_id,
            $account_auth_secret,
            $account_avatar,
            $this->const_inp_mobile,
            $this->const_empty_val,
            $account_name,
            $account_password,
            $const_account_balance,
            $this->const_zero_val,
            $this->const_zero_val,
            $this->const_zero_val,
            $this->const_zero_val,
            $this->const_inp_refercode,
            $this->curr_date,
            $this->curr_time,
            $this->const_account_level,
            $this->const_true_val,
            $curr_date_time
        );
        $insert_sql->execute();

        if ($insert_sql->error == "") {
            
            // this will add new transaction if signup balance is > 0
            $this->addNewTransaction($const_account_balance);
            
            $index['account_id'] = $this->const_inp_user_id;
            $index['account_mobile_num'] = $this->const_inp_mobile;
            $index['account_balance'] = $const_account_balance;
            $index['account_w_balance'] = $this->const_zero_val;
            $index['account_refered_by'] = $this->const_inp_refercode;
            $index['auth_secret_key'] = $account_auth_secret;
            array_push($this->resArr['data'], $index);
        
            $this->resArr['status_code'] = "success";
        }
    }
    
    private function validateDetails(){
        if(!$this->filterMobileNumber()){
           $this->resArr['status_code'] = "invalid_mobile"; 
        }else if(strlen($this->const_inp_password) < 6){
           $this->resArr['status_code'] = "password_weak"; 
        }else if(!$this->is_signup_allowed){
           $this->resArr['status_code'] = "signup_not_allowed"; 
        }else{
            if($this->checkAccountInfo()){
              if($this->const_inp_mobile!="" && $this->const_inp_refercode==""){
                  
                $this->createNewAccount();
                
              }else if($this->const_inp_mobile!="" && $this->const_inp_refercode!=""){
                  
                  if($this->checkInviteCode($this->const_inp_refercode)){
                    $this->createNewAccount();  
                  }else{
                    $this->resArr['status_code'] = "invalid_refer_code";  
                  }
                  
              }else{
                  $this->resArr['status_code'] = "invalid_params";
              }
            }
        }
    }
    
    private function processRequest(){
        if($this->const_inp_mobile!="" && $this->const_inp_password!="" && $this->const_inp_otp!=""){
            if($this->checkOTP()){
               $this->validateDetails(); 
            }else{
               $this->resArr['status_code'] = "invalid_otp";
            }
            
        }else{
            $this->resArr['status_code'] = "invalid_params";
        }
        
        $this->returnRequest();
    }
    
    public function process() {
        $this->getParams();
        $this->getServiceDetails();
        $this->processRequest();
    }
}

// Initializing database connection
if ($conn->connect_error) {
    die("db_conn_error");
}


// initializing new required objects
$helperFunctions = new HelperFunctions();

// initializing new object & calling function
$accountManager = new AccountManager($conn, $helperFunctions, $curr_date, $curr_time, $DEFAULT_ACCOUNT_ID);
$accountManager->process();
?>