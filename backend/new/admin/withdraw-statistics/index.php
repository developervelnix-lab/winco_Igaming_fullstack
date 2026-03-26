<?php

define("ACCESS_SECURITY", "true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if ($accessObj->validate() == "true") {
    if ($accessObj->isAllowed("access_withdraw") == "false") {
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
} else {
    header('location:../logout-account');
    exit;
}

// Calculate total withdraw amounts for each status
$statuses = ['success', 'rejected', 'pending', 'approve'];
$total_withdraws = [];

foreach ($statuses as $status) {
    $total_withdraw_sql = "SELECT SUM(tbl_withdraw_amount) as total FROM tbluserswithdraw WHERE tbl_request_status='$status'";
    $total_withdraw_result = mysqli_query($conn, $total_withdraw_sql);
    $total_withdraws[$status] = mysqli_fetch_assoc($total_withdraw_result)['total'] ?? 0;
}

$total_all = array_sum($total_withdraws);

// Get the name of the timestamp column
$table_info_sql = "SHOW COLUMNS FROM tbluserswithdraw";
$table_info_result = mysqli_query($conn, $table_info_sql);
$timestamp_column = null;
while ($row = mysqli_fetch_assoc($table_info_result)) {
    if (strpos(strtolower($row['Type']), 'timestamp') !== false) {
        $timestamp_column = $row['Field'];
        break;
    }
}

if (!$timestamp_column) {
    $timestamp_column = 'id'; // Fallback to 'id' if no timestamp column is found
}

// Get monthly data for the chart
$monthly_data_sql = "SELECT 
    DATE_FORMAT($timestamp_column, '%Y-%m') as month,
    SUM(CASE WHEN tbl_request_status = 'success' THEN tbl_withdraw_amount ELSE 0 END) as success_total,
    SUM(CASE WHEN tbl_request_status = 'rejected' THEN tbl_withdraw_amount ELSE 0 END) as rejected_total,
    SUM(CASE WHEN tbl_request_status = 'pending' THEN tbl_withdraw_amount ELSE 0 END) as pending_total,
    SUM(CASE WHEN tbl_request_status = 'approve' THEN tbl_withdraw_amount ELSE 0 END) as approved_total
FROM tbluserswithdraw
GROUP BY DATE_FORMAT($timestamp_column, '%Y-%m')
ORDER BY month DESC
LIMIT 12";

$monthly_data_result = mysqli_query($conn, $monthly_data_sql);
if (!$monthly_data_result) {
    die("Error in SQL query: " . mysqli_error($conn));
}
$monthly_data = [];
while ($row = mysqli_fetch_assoc($monthly_data_result)) {
    $monthly_data[] = $row;
}
$monthly_data = array_reverse($monthly_data);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php"; ?>
  <title><?php echo $APP_NAME; ?>: Withdraw Statistics</title>
  <link href='../style.css' rel='stylesheet'>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        <?php include "../components/side-menu.php"; ?>
        
        <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
            <p>Dashboard > Withdraw Statistics</p>
            
            <div class="w-100 row-view j-start mg-t-20">
                <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white">
                    <i class='bx bx-menu'></i>
                </div>
                <h1 class="mg-l-15">Withdraw Statistics</h1>
            </div>
            
            <div class="pd-10 mg-t-30 bx-shdw br-r-5">
                <div class="w-100 pd-15 mg-b-20 bg-l-blue br-r-5">
                    <h3>Total Withdrawals</h3>
                    <div class="row-view j-between mg-t-10">
                        <div><br><br>All: <?php echo number_format($total_all, 2); ?></div> 
                        <br>
                        <div><br><br><br><br><br>Success: <?php echo number_format($total_withdraws['success'], 2); ?></div>
                        <div><br><br><br><br><br><br><br><br><br>Rejected: <?php echo number_format($total_withdraws['rejected'], 2); ?></div>
                        <div><br><br><br><br><br><br><br><br><br><br><br><br><br><br>Pending: <?php echo number_format($total_withdraws['pending'], 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../script.js?v=1"></script>
</body>
</html>