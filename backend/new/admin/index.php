<?php
define("ACCESS_SECURITY","true");
include '../security/config.php';
include '../security/constants.php';
 
$host_os = "";
if(isset($_GET['host'])){
  $host_os = mysqli_real_escape_string($conn,$_GET['host']);
}
 
$signin_id = "";
if(isset($_GET['id'])){
  $signin_id = mysqli_real_escape_string($conn,$_GET['id']);
}
 
$signin_password = "";
if(isset($_GET['password'])){
  $signin_password = mysqli_real_escape_string($conn,$_GET['password']);
}
 

session_start();
if (isset($_SESSION["admin_user_id"])) {
  header('location:dashboard');
}

function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

 
/*submit button*/
if (isset($_POST['submit'])){
      $auth_user_id = mysqli_real_escape_string($conn,$_POST['user_id']);
      $auth_user_password = mysqli_real_escape_string($conn,$_POST['password']);
      
      $new_secret_key = generateRandomString();

      $pre_sql = "SELECT * FROM tbladmins WHERE tbl_user_id='$auth_user_id' ";
      $pre_result = mysqli_query($conn, $pre_sql) or die('error');
      $pre_res_data = mysqli_fetch_assoc($pre_result);

      if (mysqli_num_rows($pre_result) > 0){
        $decoded_password = password_verify($auth_user_password,$pre_res_data['tbl_user_password']);
        if($decoded_password == 1){
          $update_sql = "UPDATE tbladmins SET tbl_auth_secret ='{$new_secret_key}' WHERE tbl_user_id='{$auth_user_id}' ";
          $update_query = mysqli_query($conn, $update_sql) or die('error');
          
          if($host_os=="android"){ ?>
           <script>
              Handle.saveLogin(<?php echo $auth_user_id; ?>,<?php echo $auth_user_password; ?>);
           </script>
          <?php }
            $_SESSION["admin_user_id"] = $auth_user_id;
            $_SESSION["admin_secret_key"] = $new_secret_key;
            $_SESSION["admin_access_list"] = $pre_res_data['tbl_user_access_list'];
            header('location:dashboard');
          }else{ ?>
            <script>
              alert('id & password not matched');
            </script>
          <?php } }else{ ?>
          <script>
            alert('No account exit with this ID!');
          </script>
<?php } } ?>

<!DOCTYPE html>
<html>
<head>
  <?php include "header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Admin Panel</title>
  <link href='style.css' rel='stylesheet'>
    
<style>
form{
  width: 400px;
  padding: 15px 10px;
  border-radius: 5px;
  border: 1px solid rgba(0,0,0,0.09);
}

@media (max-width: 500px) {
  form{
    width: 95%;
  }
}

</style>

</head>
<body>

 <div class="row-view w-100 mh-100vh dotted-back">
 	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="v-center flx-d-col bg-white bx-shdw">
 		<div class="col-view v-center">
 		    <div class="v-center ft-sz-25">
 		      <i class='bx bxs-bolt-circle'></i>
 		      <span class="mg-l-5">Login</span>
 		    </div>
 		    
 		     <span class="ft-sz-13 mg-t-5">100% Safe & Secured</span>
 		</div>
 		
 		<div class="w-90 mg-t-50">
 	      <span class="ft-sz-15">Account ID</span>
 		  <input type="text" name="user_id" class="cus-inp w-100 mg-t-5" required>
 		</div>
 		
 		<div class="w-90 mg-t-20">
 	      <span class="ft-sz-15">Account Password</span>
 		  <input type="password" name="password" autocomplete="off" class="cus-inp w-100 mg-t-5" required>
 		</div>
 		<button type="submit" name="submit" class="w-90 action-btn br-r-5 ft-sz-20 mg-t-30">Log in &nbsp;<i class='bx bx-right-arrow-alt'></i></button>
 		<p class="mg-t-10 ft-sz-12">Incase you forgot ID & Password then contact the developer. @stondev</p>
 	</form>
 </div>

</body>
</html>