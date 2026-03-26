<?php
define("ACCESS_SECURITY","true");
include '../../../security/config.php';
include '../../../security/constants.php';
include '../../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_admins")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../../logout-account');
}

// update settings btn
if (isset($_POST['submit'])){
  if(!$IS_PRODUCTION_MODE){
    echo "Game is under Demo Mode. So, you can not add or modify.";
    return;
  }
  
  $auth_user_id = mysqli_real_escape_string($conn, $_POST["signup_mobile"] ?? '');
  $auth_user_password = mysqli_real_escape_string($conn, password_hash($_POST["signup_password"] ?? '', PASSWORD_BCRYPT));
  
  $user_access_list = "";
  
  $access_fields = [
    'access_match', 'access_users_data', 'access_recent_played', 'access_recharge',
    'access_withdraw', 'access_template', 'access_help', 'access_message',
    'access_gift', 'access_settings', 'access_pandl', 'access_admins'
  ];

  foreach ($access_fields as $field) {
    if (isset($_POST[$field]) && $_POST[$field] == 'on') {
      $user_access_list .= $field . ',';
    }
  }
  
  $user_access_list = rtrim($user_access_list, ',');

  function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  $unique_id = generateRandomString();
  $tbl_auth_secret = generateRandomString();

  // current date & time
  date_default_timezone_set('Asia/Kolkata');
  $curr_date_time = date('d-M-Y h:i:s a');

  if($auth_user_id != ""){
    $pre_sql = "SELECT * FROM tbladmins";
    $pre_result = mysqli_query($conn, $pre_sql) or die('error');
    
    if(mysqli_num_rows($pre_result) < $ADMIN_ACCOUNTS_LIMIT){
      $pre_sql = "SELECT * FROM tbladmins WHERE tbl_uniq_id=? OR tbl_user_id=?";
      $stmt = mysqli_prepare($conn, $pre_sql);
      mysqli_stmt_bind_param($stmt, "ss", $unique_id, $auth_user_id);
      mysqli_stmt_execute($stmt);
      $pre_result = mysqli_stmt_get_result($stmt);
    
      if (mysqli_num_rows($pre_result) <= 0){
        $insert_sql = "INSERT INTO tbladmins(tbl_uniq_id, tbl_user_id, tbl_user_password, tbl_user_access_list, tbl_date_time, tbl_auth_secret) 
                       VALUES(?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $unique_id, $auth_user_id, $auth_user_password, $user_access_list, $curr_date_time, $tbl_auth_secret);
        $insert_result = mysqli_stmt_execute($stmt);
        if($insert_result){
          echo "<script>alert('New account Created!');window.history.back();</script>";
        } else {
          echo "Error creating account: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
      } else {
        echo "Entered mobile or uniqid is already registered!";
      }
    } else {
      echo "Maximum number of admin accounts reached!";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: New Admin</title>
  <link href='../../style.css' rel='stylesheet'>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
    <div class="w-100 col-view pd-15 bg-primary">
      <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
          <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
          <div class="col-view ft-sz-20 mg-l-10">Add Admin</div>
      </div>
    </div>
    
  <div class="w-100 col-view v-center">
        
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 direct-p-mg-t bg-white bx-shdw">
 	
 	  <div>
 	    <p>Mobile Number</p>
   	    <input type="text" name="signup_mobile" placeholder="Enter Mobile Number" class="w-100 cus-inp mg-t-10" required>
 	  </div>
 	
 	  <div>
 	    <p>Password</p>
 	    <input type="text" name="signup_password" placeholder="Enter Password" class="w-100 cus-inp mg-t-10" required>
   	  </div>
 	
 	  </br>
 	  <div>
 	   <p>Access List</p>
 	 
 	   <div class="access-checkbox mg-t-10">
 	    <input type="checkbox" name="access_all" id="access_all">
        <label for="access_all">All Access</label>
 	   </div>
 	  	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_users_data" id="access_users_data">
        <label for="access_users_data">Access Users Data</label>
 	   </div>
 	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_recent_played" id="access_recent_played">
        <label for="access_recent_played">Access Recently Played</label>
 	   </div>
 	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_recharge" id="access_recharge">
        <label for="access_recharge">Access Recharge</label>
 	   </div>
 	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_withdraw" id="access_withdraw">
        <label for="access_withdraw">Access Withdraw</label>
 	   </div> 	  	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_help" id="access_help">
        <label for="access_help">Access Help Desk</label>
 	   </div>
 	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_gift" id="access_gift">
        <label for="access_gift">Access GiftCard</label>
 	   </div>
 	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_message" id="access_message">
        <label for="access_message">Access Message</label>
 	   </div>
 	  
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_settings" id="access_settings">
        <label for="access_settings">Access Settings</label>
 	   </div>
 	 
 	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_admins" id="access_admins">
        <label for="access_admins">Access Admins</label>
 	   </div>
 	 
   	   <div class="access-checkbox mg-t-5">
 	    <input type="checkbox" name="access_pandl" id="access_pandl">
        <label for="access_pandl">Access P&L</label>
 	   </div>
     
 	</div>
 	
 	<input type="submit" name="submit" value="Create Account" class="action-btn ft-sz-18 pd-10-15 mg-t-20 bg-green">
 	
 	</br>
  </form>
  
  </div>
  
</div>

<script>
    let access_all = document.querySelector("#access_all");
    let access_checkbox = document.querySelectorAll(".access-checkbox");
    let access_checkbox_input = document.querySelectorAll(".access-checkbox input");
    
    access_all.addEventListener("click", function(){
        if (access_all.checked == true){
          enableDisableAll("enable");
        } else {
          enableDisableAll("disable");
        }
    })
    
    function enableDisableAll(type){
      for (let i = 0; i < access_checkbox_input.length; i++) {
          if(type=="enable"){
            if(i!=0){
              access_checkbox_input[i].checked = true;
            }
          }else{
            if(i!=0){
              access_checkbox_input[i].checked = false;
            }
          }
      }
    }

    for (let i = 0; i < access_checkbox_input.length; i++) {
        access_checkbox_input[i].addEventListener("click", ()=>{
            if(i!=0){
                if (access_checkbox_input[i].checked != true){
                    access_checkbox_input[0].checked = false;
                }
            }
        })
    }
</script>
    
</body>
</html>