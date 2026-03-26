<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../../security/config.php';
include '../../../security/constants.php';
include '../../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_users_data")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../../logout-account');
}

if(!isset($_GET['user-id'])){
  echo "request block";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php" ?>
  <title>Manage: All Notices</title>
  <link href='../../style.css' rel='stylesheet'>
</head>

<body>
    
<div class="mh-100vh w-100 col-view dotted-back">
    
    <div class="w-100 col-view pd-15 bg-primary">
      <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
          <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
          <div class="col-view ft-sz-20 mg-l-10">All Notices</div>
      </div>
    </div>
    
    <div class="w-100 col-view v-center">
       
    <div class="w-90 pd-10 mg-t-30 bg-white bx-shdw br-r-5">
    
      <a class="cur-pointer pd-10-15 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a>
      <a href="send-notice.php?user-id=<?php echo $user_id; ?>" class="txt-deco-n cur-pointer pd-10-15 mg-l-5 cl-white br-r-5 bg-primary"><i class='bx bx-message-square-dots'></i>&nbsp;Send Notice</a>
    
      <div class="w-100 ovflw-x-scroll mg-t-20">
      <table class="cus-tbl mg-t-10 bg-white">
	    <tr>
	  	  <th>Notice Title</th>
		  <th>Date & Time</th>
	    </tr>

        <?php
        $sql = "SELECT * FROM tblallnotices WHERE tbl_user_id='{$user_id}'";
        $result = mysqli_query($conn, $sql) or die('search failed');
      
        if (mysqli_num_rows($result) > 0){
          while ($row = mysqli_fetch_assoc($result)){ ?>
           <tr <?php if($row['tbl_notice_status']!="true"){ ?>class="approved_order"<?php } ?>>
             <td><?php echo $row['tbl_notice_title']; ?></td>
             <td><?php echo $row['tbl_time_stamp']; ?></td>
	  	   </tr>
          <?php } }else{ ?>
          <tr>
		    <td colspan="2">No Data Found!</td>
		  </tr>
        <?php } ?>

	  </table>
	  </div>
	
    </div>
    
    </div>

</div>

</body>
</html>