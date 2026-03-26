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

if(!isset($_GET['user-id'])){
  echo "invalid request";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}


$select_sql = "SELECT * FROM tbladmins WHERE tbl_user_id='$user_id' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $uniq_id = $select_res_data['tbl_uniq_id'];
  $user_joined = $select_res_data['tbl_date_time'];
  
}else{
  echo 'Invalid user-id!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "../../components/header.php"; ?>
<title>Manage: Game Settings</title>
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
          
        <div class="col-view mg-l-10">Manage Settings</div>
    </h3>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <div class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 bg-white bx-shdw">
      <p class="mg-t-5">Account Id: <?php echo $user_id; ?></p>
      <p class="mg-t-5">Account Uniq Id: <?php echo $uniq_id; ?></p>
      <p class="mg-t-5">Account Created: <?php echo $user_joined; ?></p>
      
      <a href="update-account.php?uniq-id=<?php echo $uniq_id; ?>" class="w-100 ft-sz-18 action-btn mg-t-15">Change Password</a>  
      <button class="w-100 ft-sz-18 action-btn bg-red mg-t-10" onclick="removeAccount('<?php echo $uniq_id; ?>')">Remove Account</button>
    </div>
  </div>
  
</div>

<script>
  function removeAccount(admin_uniq_id){
    if(confirm("Are you sure you want to remove this account?")){
        window.open("remove-account.php?uniq-id="+admin_uniq_id);
    }
  }
</script>
    
</body>
</html>