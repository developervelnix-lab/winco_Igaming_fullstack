<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_settings")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

if(!isset($_GET['id'])){
  echo "invalid request";
  return;
}else{
  $service_name = mysqli_real_escape_string($conn,$_GET['id']);
}


function getNumberFormat($number = 0, $decimalPoint=2){
    $multiplier = pow(10, $decimalPoint);
          
    // Truncate without rounding
    $truncated = floor($number * $multiplier) / $multiplier;
    return number_format($truncated, $decimalPoint, '.', '');
}

// update settings btn
if (isset($_POST['submit'])){
  if(!$IS_PRODUCTION_MODE){
    echo "Game is under Demo Mode. So, you can not add or modify.";
    return;
  }
  
  $service_value = mysqli_real_escape_string($conn,$_POST['service_value']);
  // Remove extra charcters
  if($service_name!="IMP_MESSAGE"){
    // Remove extra charcters
    $service_value = str_replace(' ', '', $service_value);
    $service_value = str_replace('%', '', $service_value); 
  }

  if($service_name=="WITHDRAW_TAX"){
    $service_value = getNumberFormat(($service_value/100), 2);
  }

  $update_sql = "UPDATE tblservices SET tbl_service_value='{$service_value}' WHERE tbl_service_name='{$service_name}'";
  $update_result = mysqli_query($conn, $update_sql) or die('error');
  if ($update_result){ ?>

  <script>
    alert('Settings updated!');
    window.history.back();
  </script>

<?php }else{ ?>
  
  <script>
    alert('Failed to update setting!');
  </script>

<?php } }

$service_description = "";
$service_on_off = false;

$select_sql = "SELECT * FROM tblservices WHERE tbl_service_name='{$service_name}' ";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $service_name = $select_res_data['tbl_service_name'];
  $service_value = $select_res_data['tbl_service_value'];
  
  if($service_name=="APP_STATUS"){
    $service_on_off = true;
    $service_description = "Update App Status";
  }else if($service_name=="GAME_STATUS"){
    $service_on_off = true;
    $service_description = "Update Game Status";
  }else if($service_name=="DEPOSIT_BONUS"){
    $service_on_off = true;
    $service_description = "Update Deposit Bonus";
  }else if($service_name=="DEPOSIT_BONUS_OPTIONS"){
    $service_description = "Add values with comma separated.";      
  }else if($service_name=="OTP_ALLOWED"){
    $service_on_off = true;
    $service_description = "Update OTP Allowed";
  }else if($service_name=="SIGNUP_ALLOWED"){
    $service_on_off = true;
    $service_description = "Update SignUp (New Account) Allowed";
  }else if($service_name=="COMISSION_BONUS"){
    $service_description = "Comma separated (Level 1, Level 2, Level 3) or One value for all"; 
  }else if($service_name=="SALLARY_PERCENT"){
    $service_description = "Comma separated (Level 1, Level 2, Level 3) or One value for all";
  }else if($service_name=="RECHARGE_OPTIONS"){
    $service_description = "Add values with comma separated.";  
  }else if($service_name=="WITHDRAW_TAX"){
    $service_value = $service_value*100;
    $service_description = "Tax in percentage";
  }else if($service_name=="SMS_TOKEN"){
    $service_description = "Support: DV HOSTING API";
  }
}else{
  echo 'Invalid Service-id!';
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
  width: 100%;
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
          
        <div class="col-view ft-sz-20 mg-l-10">Manage Settings</div>
    </div>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
 	  <input type="text" name="service_name" placeholder="Service Name" value="<?php echo $service_name; ?>" required disabled>

 	  <div class="w-90 pd-10-15 mg-t-15 br-r-5 bx-shdw">
 	    <span class="ft-sz-13"><?php echo $service_description; ?></span>
 	    
 	    <?php if($service_on_off){ ?>
 	      <select class="w-100 mg-t-10" name="service_value">
 	        <option value="true" <?php if($service_value=="true"){ ?>selected<?php } ?>>ON</option>
 	        <option value="false" <?php if($service_value=="false"){ ?>selected<?php } ?>>OFF</option>
 	      </select>
        
 	    <?php }else{ ?>
 	      <textarea name="service_value" placeholder="Service Value" class="mg-t-10"><?php echo $service_value; ?></textarea>
 	    <?php } ?>
 	  </div>
      
   	  <input type="submit" name="submit" value="Update Setting" class="w-90 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>

<script>
function handleSelectChange(event) {
    let auto_payment_view = document.querySelector(".auto_payment_view");
    let manual_payment_view = document.querySelector(".manual_payment_view");
    
    var selectElement = event.target;
    var value = selectElement.value;
    
    if(value=="manual"){
        auto_payment_view.classList.add("hide_view");
    }else{
        auto_payment_view.classList.remove("hide_view");
    }
}
</script>
    
</body>
</html>