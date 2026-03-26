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

// Get data for Recharge vs Withdrawal chart (last 7 days)
$recharge_data = [];
$withdraw_data = [];
$date_labels = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_labels[] = date('d M', strtotime("-$i days"));
    
    // Format date for SQL query
    $start_date = $date . " 00:00:00";
    $end_date = $date . " 23:59:59";
    
    // Get recharge data
    $recharge_sql = "SELECT SUM(tbl_recharge_amount) as total_recharge 
                    FROM tblusersrecharge 
                    WHERE tbl_request_status = 'success' 
                    AND STR_TO_DATE(tbl_time_stamp, '%d-%m-%Y %h:%i:%s %p') 
                    BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d %H:%i:%s') 
                    AND STR_TO_DATE('$end_date', '%Y-%m-%d %H:%i:%s')";
    
    $recharge_result = mysqli_query($conn, $recharge_sql);
    $recharge_row = mysqli_fetch_assoc($recharge_result);
    $recharge_data[] = $recharge_row['total_recharge'] ? (float)$recharge_row['total_recharge'] : 0;
    
    // Get withdrawal data
    $withdraw_sql = "SELECT SUM(tbl_withdraw_amount) as total_withdraw 
                    FROM tbluserswithdraw 
                    WHERE tbl_request_status = 'success' 
                    AND STR_TO_DATE(tbl_time_stamp, '%d-%m-%Y %h:%i:%s %p') 
                    BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d %H:%i:%s') 
                    AND STR_TO_DATE('$end_date', '%Y-%m-%d %H:%i:%s')";
    
    $withdraw_result = mysqli_query($conn, $withdraw_sql);
    $withdraw_row = mysqli_fetch_assoc($withdraw_result);
    $withdraw_data[] = $withdraw_row['total_withdraw'] ? (float)$withdraw_row['total_withdraw'] : 0;
}

// Get data for Profit/Loss Analysis
$profit_data = [];
$loss_data = [];
$cost_data = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    
    // Format date for SQL query
    $start_date = $date . " 00:00:00";
    $end_date = $date . " 23:59:59";
    
    $profit_sql = "SELECT 
                    SUM(CASE WHEN tbl_match_status = 'profit' THEN tbl_match_profit ELSE 0 END) as total_profit,
                    SUM(CASE WHEN tbl_match_status = 'loss' THEN tbl_match_profit ELSE 0 END) as total_loss,
                    SUM(tbl_match_cost) as total_cost
                  FROM tblmatchplayed 
                  WHERE STR_TO_DATE(tbl_time_stamp, '%d-%m-%Y %h:%i %p') 
                  BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d %H:%i:%s') 
                  AND STR_TO_DATE('$end_date', '%Y-%m-%d %H:%i:%s')";
    
    $profit_result = mysqli_query($conn, $profit_sql);
    $profit_row = mysqli_fetch_assoc($profit_result);
    
    $profit_data[] = $profit_row['total_profit'] ? (float)$profit_row['total_profit'] : 0;
    $loss_data[] = $profit_row['total_loss'] ? (float)$profit_row['total_loss'] : 0;
    $cost_data[] = $profit_row['total_cost'] ? (float)$profit_row['total_cost'] : 0;
}

// Fetch users with deposits (last 7 days)
$deposit_sql = "SELECT u.tbl_full_name, r.tbl_recharge_amount, r.tbl_time_stamp
                FROM tblusersrecharge r
                JOIN tblusersdata u ON r.tbl_uniq_id = u.tbl_uniq_id
                WHERE r.tbl_request_status = 'success' 
                AND STR_TO_DATE(r.tbl_time_stamp, '%d-%m-%Y %h:%i:%s %p') >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY r.tbl_recharge_amount DESC";

$deposit_result = mysqli_query($conn, $deposit_sql);

// Fetch users with withdrawals (last 7 days)
$withdraw_sql = "SELECT u.tbl_full_name, w.tbl_withdraw_amount, w.tbl_time_stamp
                 FROM tbluserswithdraw w
                 JOIN tblusersdata u ON w.tbl_uniq_id = u.tbl_uniq_id
                 WHERE w.tbl_request_status = 'success' 
                 AND STR_TO_DATE(w.tbl_time_stamp, '%d-%m-%Y %h:%i:%s %p') >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 ORDER BY w.tbl_withdraw_amount DESC";

$withdraw_result = mysqli_query($conn, $withdraw_sql);

$signup_sql = "SELECT tbl_mobile_num, tbl_balance, tbl_user_joined 
FROM tblusersdata
WHERE tbl_account_status = 'true' 
AND STR_TO_DATE(tbl_user_joined, '%d-%m-%Y %h:%i %p') >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY STR_TO_DATE(tbl_user_joined, '%d-%m-%Y %h:%i %p') DESC";

