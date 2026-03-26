<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_pandl")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../index.php');
}

date_default_timezone_set('Asia/Kolkata');
$curr_date_time = date('d-m-Y');
 
 $searched="";
 if (isset($_POST['submit'])){
   $searched = $_POST['searched'];
 }

 if (isset($_POST['reset'])){
   $searched = $curr_date_time;
 }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Game Statistics & P&L</title>
    <link href='../style.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
<style><?php include "../components/theme-variables.php"; ?></style>
<style>
/* Page specific variable overrides only if needed */
        body {
            font-family: var(--font-body) !important;
            background-color: var(--page-bg) !important;
            min-height: 100vh; color: var(--text-main); margin: 0; padding: 0; overflow: hidden;
        }

        .main-panel {
            flex-grow: 1; height: 100vh; border-radius: 16px; border: 1px solid var(--border-dim);
            background: var(--panel-bg); box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            padding: 20px; overflow-y: auto;
        }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; padding: 10px 0; border-bottom: 1px solid var(--border-dim);
        }
        .dash-title h1 { font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0; }
        .dash-breadcrumb { font-size: 11px; font-weight: 700; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 1px; }

        /* Search Section */
        .search-area {
            background: var(--input-bg); border: 1px solid var(--border-dim);
            border-radius: 12px; padding: 16px; margin-bottom: 24px;
        }
        .cus-inp {
            height: 40px; background: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important; border-radius: 8px !important;
            padding: 0 12px !important; color: var(--text-main) !important; font-size: 14px !important;
        }
        .cus-inp::placeholder {
            color: var(--text-dim) !important; opacity: 0.6;
        }
        .btn-modern {
            height: 40px; padding: 0 20px; border-radius: 8px; font-weight: 700; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            cursor: pointer; border: none; text-decoration: none;
        }
        .btn-primary-modern { background: var(--accent-blue); color: #fff; }
        .btn-primary-modern:hover { background: #2563eb; transform: translateY(-2px); }
        .btn-outline-modern { background: var(--input-bg); border: 1px solid var(--border-dim); color: var(--text-main); }
        .btn-outline-modern:hover { background: var(--table-row-hover); }
        .btn-success-modern { background: var(--accent-emerald); color: #fff; }

        /* Grid Layout */
        .analytics-section { margin-bottom: 40px; }
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1.5px; color: var(--text-dim); display: flex; align-items: center; gap: 10px;
            flex-grow: 1;
        }
        .section-title::after { content: ''; flex-grow: 1; height: 1px; background: var(--border-dim); margin-left: 10px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        
        .stat-card {
            background: var(--input-bg); border-radius: 16px; border: 1px solid var(--border-dim);
            padding: 20px; position: relative; overflow: hidden; transition: all 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); border-color: rgba(59, 130, 246, 0.3); }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 3px; height: 100%; background: var(--card-color, var(--accent-blue));
        }

        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.04);
            display: flex; align-items: center; justify-content: center; font-size: 20px;
            color: var(--card-color, var(--accent-blue)); margin-bottom: 16px;
        }
        .stat-label { font-size: 11px; font-weight: 700; color: var(--text-dim); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 18px; font-weight: 800; color: var(--text-main); }
        
        .status-badge {
            font-size: 9px; font-weight: 900; text-transform: uppercase;
            padding: 4px 10px; border-radius: 100px; margin-top: 10px; display: inline-block;
            letter-spacing: 1px;
        }

        .badge-profit { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); }
        .badge-loss { background: rgba(244, 63, 94, 0.15); color: var(--accent-rose); border: 1px solid rgba(244, 63, 94, 0.2); }
    </style>
