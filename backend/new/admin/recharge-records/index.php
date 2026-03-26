<?php
define("ACCESS_SECURITY", "true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if ($accessObj->validate() == "true") {
    if ($accessObj->isAllowed("access_recharge") == "false") {
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
} else {
    header('location:../logout-account');
}

// Auto-update pending requests older than 12 hours to rejected
$update_sql = "UPDATE tblusersrecharge 
               SET tbl_request_status = 'rejected' 
               WHERE tbl_request_status = 'pending' 
               AND STR_TO_DATE(tbl_time_stamp, '%d-%m-%Y %h:%i:%s %p') <= NOW() - INTERVAL 12 HOUR";

mysqli_query($conn, $update_sql) or die(mysqli_error($conn));


mysqli_query($conn, $update_sql) or die('Failed to update pending status');

$searched = "";
if (isset($_POST['submit'])) {
    $searched = $_POST['searchinp'];
}

$content = 15;
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$offset = ($page_num - 1) * $content;

$newRequestStatus = isset($_POST['order_type']) ? $_POST['order_type'] : "pending";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php"; ?>
  <title><?php echo $APP_NAME; ?>: Recharge Records</title>
  <link href='../style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Recharge Records</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white">
                <i class='bx bx-menu'></i>
            </div>
            <h1 class="mg-l-15">Recharge Records</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="text" name="searchinp" placeholder="Search Id, Amount, Date" class="w-100 cus-inp" />
                <br><br>
                <div class="row-view j-start">
                    <input type="submit" name="submit" value="Search Records" class="action-btn br-r-5 ft-sz-18 pd-10-15">
                    <button class="filter_btn action-btn br-r-5 ft-sz-18 pd-10-15 mg-l-10" type="button">Filter</button>
                    <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportPDF('recharge-records', 'recharge')" type="button">Export PDF</button>
                </div>
                 
                <div class="w-100 pd-15 mg-t-10 bg-l-blue br-r-5 filter_options hide_view">
                  <input type="checkbox" id="success_orders" name="order_type" value="success" <?php if($newRequestStatus == "success") echo "checked"; ?>>
                  <label for="success_orders"> Show success</label><br>
                  
                  <input type="checkbox" id="rejected_orders" class="mg-t-10" name="order_type" value="rejected" <?php if($newRequestStatus == "rejected") echo "checked"; ?>>
                  <label for="rejected_orders"> Show rejected</label><br>
                  
                  <input type="checkbox" id="pending_orders" class="mg-t-10" name="order_type" value="pending" <?php if($newRequestStatus == "pending") echo "checked"; ?>>
                  <label for="pending_orders"> Show pending</label>
                </div>
            </form>
            
            <br><br>
            <p><?php echo ucfirst($newRequestStatus); ?> Records <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <table id="recharge" class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:20%">Id</th>
	  	         <th>Amount</th>
	  	         <th>Date & Time</th>
	  	         <th>Status</th>
                 <th>Recharge Details</th>
                 <th>Recharge Mode</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              
              if ($searched != "") {
                $recharge_records_sql = "SELECT * FROM tblusersrecharge WHERE tbl_request_status='{$newRequestStatus}' AND 
                (tbl_uniq_id LIKE '%$searched%' OR tbl_user_id LIKE '%$searched%' OR tbl_recharge_amount LIKE '%$searched%' OR 
                tbl_recharge_details LIKE '%$searched%' OR tbl_time_stamp LIKE '%$searched%') LIMIT 100";
              } else {
                $recharge_records_sql = "SELECT * FROM tblusersrecharge WHERE tbl_request_status='{$newRequestStatus}' ORDER BY id DESC LIMIT {$offset}, {$content}";
              }

              $recharge_records_result = mysqli_query($conn, $recharge_records_sql);

              if (mysqli_num_rows($recharge_records_result) > 0) {
                while ($row = mysqli_fetch_assoc($recharge_records_result)) {
                 ?>
                 <tr onclick="window.location.href='manager.php?uniq-id=<?php echo $row['tbl_uniq_id']; ?>'">
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_user_id']; ?></td>
	               <td><?php echo $row['tbl_recharge_amount']; ?></td>
	               <td><?php echo $row['tbl_time_stamp']; ?></td>
	               <td class="<?php echo ($row['tbl_request_status'] == 'success' ? 'cl-green' : ($row['tbl_request_status'] == 'rejected' ? 'cl-red' : 'cl-black')); ?>">
                     <?php echo ucfirst($row['tbl_request_status']); ?>
                   </td>
                   <td><?php echo $row['tbl_recharge_details']; ?></td>
                   <td><?php echo $row['tbl_recharge_mode']; ?></td>
	             </tr>
                 <?php 
                 $indexVal++;
                } 
              } else { ?>
              <tr>
	               <td colspan="7">No data found!</td>
	          </tr>
            <?php } ?>
	        </table>
        
           </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=1"></script>
<script>
  document.querySelector(".filter_btn").addEventListener("click", () => {
    document.querySelector(".filter_options").classList.toggle("hide_view");
  });
</script>

</body>
</html>
