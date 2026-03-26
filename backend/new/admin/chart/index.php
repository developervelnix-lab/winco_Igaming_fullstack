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

$sql = "SELECT tbl_mobile_num, tbl_balance, tbl_user_joined 
FROM tblusersdata
WHERE tbl_account_status = 'true' 
AND STR_TO_DATE(tbl_user_joined, '%d-%m-%Y %h:%i %p') >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY STR_TO_DATE(tbl_user_joined, '%d-%m-%Y %h:%i %p') DESC";

$result = $conn->query($sql);

// Close database connection

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard Charts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --card-shadow: 0 8px 24px rgba(149, 157, 165, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f8fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .dashboard-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }
        
        .back-button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(67, 97, 238, 0.3);
        }
        
        .back-button i {
            margin-right: 8px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: transform 0.3s ease;
            height: 400px;
            position: relative;
        }
        
        .chart-container:hover {
            transform: translateY(-5px);
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            align-items: center;
        }
        
        .chart-title i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .data-table-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .data-table-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            align-items: center;
        }
        
        .data-table-title i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            text-align: left;
            padding: 12px 15px;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafc;
        }
        
        tr:hover {
            background-color: #f0f4ff;
        }
        
        .amount {
            font-weight: 500;
            color: var(--primary);
        }
        
        .date {
            color: var(--gray);
            font-size: 0.9em;
        }
        
        .empty-state {
            text-align: center;
            padding: 20px;
            color: var(--gray);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .badge-danger {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger);
        }
        
        canvas {
            width: 100% !important;
            height: 300px !important;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .chart-container {
                padding: 15px;
                height: 350px;
            }
            
            canvas {
                height: 250px !important;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1 class="dashboard-title">Financial Analytics Dashboard</h1>
        <a href="../" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="dashboard-grid">
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-exchange-alt"></i> Recharge vs Withdrawal (Last 7 Days)
            </div>
            <canvas id="transactionChart"></canvas>
        </div>
        
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-chart-line"></i> Profit/Loss Analysis
            </div>
            <canvas id="profitLossChart"></canvas>
        </div>
    </div>
    
    <div class="data-table-container">
        <div class="data-table-title">
            <i class="fas fa-arrow-circle-up"></i> Top User Deposits (Last 7 Days)
        </div>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Deposit Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($deposit_result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($deposit_result)) { 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tbl_full_name']); ?></td>
                    <td class="amount">₹<?php echo number_format($row['tbl_recharge_amount'], 2); ?></td>
                    <td class="date"><?php echo htmlspecialchars($row['tbl_time_stamp']); ?></td>
                    <td><span class="badge badge-success">Completed</span></td>
                </tr>
                <?php 
                    }
                } else {
                    echo '<tr><td colspan="4" class="empty-state">No deposit records found</td></tr>';
                } 
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="data-table-container">
        <div class="data-table-title">
            <i class="fas fa-arrow-circle-down"></i> Top User Withdrawals (Last 7 Days)
        </div>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Withdrawal Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($withdraw_result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($withdraw_result)) { 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tbl_full_name']); ?></td>
                    <td class="amount">₹<?php echo number_format($row['tbl_withdraw_amount'], 2); ?></td>
                    <td class="date"><?php echo htmlspecialchars($row['tbl_time_stamp']); ?></td>
                    <td><span class="badge badge-danger">Processed</span></td>
                </tr>
                <?php 
                    }
                } else {
                    echo '<tr><td colspan="4" class="empty-state">No withdrawal records found</td></tr>';
                } 
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="data-table-container">
        <div class="data-table-title">
            <i class="fas fa-user-plus"></i> Recent Signups (Last 7 Days)
        </div>
        <table>
            <thead>
                <tr>
                    <th>Mobile</th>
                    <th>Balance</th>
                    <th>Date Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["tbl_mobile_num"]) . "</td>
                                <td class='amount'>₹" . number_format($row["tbl_balance"], 2) . "</td>
                                <td class='date'>" . $row["tbl_user_joined"] . "</td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="3" class="empty-state">No recent signups found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <script>
        // Make sure the DOM is fully loaded before initializing charts
        document.addEventListener('DOMContentLoaded', function() {
            // Transaction Chart
            const transactionCtx = document.getElementById('transactionChart');
            if (transactionCtx) {
                const transactionChart = new Chart(transactionCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo $date_labels_json; ?>,
                        datasets: [
                            {
                                label: 'Recharge',
                                data: <?php echo $recharge_data_json; ?>,
                                backgroundColor: 'rgba(76, 175, 80, 0.7)',
                                borderColor: 'rgba(76, 175, 80, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Withdrawal',
                                data: <?php echo $withdraw_data_json; ?>,
                                backgroundColor: 'rgba(244, 67, 54, 0.7)',
                                borderColor: 'rgba(244, 67, 54, 1)',
                                borderWidth: 1
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ₹' + context.raw.toLocaleString();
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }
            
            // Profit/Loss Chart
            const profitLossCtx = document.getElementById('profitLossChart');
            if (profitLossCtx) {
                const profitLossChart = new Chart(profitLossCtx, {
                    type: 'line',
                    data: {
                        labels: <?php echo $date_labels_json; ?>,
                        datasets: [
                            {
                                label: 'Profit',
                                data: <?php echo $profit_data_json; ?>,
                                backgroundColor: 'rgba(76, 175, 80, 0.2)',
                                borderColor: 'rgba(76, 175, 80, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Loss',
                                data: <?php echo $loss_data_json; ?>,
                                backgroundColor: 'rgba(244, 67, 54, 0.2)',
                                borderColor: 'rgba(244, 67, 54, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Total Cost',
                                data: <?php echo $cost_data_json; ?>,
                                backgroundColor: 'rgba(33, 150, 243, 0.2)',
                                borderColor: 'rgba(33, 150, 243, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ₹' + context.raw.toLocaleString();
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>