<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_withdraw")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

if(!isset($_GET['uniq-id'])){
  echo "invalid request";
  return;
}else{
  $uniq_id = mysqli_real_escape_string($conn,$_GET['uniq-id']);
}

$select_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_uniq_id='$uniq_id'";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_id = $select_res_data['tbl_user_id'];
  $withdraw_amount = $select_res_data['tbl_withdraw_amount'];
  $withdraw_request = $select_res_data['tbl_withdraw_request'];
  $withdraw_details = $select_res_data['tbl_withdraw_details'];
  
  $withdraw_details_arr = explode(',', $withdraw_details);
  $actual_name = $withdraw_details_arr[0];
  $bank_account = $withdraw_details_arr[1];
  $bank_ifsc_code = $withdraw_details_arr[2];
  $bank_name = $withdraw_details_arr[3];
  
  $request_status = $select_res_data['tbl_request_status'];
  $request_date_time = $select_res_data['tbl_time_stamp'];
}else{
  echo 'Invalid order-id or order-id already confirmed!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Withdraw Records</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
  <div class="w-100 col-view pd-15 bg-primary">
    <h3 class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view mg-l-10">
            Manage Withdraw
        </div>
    </h3>
  </div>

  <div class="w-100 col-view v-center">
       
    <div class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 bg-white bx-shdw direct-p-mg-t">
      <br>
      <h3>Requested By: <?php echo $user_id; ?></h3>

    <br>
    <p>User id: <?php echo $user_id; ?></p>
    <p>Uniq id: <span class="cl-blue"><?php echo $uniq_id; ?></span></p>
    <p>Withdraw Request: ₹<s><?php echo $withdraw_request; ?></s></p>
    <p>Withdraw Amount: ₹<?php echo $withdraw_amount; ?></p>
    <p>Withdraw DateTime: <?php echo $request_date_time; ?></p>

    <?php if($request_status=="approve"){ ?>
      <p>Status: <label id="status_approved_tv"><?php echo $request_status; ?></label></p>
    <?php }else if($request_status=="success"){ ?>
      <p>Status: <label id="status_approved_tv"><?php echo $request_status; ?></label></p>
    <?php }else if($request_status=="rejected"){ ?>
      <p>Status: <label id="status_pending_tv"><?php echo $request_status; ?></label></p>
    <?php } ?>

    <br>
    <p>Withdraw Details:</p>

    <div class="light_back">
      <?php if($bank_ifsc_code!="null"){ ?>
        <p><?php echo 'Actual Name: '.$actual_name.'<br>Bank Name: '.$bank_name.'<br>Bank Account: '.$bank_account
      .'<br>IFSC Code: '.$bank_ifsc_code ?></p>
      <?php }else{ ?>
        <p><?php echo 'Actual Name: '.$actual_name.'<br>UPI Id: '.$bank_account
      .'<br>User State: '.$user_state; ?></p>
      <?php } ?>
    </div>

    <br>
    <?php if($request_status=="approve"){ ?>
      <button class="action-btn ft-sz-18 pd-10-15 bg-green" onclick="SucessRequest('success')">Success Request</button>
      <button class="action-btn ft-sz-18 pd-10-15 mg-t-10 bg-red" onclick="RejectRequest()">Reject Request</button>
    <?php }else if($request_status=="pending"){ ?>
      <button class="action-btn ft-sz-18 pd-10-15 bg-green" onclick="SucessRequest('success')">Success Request</button>
      <button class="action-btn ft-sz-18 pd-10-15 mg-t-10 bg-red" onclick="RejectRequest()">Reject Request</button>
    <?php } ?>
    
    </br>

  </div>
  
  </div>

</div>

<script>
  function RejectRequest(){
    window.open("update-request.php?order-type=rejected&order-id=<?php echo $uniq_id; ?>");
  }

  function SucessRequest(status){
    window.open("update-request.php?order-type="+status+"&order-id=<?php echo $uniq_id; ?>");
  }
</script>
    
</body>
</html>