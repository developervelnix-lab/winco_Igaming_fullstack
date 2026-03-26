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

if(!isset($_GET['user-id'])){
  echo "request block";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}


date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');
$curr_time = date('h:i:s a');
$curr_date_time = $curr_date.' '.$curr_time;


// update settings btn
if (isset($_POST['submit'])){
  $notice_status = "true";
  $notice_title = $_POST['notice_title'];
  $notice_note = $_POST['notice_note'];
  
  if(!$IS_PRODUCTION_MODE){
    echo "Game is under Demo Mode. So, you can not add or modify.";
    return;
  }

  $insert_sql = "INSERT INTO tblallnotices(tbl_user_id,tbl_notice_title,tbl_notice_note,tbl_notice_status,tbl_time_stamp) VALUES('{$user_id}','{$notice_title}','{$notice_note}','{$notice_status}','{$curr_date_time}')";
  $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');

  if ($insert_result){ ?>

  <script>
    alert('Notice Sent!!');
    window.history.back();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to send Notice!');
  </script>

<?php } }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php" ?>
  <title>Manage: Notices</title>
  <link href='../../style.css' rel='stylesheet'>
<style>
textarea{
    padding: 10px !important;
}
</style>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
  <div class="w-100 col-view pd-15 bg-primary">
    <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view ft-sz-20 mg-l-10">Send Notice</div>
    </div>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="res-w-480 col-view pd-15 br-r-5 bg-white bx-shdw">
        
      <div class="mg-t-10">
 	    <p class="ft-sz-13">Notice Title</p>
 	    <input type="text" name="notice_title" placeholder="Title" class="w-100 cus-inp mg-t-10" required>
 	  </div>
 	  
 	  <div class="mg-t-20">
 	    <p class="ft-sz-13">Notice Description</p>
 	    <textarea name="notice_note" placeholder="Description" class="w-100 h-150-p cus-inp resize-n mg-t-10"></textarea>
 	  </div>
      
   	  <input type="submit" name="submit" value="Send Notice" class="w-100 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>
    
</body>
</html>