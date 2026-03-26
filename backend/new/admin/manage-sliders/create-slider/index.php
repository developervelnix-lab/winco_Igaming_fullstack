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


// update settings btn
if (isset($_POST['submit'])){
  if(!$IS_PRODUCTION_MODE){
    echo "Game is under Demo Mode. So, you can not add or modify.";
    return;
  }
  
  $slider_status = "true";
  $slider_action = $_POST['slider_action'];
  $slider_image_url = $_POST['slider_image_url'];
  
  if($slider_action==""){
    $slider_action = "none";
  }

  $insert_sql = "INSERT INTO tblsliders(tbl_slider_img,tbl_slider_action,tbl_slider_status) VALUES('{$slider_image_url}','{$slider_action}','{$slider_status}')";
  $insert_result = mysqli_query($conn, $insert_sql) or die('query failed');

  if ($insert_result){ ?>

  <script>
    alert('Slider Created!');
    window.history.back();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to create Slider!');
  </script>

<?php } }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php" ?>
  <title>Manage: New Slider</title>
  <link href='../../style.css' rel='stylesheet'>
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
    <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
        <div class="col-view ft-sz-20 mg-l-10">Create Slider</div>
    </div>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
 	  <input type="text" name="slider_action" placeholder="Slider Action URL (Optional)">
 	  
 	  <textarea name="slider_image_url" placeholder="Slider Image URL"></textarea>
      
   	  <input type="submit" name="submit" value="Create Slider" class="w-90 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>
    
</body>
</html>