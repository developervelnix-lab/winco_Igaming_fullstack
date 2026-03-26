<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_users_data")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}
 


if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_uniq_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

function generateOrderID($length = 15) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return 'RR0'.$randomString;
}

$uniqId = generateOrderID();

$user_balance = 0;
$account_level = 1;
$select_sql = "SELECT * FROM tblusersdata WHERE tbl_uniq_id='$user_uniq_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_mobile_num = $select_res_data['tbl_mobile_num'];
  $user_email_id = $select_res_data['tbl_email_id'];
  $user_balance = $select_res_data['tbl_balance'];
  $account_level = $select_res_data['tbl_account_level'];
}else{
  echo 'Invalid User-Id!';
  return;
}

// update settings btn
if (isset($_POST['submit'])){
    
  $new_user_level = mysqli_real_escape_string($conn,$_POST['new_user_level']);
  $new_user_balance = mysqli_real_escape_string($conn,$_POST['updated_user_balance']);
  $new_user_password = mysqli_real_escape_string($conn,$_POST['new_user_password']);
  
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-m-Y h:i a');
  
  if(!$IS_PRODUCTION_MODE && $new_user_balance > 500){
    echo "Game is under Demo Mode. So, you can not add balance more than 500rs";
    return;
  }

  if($new_user_balance < 0 || $new_user_balance == ""){
      echo "Updated Balance can't be less than 0";
      return;
  }else if($new_user_balance <= $user_balance){
    $update_balance = $user_balance - $new_user_balance;
  }else{
    $update_balance = $new_user_balance;
  }
  
  if($new_user_password != ""){
    $user_hashed_password = password_hash($new_user_password,PASSWORD_BCRYPT);
    if($new_user_balance!=$user_balance){
        $update_sql = "UPDATE tblusersdata SET tbl_balance='{$new_user_balance}',tbl_password='{$user_hashed_password}',tbl_account_level='{$new_user_level}' WHERE tbl_uniq_id='{$user_uniq_id}'"; 
    }else{
        $update_sql = "UPDATE tblusersdata SET tbl_password='{$user_hashed_password}',tbl_account_level='{$new_user_level}' WHERE tbl_uniq_id='{$user_uniq_id}'"; 
    }
  }else{
    $update_sql = "UPDATE tblusersdata SET tbl_balance='{$new_user_balance}',tbl_account_level='{$new_user_level}' WHERE tbl_uniq_id='{$user_uniq_id}'";
  }
  
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  
  if ($update_result){
    if($new_user_balance < $user_balance){
      $request_status = "deducted";
      $update_balance = "-".$update_balance;
    }else{
      $request_status = "success";
    }
    
    $recharge_mode = "Manual";
    $recharge_details = "Manual-Method";
    
    $insert_sql = $conn->prepare("INSERT INTO tblusersrecharge(tbl_uniq_id,tbl_user_id,tbl_recharge_amount,tbl_recharge_mode,tbl_recharge_details,tbl_request_status,tbl_time_stamp) VALUES(?,?,?,?,?,?,?)");
    $insert_sql->bind_param("sssssss", $uniqId,$user_uniq_id,$update_balance,$recharge_mode, $recharge_details,$request_status,$curr_date_time);
    $insert_sql->execute();
  
    if ($insert_sql->error == "") { ?>
      <script>
        alert('Account updated!');
        window.history.back();
      </script>
  <?php }else{ ?>

    <script>
      alert('Failed to update!');
      window.history.back();
    </script>

  <?php } }else{ ?>
  
  <script>
    alert('Failed to update account!');
  </script>

<?php } } ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../components/header.php"; ?>
  <title>Manage: Update Account</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
  <div class="w-100 col-view pd-15 bg-primary">
    <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view ft-sz-20 mg-l-10">Update UserData</div>
    </div>
  </div>

  <div class="w-100 col-view v-center">

   <form class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 bg-white bx-shdw" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
 	<h3>Update Account</h3><br>
 	<input type="text" name="user_uniq_id" placeholder="Enter Unique Id" value="<?php echo $user_uniq_id; ?>" class="w-100 cus-inp mg-t-15" required disabled>
 	
 	<div class="mg-t-20">
 	  <p>Mobile Number</p>
   	  <input type="number" name="new_mobile_number" placeholder="Enter Mobile Number" value="<?php echo $user_mobile_num; ?>" class="w-100 cus-inp mg-t-5" required disabled>
 	</div>
 	
    <div class="mg-t-20">
 	  <p>Email Id</p>
 	  <input type="text" name="new_email_id" placeholder="Enter Email Id" value="<?php echo $user_email_id; ?>" class="w-100 cus-inp mg-t-5" required disabled>
  	</div>
 	
 	<div class="mg-t-20">
 	  <p>Account Balance</p>
 	  <input type="number" name="updated_user_balance" id="updated_user_balance" value="<?php echo $user_balance; ?>" hidden required>
 	  <input type="number" step="any" name="new_user_balance" id="inp_new_user_balance" placeholder="Enter Balance" class="w-100 cus-inp mg-t-5" onInput="onInpChange()">
 	  <div class="dspl-in-block mg-t-10">
 	      Total:
 	      <span id="total_balance_tv" class="cl-red">₹<?php echo $user_balance; ?></span>
 	  </div>
 	</div>
 	
 	<div class="mg-t-30">
 	  <p>Account Password</p>
 	  <input type="text" name="new_user_password" placeholder="Enter Password" class="w-100 cus-inp mg-t-5">
 	</div>
 	
 	<div class="mg-t-20">
 	  <p>Account Level</p>
 	  <select class="w-100 cus-inp mg-t-5" name="new_user_level">
 	    <option value="1" <?php if($account_level=="1"){ ?>selected<?php } ?>>Normal</option>
 	    <option value="2" <?php if($account_level=="2"){ ?>selected<?php } ?>>Premium</option>
 	    <option value="3" <?php if($account_level=="3"){ ?>selected<?php } ?>>Agent</option>
 	  </select>
 	</div>
    
 	<input type="submit" name="submit" value="Update Data" class="w-100 br-r-5 mg-t-30 action-btn ft-sz-20">
  </form>
  
  </div>
  
</div>

<script>
    let total_balance_tv = document.querySelector("#total_balance_tv");
    let updated_user_balance = document.querySelector("#updated_user_balance");
    let inp_new_user_balance = document.querySelector("#inp_new_user_balance");
    let updated_balance = parseFloat(updated_user_balance.value);
    
    function onInpChange(){
        let new_balance = parseFloat(inp_new_user_balance.value);
        
        if(updated_user_balance.value==""){
            updated_balance = 0.00;
        }else if(inp_new_user_balance.value==""){
            new_balance = 0.00;
        }
        
        let total_balance = (updated_balance+new_balance).toFixed(2);
        
        if(new_balance <= 0){
           if(new_balance < 0 && total_balance >= 0) {
            total_balance_tv.innerHTML = "₹"+total_balance;
            updated_user_balance.value = total_balance;
           }else{
            total_balance_tv.innerHTML = "₹"+updated_balance;
            updated_user_balance.value = updated_balance;
           }
        }else{
           total_balance_tv.innerHTML = "₹"+total_balance;
           updated_user_balance.value = total_balance;
        }
    }
</script>

</body>
</html>