<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_message")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../components/header.php"; ?>
  <title>Admin: Send Message</title>
  <link href='../style.css' rel='stylesheet'>
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
        <div class="col-view ft-sz-20 mg-l-10">Send Messages</div>
      </div>
   </div>
   
   <div class="w-100 col-view v-center">
       
    <div class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 bg-white bx-shdw">
      <div class="mg-t-20">
 	    <p class="ft-sz-13">Message Title</p>
        <input type="text" name="message_title" placeholder ="Title" class="w-100 cus-inp mg-t-10" id="message_title" required>
      </div>
      
      <div class="mg-t-20">
 	    <p class="ft-sz-13">Message Description</p>
        <textarea name="message_description" placeholder="Description" class="w-100 h-150-p cus-inp resize-n mg-t-10" id="message_description" required></textarea>
      </div>
    
      <button class="w-100 ft-sz-18 action-btn mg-t-30" onclick="SendMessage()">Send new message</button>  
      <button class="w-100 ft-sz-18 action-btn mg-t-10" style="background: #28B463;" onclick="SendNotice()">Send Notice&nbsp<i class='bx bx-send'></i></button>

    </div>
  
  </div>

</div>

<script src="../script.js?v=1"></script>
<script>
  let in_msg_title,in_msg_desc;
  let msg_title = document.querySelector("#message_title");
  let msg_description = document.querySelector("#message_description");

  function SendMessage(){
    in_msg_title = msg_title.value;
    in_msg_desc = msg_description.value;

    if(in_msg_title!="" && in_msg_desc!=""){
      window.open("manage-message.php?title="+in_msg_title+"&message="+in_msg_desc);
    }else{
      alert("Invalid data!");
    }
  }
  
  function SendNotice(){
    in_msg_title = msg_title.value;
    in_msg_desc = msg_description.value;

    if(in_msg_title!="" && in_msg_desc!=""){
      window.open("manage-notice.php?title="+in_msg_title+"&message="+in_msg_desc);
    }else{
      alert("Invalid data!");
    }
  }
</script>
    
</body>
</html>