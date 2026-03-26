<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_recharge")=="false"){
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

$select_sql = "SELECT * FROM tblusersrecharge WHERE tbl_uniq_id='$uniq_id'";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_id = $select_res_data['tbl_user_id'];
  $recharge_amount = $select_res_data['tbl_recharge_amount'];
  $recharge_mode = $select_res_data['tbl_recharge_mode'];
  $recharge_details = $select_res_data['tbl_recharge_details'];
  $request_status = $select_res_data['tbl_request_status'];
  $request_date_time = $select_res_data['tbl_time_stamp'];
}else{
  echo 'Invalid order-id!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title>Manage Recharge Request</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
  <div class="w-100 col-view pd-15 bg-primary">
    <h3 class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view mg-l-10">
            Manage Recharge
        </div>
    </h3>
  </div>

  <div class="w-100 col-view v-center">
       
    <div class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 bg-white bx-shdw direct-p-mg-t">
      <br>
      <h3>Requested By: <?php echo $user_id; ?></h3>

      <br>
      <p>User Id: <?php echo $user_id; ?></p>
      <p>Uniq Id: <span class="cl-blue"><?php echo $uniq_id; ?></span></p>
      <p>Recharge Amount: ₹<?php echo $recharge_amount; ?></p>
      <p>Recharge Mode: <?php echo $recharge_mode; ?></p>
      <p>Recharge DateTime: <?php echo $request_date_time; ?></p>

      <?php if($request_status=="success"){ ?>
      <p>Status: <label class="pd-5-10 ft-sz-14 br-r-5 cl-white bg-green"><?php echo $request_status; ?></label></p>
      <?php }else if($request_status=="rejected"){ ?>
      <p>Status: <label class="pd-5-10 ft-sz-14 br-r-5 cl-white bg-red"><?php echo $request_status; ?></label></p>
      <?php }else{ ?>
      <p>Status: <label class="cl-orange"><?php echo $request_status; ?></label></p>
      <?php } ?>

      <?php if($recharge_details!=""){ ?>
      <br>
      <p>Recharge Details</p>

      <div class="pd-10-15 mg-t-10 br-r-5 bg-l-blue">
        <?php echo $recharge_details; ?>
      </div>
      <?php } ?>

      <br>
      <?php if($request_status=="success"){ ?>
      <button class="action-btn ft-sz-18 pd-10-15 bg-red" onclick="RejectRequest()">Reject Request</button>
      <?php }else if($request_status=="pending"){ ?>
      <button class="action-btn ft-sz-18 pd-10-15 bg-green" onclick="SucessRequest('approve')">Approve Request</button>
      <button class="action-btn ft-sz-18 pd-10-15 mg-t-10 bg-red" onclick="RejectRequest()">Reject Request</button>
      <?php }else{ ?>
      <button class="action-btn ft-sz-18 pd-10-15 bg-green" onclick="SucessRequest('approve')">Approve Request</button>
      <?php } ?>
      </br>
    </div>

</div>

</div>

<script>
  let recharge_details_view = document.querySelector(".recharge_details_view p");
  
  function RejectRequest(){
    window.open("update-request.php?type=rejected&uniq-id=<?php echo $uniq_id; ?>");
  }

  function SucessRequest(){
    window.open("update-request.php?type=success&uniq-id=<?php echo $uniq_id; ?>");
  }
  
  recharge_details_view.addEventListener("click", ()=>{
    let screenshotURL = recharge_details_view.innerHTML;
    if(screenshotURL.includes("screenshots")){
      window.open(screenshotURL, "_blank");
    }
  })

  
</script>
    
</body>
</html>