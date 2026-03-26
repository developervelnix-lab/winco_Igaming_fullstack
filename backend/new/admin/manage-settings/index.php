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
    if($accessObj->isAllowed("access_settings")=="false"){
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
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Settings</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Settings</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">All Settings</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            
            <p>All Settings <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <div class="w-100 ovflw-x-scroll">
            <table class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">Name</th>
	  	         <th>Value</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              
              $settings_records_sql = "SELECT * FROM tblservices ORDER BY id DESC";
      
              $settings_records_result = mysqli_query($conn, $settings_records_sql) or die('search failed');
          
              if (mysqli_num_rows($settings_records_result) > 0){
                $paginationAvailable = true;
                
                while ($row = mysqli_fetch_assoc($settings_records_result)){
                    $setting_id = $row['tbl_service_name'];
                ?>
                 <tr onclick="window.location.href='manager.php?id=<?php echo $setting_id; ?>'">
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_service_name']; ?></td>
	               <td>
	               <?php
	                 if(strlen($row['tbl_service_value'])>30){
	                   echo substr($row['tbl_service_value'],0,30).'...'; 
	                 }else{ 
	                   if($row['tbl_service_value']=="true"){ 
	                     echo "ON";
	                   }else if($row['tbl_service_value']=="false"){ 
	                     echo "OFF";
	                   }else{ 
	                     echo $row['tbl_service_value']; 
	                   }
	                 } ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	            <td colspan="3">No data found!</td>
	          </tr>
            <?php } ?>
	        </table>
	        </div>
        
        </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=1"></script>
<script>
  document.querySelector(".filter_btn").addEventListener("click", ()=>{
    document.querySelector(".filter_options").classList.toggle("hide_view")
  });

  var filterOp = document.querySelector(".filter_options");
    var option = filterOp.getElementsByTagName("input");
    for (var i = 0; i < option.length; i++) {
      option[i].onclick = function () {
        for (var i = 0; i < option.length; i++) {
          if (option[i] != this && this.checked) {
            option[i].checked = false;
          }
        }
      };
    }
</script>

</body>
</html>