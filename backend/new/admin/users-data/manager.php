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

if(!isset($_GET['id'])){
  echo "invalid request";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['id']);
}

$select_sql = "SELECT * FROM tblusersdata WHERE tbl_uniq_id='$user_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_mobile_num = $select_res_data['tbl_mobile_num'];
  $user_full_name = $select_res_data['tbl_full_name'];
  $user_email_id = $select_res_data['tbl_email_id'];
  $user_balance = $select_res_data['tbl_balance'];
  $user_refered_by = $select_res_data['tbl_joined_under'];
  $user_last_active_date = $select_res_data['tbl_last_active_date'];
  $user_last_active_time = $select_res_data['tbl_last_active_time'];
  $user_withdrawl_balance = $select_res_data['tbl_withdrawl_balance'];
  $account_level = $select_res_data['tbl_account_level'];
  $user_status = $select_res_data['tbl_account_status'];
  $user_joined = $select_res_data['tbl_user_joined'];
  
}else{
  echo 'Invalid user-id!';
  return;
}

$user_reward_balance = 0;

$select_sql = "SELECT * FROM tblotherstransactions WHERE tbl_user_id='{$user_id}' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  while ($row = mysqli_fetch_assoc($select_result)){
    $user_reward_balance += $row['tbl_transaction_amount'];
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title>Manage: User</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
    <div class="w-100 col-view pd-15 bg-primary">
      <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
          <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
          <div class="col-view ft-sz-20 mg-l-10">View UserData</div>
      </div>
    </div>
    
  <div class="w-100 col-view v-center">
       
    <div class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 direct-p-mg-t bg-white bx-shdw">

    <br>
    <p>User Mobile: <?php echo $user_mobile_num; ?></p>
    <p>User Name: <?php echo $user_full_name; ?></p>
    <p>User Email: <?php echo $user_email_id; ?></p>
    <p>User Balance: ₹<?php echo $user_balance; ?></p>
    <p>User Rewards: ₹<?php echo $user_reward_balance; ?></p>
    <!--<p>User Withdrawl Balance: ₹<?php echo $user_withdrawl_balance; ?></p>-->
    <p>User Account Level: <?php echo $account_level; ?></p>
    <p>User Refered By: <?php echo $user_refered_by; ?></p>
    <p>User Last Active: <?php echo $user_last_active_date.' '.$user_last_active_time; ?></p>
    <p>User Joined: <?php echo $user_joined; ?></p>
    </br>
    <?php if($user_status=="true"){ ?>
      <p id="status_active">User Status: <label class="pd-5-10 br-r-5 cl-white bg-green">Active</label></p>
    <?php }else if($user_status=="false"){ ?>
      <p id="status_ban">User Status: <label>In progress</label></p>
     <?php }else { ?>
      <p id="status_ban">User Status: <label class="pd-5-10 br-r-5 cl-white bg-red">Ban</label></p>
     <?php } ?>
    
    <br>
    <?php if($user_status=="true"){ ?>
      <br>
      <button class="action-btn ft-sz-18 pd-10-15 bg-red" onclick="BanAccount()">Ban Account</button>
    <?php }else{ ?>
      <br>
      <button class="action-btn ft-sz-18 pd-10-15 bg-green" onclick="ActiveAccount()">Active Account</button>
    <?php } ?>

    <a href="update-account.php?user-id=<?php echo $user_id; ?>" class="txt-deco-n action-btn ft-sz-18 pd-10-15 mg-t-10">Update&nbsp;<i class='bx bx-chevron-right' ></i></a>
    
    <a href="view-referals.php?id=<?php echo $user_id; ?>" class="txt-deco-n action-btn ft-sz-18 pd-10-15 mg-t-10">Referals&nbsp;<i class='bx bx-chevron-right' ></i></a>
    <a href="view-activities.php?user-id=<?php echo $user_id; ?>" class="txt-deco-n action-btn ft-sz-18 pd-10-15 mg-t-10">Activities&nbsp;<i class='bx bx-chevron-right' ></i></a>
    <a href="all-notices?user-id=<?php echo $user_id; ?>" class="txt-deco-n action-btn ft-sz-18 pd-10-15 mg-t-10">Send Notice&nbsp;<i class='bx bx-chevron-right' ></i></a>
    </br>
    
    </div>
    
  </div>

</div>

<script>
  function BanAccount(){
      if(confirm("Are you sure you want to ban this account?")){
        window.open("update-request.php?request-type=ban&user-id=<?php echo $user_id; ?>");  
        window.location.reload();
      }
  }

  function ActiveAccount(){
      if(confirm("Are you sure you want to active this account?")){
        window.open("update-request.php?request-type=true&user-id=<?php echo $user_id; ?>");
        window.location.reload();
      }
  }
</script>
    
</body>
</html>