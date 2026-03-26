<?php
define("ACCESS_SECURITY","true");
include '../../../security/config.php';
include '../../../security/constants.php';
include '../../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_settings")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../../logout-account');
}


function generateUniqId($length = 6){
    $characters = "0123456789ABCDEFGHIJKLMNOPRSTUZX";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$uniq_reward_id = generateUniqId();

// update settings btn
if (isset($_POST['submit'])){
  if(!$IS_PRODUCTION_MODE){
    echo "Game is under Demo Mode. So, you can not add or modify.";
    return;
  }

  $salary_userid = mysqli_real_escape_string($conn,$_POST['salary_userid']);
  $salary_amount = mysqli_real_escape_string($conn,$_POST['salary_amount']);
  
  
  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-m-Y h:i:s a');

    $insert_sql = "INSERT INTO tblotherstransactions(tbl_user_id,tbl_received_from,tbl_transaction_type,tbl_transaction_amount,tbl_transaction_note,tbl_time_stamp) VALUES('{$salary_userid}','app','Agent Salary','{$salary_amount}','Agent Salary','{$curr_date_time}')";
    $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');
// Ensure salary_wager is set and sanitize it
$salary_wager = isset($_POST['salary_wager']) ? $_POST['salary_wager'] : null;

if ($salary_wager === 'true') {
    $update_sql = "UPDATE tblusersdata 
                   SET tbl_balance = tbl_balance + '{$salary_amount}', 
                       tbl_requiredplay_balance = tbl_requiredplay_balance + '{$salary_amount}' 
                   WHERE tbl_uniq_id = '{$salary_userid}'"; 
} else {
    $update_sql = "UPDATE tblusersdata 
                   SET tbl_balance = tbl_balance + '{$salary_amount}' 
                   WHERE tbl_uniq_id = '{$salary_userid}'"; 
}
$update_result = mysqli_query($conn, $update_sql) or die('Error: ' . mysqli_error($conn));

    if($insert_result && $update_sql){
      echo "<script>alert('Salary Updated!');window.history.back();</script>";
    }   
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php" ?>
  <title>Manage: New Salary</title>
  <link href='../../style.css' rel='stylesheet'>
<style>
form{
  width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 15px 20px;
  border-radius: 5px;
  background: #ffffff;
  box-shadow: 0.1px 2px 8px 4px rgba(0, 0, 0, 0.05);
}

input[type="text"],input[type="number"],select{
   width: 100%;
   height: 50px;
   margin: 10px 0;
   font-size: 20px;
   padding: 0 10px;
   border: 1px solid rgba(0,0,0,0.09);
}

form textarea{
  width: 90%;
  height: 150px;
  padding: 10px;
  font-size: 20px;
  resize: none;
}

@media (max-width: 500px) {
  form{
    width: 95%;
  }
}
</style>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
  <div class="w-100 col-view pd-15 bg-primary">
    <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view ft-sz-20 mg-l-10">Create Salary</div>
    </div>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">

 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">UserId</span>
 	    <input type="text" name="salary_userid" placeholder="User ID" required>
 	  </div>
 	  
 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">Salary Amount</span>
 	    <input type="text" name="salary_amount" placeholder="Salary Amount" required>   
 	  </div>
      
      <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">Wager ?</span>
 	    <input type="radio" name="salary_wager" placeholder="Salary Amount" value="true" checked> Yes  
 	    <input type="radio" name="salary_wager" placeholder="Salary Amount" value="false" > No  
 	  </div>
 	  
   	  <input type="submit" name="submit" value="Give Salary" class="w-100 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>
    
</body>
</html>