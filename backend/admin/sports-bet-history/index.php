<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()!="true"){
    header('location:../logout-account');
}

$searched="";
if (isset($_POST['submit'])){
  $searched = mysqli_real_escape_string($conn, $_POST['searchinp']);
}

$content = 15;
$page_num = (int)(isset($_GET['page_num']) ? $_GET['page_num'] : 1);
if ($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $content;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Sports Bet History</title>
    <link href='../style.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
<style><?php include "../components/theme-variables.php"; ?></style>
<style>
/* Page specific variable overrides only if needed */
        body {
            font-family: var(--font-body) !important;
            background-color: var(--page-bg) !important;
            min-height: 100vh; color: var(--text-main); margin: 0; padding: 0; overflow: hidden;
        }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 30px; border-bottom: 1px solid var(--border-dim);
            padding-bottom: 20px;
        }
        .dash-header-left  { display: flex; align-items: center; gap: 14px; }
        .dash-header-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .dash-menu-btn {
            display:flex; align-items:center; justify-content:center;
            width:40px; height:40px; border-radius:10px;
            background: var(--input-bg);
            border: 1px solid var(--border-dim);
            font-size: 20px; color: var(--text-main); cursor: pointer; flex-shrink: 0;
            transition: background .2s, transform .2s;
        }
        .dash-menu-btn:hover { background: var(--table-row-hover); transform: scale(1.06); }

        .dash-breadcrumb {
            font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
            background:linear-gradient(90deg, #3b82f6, #06b6d4);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
            display: block; margin-bottom: 4px;
        }
        .dash-title {
            font-size: 26px; font-weight: 700; letter-spacing: -0.5px;
            color: var(--text-main); line-height: 1.2; display: block;
        }

        .search-area {
            background: var(--panel-bg);
            border: 1px solid var(--border-dim);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
        }
        .search-input-group { display: flex; gap: 12px; }
        .search-input {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 12px 18px;
            color: var(--text-main);
            flex-grow: 1;
            transition: all 0.3s;
        }
        .search-input:focus {
            background: rgba(255,255,255,0.08);
            border-color: var(--accent-blue);
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .cus-inp:focus { border-color: var(--accent-blue) !important; box-shadow: none !important; }
        .cus-inp::placeholder { color: #64748b !important; opacity: 1; }

        .btn-modern {
            padding: 12px 24px; border-radius: 12px;
            font-weight: 600; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.2s; cursor: pointer; border: none;
        }
        .btn-primary-modern {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-primary-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); }
        .btn-outline-modern {
            background: var(--input-bg);
            border: 1px solid var(--border-dim);
            color: var(--text-dim);
        }
        .btn-outline-modern:hover { background: var(--table-row-hover); color: var(--text-main); }

        .section-title {
            font-size: 14px; font-weight: 700; color: var(--text-main);
            display: flex; align-items: center; gap: 10px; margin-bottom: 24px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .title-bar {
            width: 4px; height: 20px; border-radius: 4px;
            background: linear-gradient(180deg, #3b82f6, #06b6d4); flex-shrink: 0;
        }

        .record-section {
            background: var(--panel-bg); border: 1px solid var(--border-dim); border-radius: 16px;
            padding: 24px; box-shadow: 0 4px 24px rgba(0,0,0,0.3); margin-bottom: 32px;
        }

        .r-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .r-table thead th {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: #94a3b8; padding: 0 16px 8px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .r-table tbody td {
            padding: 14px 16px; font-size: 13px; font-weight: 500; color: var(--text-main);
            background: var(--table-header-bg);
            border-top: 1px solid var(--border-dim);
            border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid var(--border-dim); }
        .r-table tbody td:last-child  { border-radius: 0 12px 12px 0; border-right: 1px solid var(--border-dim); }
        .r-table tr:hover td { background: var(--table-row-hover); color: var(--text-main); border-color: var(--accent-blue); }

        .status-badge {
            padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .status-profit { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-loss   { background: rgba(244, 63, 94, 0.15);  color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); }

        .pagination-container { display: flex; justify-content: flex-end; margin-top: 24px; gap: 8px; }
        .page-btn {
            width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border-dim);
            border-radius: 10px; color: var(--text-dim); font-weight: 600; text-decoration: none;
            transition: all 0.2s;
        }
        .page-btn:hover { background: rgba(59, 130, 246, 0.1); color: #ffffff; border-color: var(--accent-blue); }
        .page-btn.active { background: var(--accent-blue); color: #ffffff; border-color: var(--accent-blue); }
        .page-btn.disabled { opacity: 0.3; pointer-events: none; }

        .text-muted { color: #94a3b8 !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
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
                    <span class="dash-breadcrumb">History & Records > Sports Bet History</span>
                    <span class="dash-title">Sports Betting Logs</span>
                </div>
            </div>
            <div class="dash-header-right">
                <button class="btn-modern btn-outline-modern" onclick="location.reload()">
                    <i class='bx bx-refresh'></i> Refresh Data
                </button>
            </div>
        </div>

        <div style="padding: 24px 14px;">
            
            <div class="search-area">
                <form method="POST" class="search-input-group">
                    <input type="text" name="searchinp" placeholder="Search Username, Bet ID, Sport Category..." class="search-input" value="<?php echo htmlspecialchars($searched); ?>" />
                    <button class="btn-modern btn-primary-modern" name="submit" type="submit">
                        <i class='bx bx-search'></i> Search Records
                    </button>
                    <button class="btn-modern btn-outline-modern" type="button" onclick="exportExcel('table', 'Sports-Bet-History.xlsx')">
                        <i class='bx bx-file'></i> Export Excel
                    </button>
                    <button class="btn-modern btn-outline-modern" type="button" onclick="exportPDF('sports-bet-history', 'table')">
                        <i class='bx bxs-file-pdf'></i> Export PDF
                    </button>
                </form>
            </div>

            <div class="record-section">
                <div class="section-title">
                    <span class="title-bar"></span>
                    Sports Transaction History
                </div>
                
                <div class="w-100 ovflw-x-scroll hide-native-scrollbar">
                    <table id="table" class="r-table">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Username & ID</th>
                                <th>Bet ID</th>
                                <th>Sport</th>
                                <th>Match Details</th>
                                <th>Type</th>
                                <th width="70">Odds</th>
                                <th>Stake</th>
                                <th>Profit/Loss</th>
                                <th>Status</th>
                                <th>Result</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $indexVal = 1;
                            $footer_total_pl = 0;
                            $footer_profit_amount = 0;
                            $footer_loss_amount = 0;
                            
                            $base_query = "SELECT m.*, u.tbl_full_name 
                                           FROM tblmatchplayed m 
                                           LEFT JOIN tblusersdata u ON m.tbl_user_id = u.tbl_uniq_id 
                                           WHERE LOWER(m.tbl_project_name) IN ('saba sports', 'lucksport', 'lucksportgaming')";
                            
                            if($searched!=""){
                                $play_records_sql = $base_query . " AND (m.tbl_period_id LIKE '%$searched%' OR m.tbl_project_name LIKE '%$searched%' OR m.tbl_user_id LIKE '%$searched%' OR u.tbl_full_name LIKE '%$searched%') ORDER BY m.id DESC LIMIT 100";
                            }else{
                                $play_records_sql = $base_query . " ORDER BY m.id DESC LIMIT {$offset}, {$content}";
                            }
                            
                            $play_records_result = mysqli_query($conn, $play_records_sql);
                        
                            if (mysqli_num_rows($play_records_result) > 0){
                                while ($row = mysqli_fetch_assoc($play_records_result)){
                                    $match_status = $row['tbl_match_status'];
                                    $stake = floatval($row['tbl_match_cost']);
                                    $profit_loss = floatval($row['tbl_match_profit']);
                                    $username = $row['tbl_full_name'] ? $row['tbl_full_name'] : 'N/A';
                                    $user_id = $row['tbl_user_id'];
                                    $bet_id = $row['tbl_period_id'];
                                    $category = $row['tbl_project_name'];
                                    
                                    // Mapping potential column names (as per discovered schema)
                                    $details = isset($row['tbl_match_details']) ? $row['tbl_match_details'] : 'N/A';
                                    $bet_type = isset($row['tbl_bet_type']) ? $row['tbl_bet_type'] : 'Back';
                                    $odds = isset($row['tbl_odds']) ? $row['tbl_odds'] : 'N/A';
                                    $game_result = $row['tbl_match_result'] ? $row['tbl_match_result'] : 'Pending';

                                    $footer_total_pl += $profit_loss;
                                    if (strtolower($match_status) === 'profit') {
                                        $footer_profit_amount += $profit_loss;
                                    } else {
                                        $footer_loss_amount += $profit_loss;
                                    }
                                    $is_profit = (strtolower($match_status) === 'profit');
                                    ?>
                                    <tr>
                                        <td style="font-size: 11px; color: var(--text-dim);"><?php echo $indexVal + $offset; ?></td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($username); ?></div>
                                            <div style="font-size: 10px; color: var(--accent-blue); opacity: 0.8;"><?php echo htmlspecialchars($user_id); ?></div>
                                        </td>
                                        <td style="font-family: monospace; font-size: 11px; color: var(--text-dim);"><?php echo htmlspecialchars($bet_id); ?></td>
                                        <td style="font-weight: 600; color: var(--accent-amber);"><?php echo htmlspecialchars($category); ?></td>
                                        <td style="font-size: 12px; max-width: 200px;"><?php echo htmlspecialchars($details); ?></td>
                                        <td>
                                            <span style="font-weight: 700; color: <?php echo ($bet_type == 'Lay') ? 'var(--accent-rose)' : 'var(--accent-blue)'; ?>;">
                                                <?php echo htmlspecialchars($bet_type); ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 700; color: var(--text-main);"><?php echo $odds; ?></td>
                                        <td style="font-weight: 600;">₹<?php echo number_format($stake, 2); ?></td>
                                        <td style="font-weight: 700; color: <?php echo $is_profit ? 'var(--accent-emerald)' : 'var(--accent-rose)'; ?>">
                                            ₹<?php echo number_format($profit_loss, 2); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $is_profit ? 'status-profit' : 'status-loss'; ?>">
                                                <?php echo ucfirst($match_status); ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 600; font-size: 12px;"><?php echo htmlspecialchars($game_result); ?></td>
                                        <td style="white-space: nowrap; font-size: 11px; color: var(--text-dim);"><?php echo htmlspecialchars($row['tbl_time_stamp']); ?></td>
                                    </tr>
                                    <?php $indexVal++; 
                                }
                            } else {
                                echo "<tr><td colspan='12' class='text-center py-5 text-muted'>No sports betting records found</td></tr>";
                            } ?>
                        </tbody>
                        <?php if ($indexVal > 1) { ?>
                        <tfoot style="background: var(--table-header-bg);">
                            <tr>
                                <td colspan="7" style="text-align: right; font-weight: 700; color: var(--text-main);">Page Profit/Loss:</td>
                                <td colspan="2" style="font-weight: 800; color: <?php echo $footer_total_pl >= 0 ? 'var(--accent-emerald)' : 'var(--accent-rose)'; ?>;">
                                    ₹<?php echo number_format($footer_total_pl, 2); ?>
                                </td>
                                <td colspan="3">
                                    <div style="display: flex; gap: 15px;">
                                        <span style="color: var(--accent-emerald); font-weight: 700;">+ ₹<?php echo number_format($footer_profit_amount, 2); ?></span>
                                        <span style="color: var(--accent-rose); font-weight: 700;">- ₹<?php echo number_format(abs($footer_loss_amount), 2); ?></span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                        <?php } ?>
                    </table>
                </div>

                <?php
                $count_base_query = "SELECT COUNT(*) as total 
                                     FROM tblmatchplayed m 
                                     LEFT JOIN tblusersdata u ON m.tbl_user_id = u.tbl_uniq_id 
                                     WHERE LOWER(m.tbl_project_name) IN ('saba sports', 'lucksport', 'lucksportgaming')";
                
                if($searched!=""){
                    $count_sql = $count_base_query . " AND (m.tbl_period_id LIKE '%$searched%' OR m.tbl_project_name LIKE '%$searched%' OR m.tbl_user_id LIKE '%$searched%' OR u.tbl_full_name LIKE '%$searched%')";
                }else{
                    $count_sql = $count_base_query;
                }
                $count_result = mysqli_query($conn, $count_sql);
                $count_row = mysqli_fetch_assoc($count_result);
                $total_records = (int)$count_row['total'];
                $total_page = ceil($total_records / $content);

                if ($total_records > 0) {
                ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div style="font-size: 12px; color: var(--text-dim); font-weight: 600;">
                        Showing page <?php echo $page_num; ?> of <?php echo $total_page; ?> (<?php echo $total_records; ?> records)
                    </div>
                    <div class="pagination-container">
                        <a href="?page_num=<?php echo max(1, $page_num - 1); ?>" class="page-btn <?php if ($page_num <= 1) echo 'disabled'; ?>">
                            <i class='bx bx-chevron-left'></i>
                        </a>
                        <?php
                        $sp = max(1, $page_num - 2);
                        $ep = min($total_page, $page_num + 2);
                        for($i=$sp; $i<=$ep; $i++){
                            $act = ($page_num == $i) ? 'active' : '';
                            echo "<a href='?page_num={$i}' class='page-btn {$act}'>{$i}</a>";
                        }
                        ?>
                        <a href="?page_num=<?php echo min($total_page, $page_num + 1); ?>" class="page-btn <?php if ($page_num >= $total_page) echo 'disabled'; ?>">
                            <i class='bx bx-chevron-right'></i>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js?v=05"></script>
<script>
function exportExcel(tableID, filename = '') {
    const table = document.getElementById(tableID);
    const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet 1" });
    return XLSX.writeFile(wb, filename || 'Export.xlsx');
}
</script>
</body>
</html>
