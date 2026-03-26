<?php
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

if(!$IS_PRODUCTION_MODE){
  echo "Game is under Demo Mode. So, you can not add or modify.";
  return;
}

if(isset($_GET['title']) && isset($_GET['message'])){
  define("ACCESS_SECURITY","true");
  require_once("../../security/config.php");
  $message_title = $_GET['title'];
  $message_description = $_GET['message'];
  $final_message = $message_title.','.$message_description;

  $update_sql = "UPDATE usersdata SET in_app_message='{$final_message}' ";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>
    <script>
      alert("Notice sended!");
      window.close();
    </script>
<?php } mysqli_close($conn); } ?>