$signup_result = $conn->query($signup_sql);

$date_labels_json = json_encode($date_labels);
$recharge_data_json = json_encode($recharge_data);
$withdraw_data_json = json_encode($withdraw_data);
$profit_data_json = json_encode($profit_data);
$loss_data_json = json_encode($loss_data);
$cost_data_json = json_encode($cost_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Financial Analytics</title>
    <link href='../style.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
<style><?php include "../components/theme-variables.php"; ?></style>
<style>
        body {
            font-family: var(--font-body) !important;
            background-color: var(--page-bg) !important;
            min-height: 100vh;
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; padding: 16px 14px 14px;
            border-bottom: 1px solid var(--border-dim);
            margin-bottom: 16px;
        }
        .dash-header-left  { display: flex; align-items: center; gap: 14px; }
        .dash-header-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .dash-menu-btn {
            display:flex; align-items:center; justify-content:center;
            width:40px; height:40px; border-radius:10px;
            background:rgba(255,255,255,0.07);
            border:1px solid rgba(255,255,255,0.12);
            font-size:20px; color:#e2e8f0; cursor:pointer; flex-shrink:0;
            transition:background .2s, transform .2s;
        }
        .dash-menu-btn:hover { background:rgba(255,255,255,0.13); transform:scale(1.06); }

        .dash-breadcrumb {
            font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
            background:linear-gradient(90deg, #3b82f6, #06b6d4);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
            display: block; margin-bottom: 4px;
        }
        .dash-title {
            font-size:22px; font-weight:700; letter-spacing:-0.5px;
            color: var(--text-main); line-height:1.2; display:block;
            font-family:var(--font-body);
        }

        .dash-badge {
            display:flex; align-items:center; gap:6px;
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.11);
            border-radius:22px; padding:6px 14px;
            font-size:12px; font-weight:600; color:#94a3b8;
        }
        .dash-badge i { color:#3b82f6; font-size:14px; }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
            padding: 0 10px;
        }

        .chart-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-dim);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.30);
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .section-title {
            font-size: 14px; font-weight: 700; color: var(--text-main);
            display: flex; align-items: center; gap: 10px; margin-bottom: 24px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .title-bar {
            width: 4px; height: 20px; border-radius: 4px;
            background: linear-gradient(180deg, #3b82f6, #06b6d4); flex-shrink: 0;
        }

        .chart-wrapper {
            flex-grow: 1;
            position: relative;
            min-height: 250px;
        }

        .analytics-section {
            background: var(--panel-bg);
            border: 1px solid var(--border-dim);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.30);
        }

        .r-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .r-table thead th {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: #475569; padding: 0 16px 8px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .r-table tbody td {
            padding: 10px 14px; font-size: 14px; font-weight: 500; color: #cbd5e1;
            background: rgba(255,255,255,0.03);
            border-top: 1px solid rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: all 0.2s;
        }
        .r-table tbody td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid rgba(255,255,255,0.04); }
        .r-table tbody td:last-child  { border-radius: 0 12px 12px 0; border-right: 1px solid rgba(255,255,255,0.04); }
        .r-table tr:hover td { 
            background: rgba(59, 130, 246, 0.08); 
            color: #fff;
            border-color: rgba(59, 130, 246, 0.2);
        }

        .amount-pos { color: var(--accent-emerald); font-weight: 700; }
        .amount-neg { color: var(--accent-rose); font-weight: 700; }

        .status-badge {
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .badge-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-danger { background: rgba(244, 63, 94, 0.15); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); }

        .empty-state { text-align: center; padding: 40px; color: var(--text-dim); }

        @media (max-width: 992px) {
            .chart-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body class="bg-light">
<div class="admin-layout-wrapper">
    <?php include "../components/side-menu.php"; ?>
    <div class="admin-main-content hide-native-scrollbar">
        
        <div class="dash-header">
            <div class="dash-header-left">
                <div class="dash-menu-btn menu-open-btn"><i class='bx bx-menu'></i></div>
                <div>
                    <span class="dash-breadcrumb">Analytics > Dashboard</span>
                    <span class="dash-title">Financial Performance</span>
                </div>
            </div>
            <div class="dash-header-right">
                <div class="dash-badge"><i class='bx bx-calendar'></i>&nbsp;<?php echo date('D, M j Y'); ?></div>
                <div class="dash-badge"><i class='bx bx-time-five'></i>&nbsp;<?php echo date('h:i A'); ?></div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-card">
                <div class="section-title">
                    <span class="title-bar"></span>
                    Transaction Flow (Last 7 Days)
                </div>
                <div class="chart-wrapper">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="section-title">
                    <span class="title-bar" style="background: linear-gradient(180deg, var(--accent-emerald), #06b6d4);"></span>
                    Profit & Loss Trends
                </div>
                <div class="chart-wrapper">
                    <canvas id="profitLossChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
             <div class="analytics-section">
                <div class="section-title">
                    <span class="title-bar"></span>
                    Top User Deposits
                </div>
                <div class="w-100" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                <table class="r-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($deposit_result->num_rows > 0): 
                            while ($row = mysqli_fetch_assoc($deposit_result)): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($row['tbl_full_name']); ?></td>
                            <td class="amount-pos">₹<?php echo number_format($row['tbl_recharge_amount'], 2); ?></td>
                            <td><span class="status-badge badge-success"><i class='bx bxs-check-circle'></i> Success</span></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" class="empty-state">No deposits found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <div class="analytics-section">
                <div class="section-title">
                    <span class="title-bar" style="background: var(--accent-rose);"></span>
                    Top User Withdrawals
                </div>
                <div class="w-100" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                <table class="r-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($withdraw_result->num_rows > 0): 
                            while ($row = mysqli_fetch_assoc($withdraw_result)): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($row['tbl_full_name']); ?></td>
                            <td class="amount-neg">₹<?php echo number_format($row['tbl_withdraw_amount'], 2); ?></td>
                            <td><span class="status-badge badge-danger"><i class='bx bxs-bolt'></i> Paid</span></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" class="empty-state">No withdrawals found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="analytics-section" style="margin-bottom: 40px;">
            <div class="section-title">
                <span class="title-bar" style="background: #eab308;"></span>
                Recent Signups
            </div>
            <table class="r-table">
                <thead>
                    <tr>
                        <th>Mobile Number</th>
                        <th>Wallet Balance</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($signup_result->num_rows > 0): 
                        while ($row = $signup_result->fetch_assoc()): ?>
                        <td style="letter-spacing: 1px;"><?php echo htmlspecialchars($row["tbl_mobile_num"]); ?></td>
                        <td style="font-weight: 700; color: var(--text-main);">₹<?php echo number_format($row["tbl_balance"], 2); ?></td>
                        <td style="color: var(--text-dim); font-size: 13px;"><?php echo $row["tbl_user_joined"]; ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="3" class="empty-state">No recent signups</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim() || '#8b949e';
    Chart.defaults.font.family = "'DM Sans', sans-serif";

    document.addEventListener('DOMContentLoaded', function() {
        // Transaction Chart (Bar)
        const transactionCtx = document.getElementById('transactionChart');
        if (transactionCtx) {
            new Chart(transactionCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo $date_labels_json; ?>,
                    datasets: [
                        {
                            label: 'Recharge',
                            data: <?php echo $recharge_data_json; ?>,
                            backgroundColor: '#3b82f6',
                            borderRadius: 6,
                            barThickness: 12
                        },
                        {
                            label: 'Withdrawal',
                            data: <?php echo $withdraw_data_json; ?>,
                            backgroundColor: '#f43f5e',
                            borderRadius: 6,
                            barThickness: 12
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 20 } },
                        tooltip: {
                            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--panel-bg').trim() || '#161b22',
                            titleColor: getComputedStyle(document.documentElement).getPropertyValue('--text-main').trim() || '#fff',
                            bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--text-dim').trim() || '#c9d1d9',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) { return ' ₹' + context.raw.toLocaleString(); }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                            ticks: { callback: function(value) { return '₹' + value; } }
                        },
                        x: { grid: { display: false, drawBorder: false } }
                    }
                }
            });
        }
        
        // Profit/Loss Chart (Line)
        const profitLossCtx = document.getElementById('profitLossChart');
        if (profitLossCtx) {
            new Chart(profitLossCtx, {
                type: 'line',
                data: {
                    labels: <?php echo $date_labels_json; ?>,
                    datasets: [
                        {
                            label: 'Profit',
                            data: <?php echo $profit_data_json; ?>,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981'
                        },
                        {
                            label: 'Loss',
                            data: <?php echo $loss_data_json; ?>,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#f43f5e'
                        },
                        {
                            label: 'Cost',
                            data: <?php echo $cost_data_json; ?>,
                            borderColor: '#3b82f6',
                            borderDash: [5, 5],
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 20 } },
                        tooltip: {
                            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--panel-bg').trim() || '#161b22',
                            padding: 12,
                            borderWidth: 1,
                            borderColor: 'rgba(255,255,255,0.1)',
                            callbacks: {
                                label: function(context) { return ' ' + context.dataset.label + ': ₹' + context.raw.toLocaleString(); }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                            ticks: { callback: function(value) { return '₹' + value; } }
                        },
                        x: { grid: { display: false, drawBorder: false } }
                    }
                }
            });
        }
    });
</script>

<script src="../script.js"></script>
</body>
</html>