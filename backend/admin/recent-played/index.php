<?php
header("Cache-Control: no-cache");
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

$searched = "";
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
    <title><?php echo $APP_NAME; ?>: Recently Played</title>
    <link href='../style.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
<style><?php include "../components/theme-variables.php"; ?></style>
<style>
/* Page specific variable overrides only if needed */
        body {
            font-family: var(--font-body) !important;
            background-color: var(--page-bg) !important;
            min-height: 100vh;
            color: var(--text-main);
            margin: 0; padding: 0;
        }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; padding: 20px 14px 18px;
            border-bottom: 1px solid var(--border-dim);
            margin-bottom: 20px;
        }
        .dash-header-left  { display: flex; align-items: center; gap: 14px; }
        .dash-header-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .dash-menu-btn {
            display:flex; align-items:center; justify-content:center;
            width:40px; height:40px; border-radius:10px;
            background: var(--input-bg);
            border:1px solid var(--border-dim);
            font-size:20px; color: var(--text-main); cursor:pointer; flex-shrink:0;
            transition:background .2s, transform .2s;
        }
        .dash-menu-btn:hover { background: var(--input-border); transform:scale(1.06); }

        .dash-breadcrumb {
            font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
            background:linear-gradient(90deg, #3b82f6, #06b6d4);
            -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
            display: block; margin-bottom: 4px;
        }
        .dash-title {
            font-size:26px; font-weight:700; letter-spacing:-0.5px;
            color: var(--text-main); line-height:1.2; display:block;
        }

        .dash-badge {
            display:flex; align-items:center; gap:6px;
            background: var(--input-bg);
            border:1px solid var(--border-dim);
            border-radius:22px; padding:6px 14px;
            font-size:12px; font-weight:600; color: var(--text-dim);
        }
        .dash-badge i { color:#3b82f6; font-size:14px; }

        .search-area {
            background: var(--panel-bg);
            border: 1px solid var(--border-dim);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--card-shadow);
        }

        .search-input-group {
            display: flex; gap: 12px;
        }

        .search-input {
            background: var(--input-bg);
            border: 1px solid var(--border-dim);
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
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-dim);
            color: var(--text-dim);
        }
        .btn-outline-modern:hover { background: rgba(255,255,255,0.1); color: #fff; }

        .text-muted { color: var(--text-dim) !important; }

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
            padding: 24px; box-shadow: var(--card-shadow); margin-bottom: 32px;
        }

        .r-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .r-table thead th {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--text-dim); padding: 0 16px 8px;
            border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td {
            padding: 14px 16px; font-size: 14px; font-weight: 500; color: var(--text-main);
            background: var(--table-header-bg);
            border-top: 1px solid var(--border-dim);
            border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid var(--border-dim); }
        .r-table tbody td:last-child  { border-radius: 0 12px 12px 0; border-right: 1px solid var(--border-dim); }
        .r-table tr:hover td { background: var(--table-row-hover); color: var(--text-main); border-color: var(--border-dim); }

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
            background: var(--input-bg); border: 1px solid var(--border-dim);
            border-radius: 10px; color: var(--text-dim); font-weight: 600; text-decoration: none;
            transition: all 0.2s;
        }
        .page-btn:hover { background: var(--table-row-hover); color: var(--text-main); border-color: var(--accent-blue); }
        .page-btn.active { background: var(--accent-blue); color: #fff; border-color: var(--accent-blue); }
        .page-btn.disabled { opacity: 0.3; pointer-events: none; }

        .empty-state { text-align: center; padding: 60px; color: var(--text-dim); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
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
                    <span class="dash-breadcrumb">History & Records > Recently Played</span>
                    <span class="dash-title">Betting History</span>
                </div>
            </div>
            <div class="dash-header-right">
                <button class="btn-modern btn-outline-modern" onclick="window.location.href='index.php'">
                    <i class='bx bx-refresh'></i> Reset View
                </button>
                <div class="dash-badge"><i class='bx bx-calendar'></i>&nbsp;<?php echo date('M d, Y'); ?></div>
            </div>
        </div>

        <div class="search-area" style="margin-top: 24px;">
            <form method="POST" class="search-input-group" action="index.php">
                <input type="text" name="searchinp" placeholder="Search by User ID, Game Name..." class="search-input" value="<?php echo htmlspecialchars($searched); ?>" />
                <button class="btn-modern btn-primary-modern" name="submit" type="submit">
                    <i class='bx bx-search'></i> Search Records
                </button>
                <button class="btn-modern btn-outline-modern" type="button" onclick="exportPDF('betting_history', 'betTable')">
                    <i class='bx bxs-file-pdf'></i> Export PDF
                </button>
            </form>
        </div>

        <div class="record-section">
            <div class="section-title">
                <span class="title-bar" style="background: linear-gradient(180deg, var(--accent-blue), #1d4ed8);"></span>
                Real-Time Betting Logs
            </div>
            
            <table class="r-table" id="betTable">
                <thead>
                    <tr>
                        <th width="80">No</th>
                        <th>User ID</th>
                        <th>Game Name</th>
                        <th>Bet Amount</th>
                        <th>Profit / Loss</th>
                        <th width="150">Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $indexVal = 1;
                    if($searched!=""){
                      $play_records_sql = "SELECT * FROM tblmatchplayed WHERE tbl_period_id like '%$searched%' or tbl_project_name like '%$searched%' or tbl_user_id like '%$searched%' ORDER BY id DESC LIMIT 100";
                    }else{
                      $play_records_sql = "SELECT * FROM tblmatchplayed ORDER BY id DESC LIMIT $offset, $content";
                    }
                    
                    $play_records_result = mysqli_query($conn, $play_records_sql) or die('Query execution failed');
                
                    if (mysqli_num_rows($play_records_result) > 0){
                      while ($row = mysqli_fetch_assoc($play_records_result)){
                        $match_status = $row['tbl_match_status'];
                        $is_profit = ($match_status == 'profit');
                    ?>
                    <tr>
                        <td style="color: var(--text-dim); font-size: 11px;"><?php echo $indexVal + $offset; ?></td>
                        <td style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($row['tbl_user_id']); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-blue);"></div>
                                <?php echo htmlspecialchars($row['tbl_project_name']); ?>
                            </div>
                        </td>
                        <td style="font-weight: 800; color: var(--text-main);">₹<?php echo number_format((float)$row['tbl_match_cost'], 2); ?></td>
                        <td style="font-weight: 700; color: <?php echo $is_profit ? 'var(--accent-emerald)' : 'var(--accent-rose)'; ?>">
                            ₹<?php echo number_format((float)$row['tbl_match_profit'], 2); ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $is_profit ? 'status-profit' : 'status-loss'; ?>">
                                <i class='bx <?php echo $is_profit ? 'bx-trending-up' : 'bx-trending-down'; ?>'></i>
                                <?php echo ucfirst($match_status); ?>
                            </span>
                        </td>
                        <td style="color: var(--text-dim); font-size: 12px; font-weight: 600; white-space: nowrap;">
                            <?php echo htmlspecialchars($row['tbl_time_stamp']); ?>
                        </td>
                    </tr>
                    <?php $indexVal++; }} else { ?>
                    <tr><td colspan="7" class="empty-state">No betting records found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php
            $count_sql = "SELECT count(*) as total FROM tblmatchplayed";
            $count_res = mysqli_query($conn, $count_sql);
            $count_row = mysqli_fetch_assoc($count_res);
            $total_records = (int)$count_row['total'];
            $total_page = ceil($total_records / $content);

            if ($total_records > 0 && $searched == "") {
            ?>
            <div class="d-flex justify-content-between align-items-center mt-4" style="padding: 0 10px;">
                <div style="font-size: 12px; color: var(--text-dim); font-weight: 600;">
                    Showing page <?php echo $page_num; ?> of <?php echo $total_page; ?> (Total: <?php echo $total_records; ?> records)
                </div>
                <div class="pagination-container" style="margin: 0;">
                    <a href="index.php?page_num=<?php echo max(1, $page_num - 1); ?>" class="page-btn <?php if ($page_num <= 1) echo 'disabled'; ?>">
                        <i class='bx bx-chevron-left'></i>
                    </a>
                    
                    <?php
                    $start_p = max(1, $page_num - 2);
                    $end_p = min($total_page, $page_num + 2);
                    for ($i = $start_p; $i <= $end_p; $i++) {
                      $activeClass = ($page_num == $i) ? 'active' : '';
                      echo "<a href='index.php?page_num={$i}' class='page-btn {$activeClass}'>{$i}</a>";
                    }
                    ?>
                    
                    <a href="index.php?page_num=<?php echo min($total_page, $page_num + 1); ?>" class="page-btn <?php if ($page_num >= $total_page) echo 'disabled'; ?>">
                        <i class='bx bx-chevron-right'></i>
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>
</div>

<script src="../script.js?v=03"></script>
</body>
</html>