</head>
<body class="bg-light">
<div class="admin-layout-wrapper">
    <?php include "../components/side-menu.php"; ?>
    <div class="admin-main-content hide-native-scrollbar">
        <div class="dash-header">
            <div class="dash-title">
                <span class="dash-breadcrumb">Profit & Loss Hub</span>
                <h1>Game Statistics</h1>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-modern btn-outline-modern" onclick="window.location.reload()"><i class='bx bx-refresh'></i> Refresh</button>
            </div>
        </div>

        <div class="search-area">
            <h5 class="mg-b-15" style="font-size: 14px; font-weight: 700; color: var(--text-main);"><i class='bx bx-grid-alt'></i> View In Detail</h5>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="searched" value="<?php echo htmlspecialchars($searched); ?>" placeholder="Search Date Records (<?php echo $curr_date_time; ?>)" id="in_search_bar" class="form-control cus-inp">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" name="submit" class="btn-modern btn-primary-modern flex-grow-1">
                         Search Records
                    </button>
                    <button type="submit" name="reset" class="btn-modern btn-success-modern px-3">
                        Today Report
                    </button>
                </div>
            </form>
        </div>

        <?php
        // ORIGINAL LOGIC PRESERVATION
        $anlyt_total_users = 0;
        $anlyt_total_recharge = 0;
        $anlyt_total_withdraw = 0;
        $anlyt_total_balance = 0;
        $anlyt_final_result = 0;
        $anlyt_today_active = 0;
        $anlyt_number_withdraw = 0;
        $anlyt_number_recharge = 0;
        
        $analytic_sql = "SELECT tbl_uniq_id,tbl_user_joined,tbl_balance, tbl_last_active_date FROM tblusersdata";
        $analytic_result = mysqli_query($conn, $analytic_sql) or die('Query failed');

        if (mysqli_num_rows($analytic_result) > 0) {  
            $anlyt_total_users = mysqli_num_rows($analytic_result);           
            while ($row = mysqli_fetch_assoc($analytic_result)) {
                $date = date('d-m-Y', strtotime($row['tbl_user_joined']));
                if ($searched == $row['tbl_last_active_date']) $anlyt_today_active++;
                if ($searched == $date) {
                    if (is_numeric($row['tbl_balance'])) $anlyt_total_balance += $row['tbl_balance'];        
                }      
            }
        }

        $search_recharge_sql = "SELECT tbl_recharge_amount, tbl_time_stamp FROM tblusersrecharge 
                                WHERE tbl_request_status='success' 
                                AND (tbl_recharge_mode='ZEEPay' OR tbl_recharge_mode='UTRPay' OR tbl_recharge_mode='QRPay')";
        $search_recharge_result = mysqli_query($conn, $search_recharge_sql) or die('Recharge Query failed');
        while ($row = mysqli_fetch_assoc($search_recharge_result)) {
            $transaction_date = date('d-m-Y', strtotime($row['tbl_time_stamp']));
            if ($searched == $transaction_date) {
                $anlyt_number_recharge++;
                $anlyt_total_recharge += $row['tbl_recharge_amount'];
            }
        }

        $search_withdraw_sql = "SELECT tbl_withdraw_amount, tbl_time_stamp FROM tbluserswithdraw 
                                WHERE tbl_request_status='success'";
        $search_withdraw_result = mysqli_query($conn, $search_withdraw_sql) or die('Withdraw Query failed');
        while ($row = mysqli_fetch_assoc($search_withdraw_result)) {
            $transaction_date = date('d-m-Y', strtotime($row['tbl_time_stamp']));
            if ($searched == $transaction_date) {
                $anlyt_number_withdraw++;
                $anlyt_total_withdraw += $row['tbl_withdraw_amount'];
            }
        }

        $anlyt_p_and_l = $anlyt_total_recharge - $anlyt_total_withdraw;
        $anlyt_final_result = $anlyt_total_recharge - ($anlyt_total_withdraw + $anlyt_total_balance);
        ?>

        <div class="analytics-section">
            <div class="section-header">
                <div class="section-title">Transaction Details: <?php echo $searched; ?></div>
            </div>
            <div class="stats-grid">
                <div class="stat-card" style="--card-color: var(--accent-indigo)">
                    <div class="stat-icon"><i class='bx bx-briefcase'></i></div>
                    <div class="stat-label">Balance</div>
                    <div class="stat-value">₹<?php echo number_format($anlyt_total_balance, 2); ?></div>
                </div>
                <div class="stat-card" style="--card-color: var(--accent-emerald)">
                    <div class="stat-icon"><i class='bx bx-plus-circle'></i></div>
                    <div class="stat-label">Recharge</div>
                    <div class="stat-value">₹<?php echo number_format($anlyt_total_recharge, 2); ?></div>
                </div>
                <div class="stat-card" style="--card-color: var(--accent-rose)">
                    <div class="stat-icon"><i class='bx bx-minus-circle'></i></div>
                    <div class="stat-label">Withdraw</div>
                    <div class="stat-value">₹<?php echo number_format($anlyt_total_withdraw, 2); ?></div>
                </div>
                <div class="stat-card" style="--card-color: <?php echo ($anlyt_p_and_l < 0) ? 'var(--accent-rose)' : 'var(--accent-emerald)'; ?>">
                    <div class="stat-icon"><i class='bx bx-stats'></i></div>
                    <div class="stat-label">P & L</div>
                    <div class="stat-value">₹<?php echo number_format($anlyt_p_and_l, 2); ?></div>
                    <?php if($anlyt_p_and_l < 0): ?>
                        <span class="status-badge badge-loss">Loss</span>
                    <?php elseif($anlyt_p_and_l > 1): ?>
                        <span class="status-badge badge-profit">Profit</span>
                    <?php endif; ?>
                </div>
                <div class="stat-card" style="--card-color: <?php echo ($anlyt_final_result < 0) ? 'var(--accent-rose)' : 'var(--accent-emerald)'; ?>">
                    <div class="stat-icon"><i class='bx bx-pie-chart-alt'></i></div>
                    <div class="stat-label">In Total</div>
                    <div class="stat-value">₹<?php echo number_format($anlyt_final_result, 2); ?></div>
                    <?php if($anlyt_final_result < 0): ?>
                        <span class="status-badge badge-loss">Loss</span>
                    <?php elseif($anlyt_final_result > 1): ?>
                        <span class="status-badge badge-profit">Profit</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="analytics-section">
            <div class="section-header">
                <div class="section-title">Users Details: <?php echo $searched; ?></div>
            </div>
            <div class="stats-grid">
                <div class="stat-card" style="--card-color: var(--accent-blue)">
                    <div class="stat-icon"><i class='bx bx-group'></i></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo number_format($anlyt_total_users); ?></div>
                </div>
                <div class="stat-card" style="--card-color: var(--accent-emerald)">
                    <div class="stat-icon"><i class='bx bx-transfer-alt'></i></div>
                    <div class="stat-label">No. Recharge</div>
                    <div class="stat-value"><?php echo number_format($anlyt_number_recharge); ?></div>
                </div>
                <div class="stat-card" style="--card-color: var(--accent-rose)">
                    <div class="stat-icon"><i class='bx bx-wallet'></i></div>
                    <div class="stat-label">No. Withdraw</div>
                    <div class="stat-value"><?php echo number_format($anlyt_number_withdraw); ?></div>
                </div>
                <div class="stat-card" style="--card-color: var(--accent-indigo)">
                    <div class="stat-icon"><i class='bx bx-user-check'></i></div>
                    <div class="stat-label">Today Active</div>
                    <div class="stat-value"><?php echo number_format($anlyt_today_active); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../script.js?v=2"></script>
</body>
</html>