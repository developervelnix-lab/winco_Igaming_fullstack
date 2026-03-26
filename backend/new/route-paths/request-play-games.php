<?php
$resArr = [];
$resArr["status_code"] = "failed";

date_default_timezone_set("Asia/Kolkata");
$curr_date_time = date("d-m-Y h:i a");
error_reporting(0);
function returnRequest($resArr){
   echo json_encode($resArr);
   exit();
}

function formatNumber($number){
    return number_format($number, 2, ".", "");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $const_user_id = "";
    function generateOrderID($length = 15)
    {
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return "GA0" . $randomString;
    }

    function encrypt($data, $key)
    {
        return base64_encode(
            openssl_encrypt($data, "AES-256-ECB", $key, OPENSSL_RAW_DATA)
        );
    }

    $json = file_get_contents("php://input");
    $data = json_decode($json);
    $const_user_id = $data->USER_ID;
    $const_game_name = $data->GAME_NAME;
    $const_game_uid = $data->GAME_UID;

    if (
        $const_user_id != "" &&
        $const_game_name != "" &&
        $const_game_uid != ""
    ) {
        $headerObj = new RequestHeaders();
        $headerObj->checkAllHeaders();
        $secret_key = $headerObj->getAuthorization();

        if ($secret_key == "null" || $secret_key == "") {
            $resArr["status_code"] = "authorization_error";
            echo json_encode($resArr);
            return;
        }
    } else {
        $resArr["status_code"] = "invalid_params";
        returnRequest($resArr);
    } 
    $select_sql = "SELECT * FROM tblusersdata WHERE tbl_uniq_id='{$const_user_id}' AND tbl_auth_secret ='{$secret_key}' ";
    $select_query = mysqli_query($conn, $select_sql);
    if (mysqli_num_rows($select_query) > 0) {
       $select_sql = "SELECT tbl_balance,tbl_requiredplay_balance,tbl_withdrawl_balance,tbl_joined_under,tbl_account_status FROM tblusersdata WHERE tbl_uniq_id='$const_user_id'";
    $select_query = mysqli_query($conn, $select_sql);

    if (mysqli_num_rows($select_query) > 0) {
        $res_data = mysqli_fetch_assoc($select_query);
        $user_refered_by = $res_data["tbl_joined_under"];

        if ($res_data["tbl_account_status"] == "true") {
               $query = "SELECT tbl_service_value FROM tblservices WHERE tbl_service_name = 'GAME_STATUS'";
               $result = mysqli_query($conn, $query);
               if ($data = mysqli_fetch_assoc($result)) {
                 if ($data['tbl_service_value'] == "false") {
                  returnRequest(["status_code" => "game_off"]);
               }}
           if ($res_data["tbl_balance"] < 100) {
                    $resArr["status_code"] = "balance_error";
                    returnRequest($resArr);
            } 
            $updated_balance = $res_data["tbl_balance"];
            $timestamp = round(microtime(true) * 1000);
            
            $payloadData = json_encode([
                "agency_uid" => $AGENCY_UID,
                "timestamp" => $timestamp,
                "member_account" => $PLAYER_PREFIX.$const_user_id,
                "game_uid" => $const_game_uid,
                "credit_amount" => formatNumber($updated_balance),
                "currency_code" => "INR",
                "language" => "en",
                "home_url" => $API_ACCESS_URL,
                "platform" => "web",
                "callback_url" => $API_TARGET_URL."game/",
            ]);
            $payload = encrypt($payloadData, $AES_SECRET_KEY);
            $headers = ["Content-Type: application/json"];
            $data = json_encode([
                "agency_uid" => $AGENCY_UID,
                "timestamp" => $timestamp,
                "payload" => $payload,
            ]);
            
            $ch = curl_init($GAME_SERVER_URL."/game/v1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
            $json_data = json_decode($response,true);
            if($json_data["code"] != 0){
               $resArr["status_code"] = "server_error";   
               $resArr["message"] = $json_data["code"];   
               returnRequest($resArr);            
            }
            $game_url = $json_data["payload"]["game_launch_url"];
            $match_order_id = generateOrderID();
            $match_result = "0";
            $open_price = "0";
            $match_status = "wait";
            $match_profit = "0";
            $const_invested_on = "0";
            $total_amount = "0";
            $const_num_lot = "0";
            $invested_amount = "0";
            $match_fee = "0";
            $match_details="";
            $bet_type="";
            $odds="";
            $match_details="";
            $curr_date = date("Y-m-d");
            $check_sql = $conn->prepare("SELECT COUNT(*) FROM tblmatchplayed  WHERE tbl_user_id = ? AND tbl_period_id = ? AND DATE(STR_TO_DATE(tbl_time_stamp, '%d-%m-%Y %h:%i %p')) = ?");
            $check_sql->bind_param("sss", $const_user_id, $const_game_uid, $curr_date);
            $check_sql->execute();
            $check_sql->bind_result($count);
            $check_sql->fetch();
            $check_sql->close();
            if ($count == 0) {
              $insert_sql = $conn->prepare(
        "INSERT INTO tblmatchplayed (
            tbl_user_id, tbl_uniq_id, tbl_period_id, tbl_invested_on, 
            tbl_match_cost, tbl_lot_size, tbl_match_invested, tbl_match_fee, 
            tbl_match_profit, tbl_match_result, tbl_last_acbalance, 
            tbl_match_status, tbl_project_name, tbl_match_details, tbl_bet_type, tbl_odds, tbl_time_stamp
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $insert_sql->bind_param(
        "sssssssssssssssss",
        $const_user_id,
        $match_order_id,
        $const_game_uid,
        $const_invested_on,
        $total_amount,
        $const_num_lot,
        $invested_amount,
        $match_fee,
        $match_profit,
        $match_result,
        $updated_balance,
        $match_status,
        $const_game_name,
        $match_details,
        $bet_type,
        $odds,
        $curr_date_time);
        $insert_sql->execute();
        if ($insert_sql->error == "") {
           $resArr["data"]["game_url"] = $game_url;
           $resArr["status_code"] = "success";
        } else {
          $resArr["status_code"] = "sql_failed";
          }
        $insert_sql->close();
        } else {
           $resArr["data"]["game_url"] = $game_url;
           $resArr["status_code"] = "success";
          }
        } else {
            $resArr["status_code"] = "account_error";
        }
    } else {
        $resArr["status_code"] = "auth_error";
    }
} else {
    $resArr["status_code"] = "authorization_error";
}
    mysqli_close($conn);
    echo json_encode($resArr);
}
?>
