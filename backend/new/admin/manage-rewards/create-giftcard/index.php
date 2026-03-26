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

  $gift_card_id = mysqli_real_escape_string($conn,$_POST['gift_card_id']);
  $gift_card_reward = mysqli_real_escape_string($conn,$_POST['gift_card_reward']);
  $gift_card_limit = mysqli_real_escape_string($conn,$_POST['gift_card_limit']);
  
  $input_single_user_id = mysqli_real_escape_string($conn,$_POST['gift_card_targeted_id']);
  $input_balance_required = mysqli_real_escape_string($conn,$_POST['gift_card_balance_limit']);
  
  if($input_single_user_id==""){
    $input_single_user_id = "none";  
  }
  
  if($input_balance_required==""){
    $input_balance_required = "none";  
  }
  
  
  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-m-Y h:i:s a');
  
  $pre_sql = "SELECT * FROM tblgiftcards WHERE tbl_giftcard='{$gift_card_id}' ";
  $pre_result = mysqli_query($conn, $pre_sql) or die('error');

  if (mysqli_num_rows($pre_result) > 0){
     echo "<script>alert('Sorry, Giftcard Already Exist!');window.history.back();</script>";
  }else{
    $insert_sql = "INSERT INTO tblgiftcards(tbl_giftcard,tbl_giftcard_bonus,tbl_giftcard_limit,tbl_giftcard_targeted_id,tbl_giftcard_balance_limit,tbl_giftcard_status,gift_date_time) VALUES('{$gift_card_id}','{$gift_card_reward}','{$gift_card_limit}','{$input_single_user_id}','{$input_balance_required}','true','{$curr_date_time}')";
    $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');
    
    if($insert_result){
      echo "<script>alert('Giftcard Created!');window.history.back();</script>";
    }   
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php" ?>
  <title>Manage: New Giftcard</title>
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
          
        <div class="col-view ft-sz-20 mg-l-10">Create Giftcard</div>
    </div>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">

 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">GiftCard Id</span>
 	    <input type="text" name="gift_card_id" placeholder="GiftCard Id" value="<?php echo $uniq_reward_id; ?>" required>
 	  </div>
 	  
 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">GiftCard Reward</span>
 	    <input type="text" name="gift_card_reward" placeholder="GiftCard Reward" required>   
 	  </div>
 	  
 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">GiftCards Limit</span>
 	    <input type="number" name="gift_card_limit" placeholder="Giftcards Limit" required>
 	  </div>
 	  
 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">GiftCards Targeted ID (Optional)</span>
 	    <input type="text" name="gift_card_targeted_id" placeholder="Giftcards Targeted ID (Optional)">  
 	  </div>
 	  
 	  <div class="w-100 mg-t-10">
 	    <span class="ft-sz-13">Giftcards Balance Limit (Optional)</span>
 	    <input type="number" name="gift_card_balance_limit" placeholder="Giftcards Balance Limit (Optional)">
 	  </div>
      
   	  <input type="submit" name="submit" value="Create GiftCard" class="w-100 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>
    
</body>
</html>