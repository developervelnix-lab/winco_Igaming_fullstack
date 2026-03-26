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
    if($accessObj->isAllowed("access_admins")=="false"){
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
  <title><?php echo $APP_NAME; ?>: Admin</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Admins</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Admins</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            
            <p><a class="cur-pointer pd-5-10 cl-white br-r-5 bg-green" onclick="window.location.href='add-admin'"><i class='bx bx-plus'></i>&nbsp;Add Admin</a><a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <table class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">Id</th>
	  	         <th>DateTime</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              
              $settings_records_sql = "SELECT * FROM tbladmins ORDER BY id DESC";
      
              $settings_records_result = mysqli_query($conn, $settings_records_sql) or die('search failed');
          
              if (mysqli_num_rows($settings_records_result) > 0){
                $paginationAvailable = true;
                
                while ($row = mysqli_fetch_assoc($settings_records_result)){
                    $request_uniq_id = $row['tbl_user_id'];
    
                ?>
                 <tr onclick="window.location.href='manager.php?user-id=<?php echo $request_uniq_id; ?>'">
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_user_id']; ?></td>
	               <td><?php echo $row['tbl_date_time']; ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	               <td colspan="2">No data found!</td>
	               <td></td>
	          </tr>
            <?php } ?>
	        </table>
        
           </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=1"></script>

</body>
</html>