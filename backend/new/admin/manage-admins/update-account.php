<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_admins")=="false"){
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
  $user_uniq_id = mysqli_real_escape_string($conn,$_GET['uniq-id']);
}

// update settings btn
if (isset($_POST['submit'])){
  if(!$IS_PRODUCTION_MODE){
    echo "Game is under Demo Mode. So, you can not add or modify.";
    return;
  }

  $auth_user_password = mysqli_real_escape_string($conn,password_hash($_POST["account_password"],PASSWORD_BCRYPT));
  
  $update_sql = "UPDATE tbladmins SET tbl_user_password='{$auth_user_password}' WHERE tbl_uniq_id='{$user_uniq_id}'";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>

  <script>
    alert('Password updated!');
    window.history.back();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to update account!');
  </script>

<?php } }

$select_sql = "SELECT * FROM tbladmins WHERE tbl_uniq_id='$user_uniq_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_mobile_num = $select_res_data['tbl_user_id'];
}else{
  echo 'Invalid Id!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Update Account</title>
<link href='../style.css' rel='stylesheet'>
<style>
form{
  width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 15px 0;
  border-radius: 5px;
  background: #ffffff;
  box-shadow: 0.1px 2px 8px 4px rgba(0, 0, 0, 0.05);
}

input[type="text"],select{
   width: 90%;
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
    <h3 class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view mg-l-10">Update Account</div>
    </h3>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
 	  <input type="text" name="account_mobile" placeholder="Mobile Number" value="<?php echo $user_mobile_num; ?>" required disabled>
 	  
 	  <input type="text" name="account_password" placeholder="New Password" value="" required>
      
   	  <input type="submit" name="submit" value="Update Password" class="w-90 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>
    
</body>
</html>