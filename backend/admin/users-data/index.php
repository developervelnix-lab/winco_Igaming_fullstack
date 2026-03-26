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
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_users_data")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
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

$newRequestStatus = isset($_POST['order_type']) ? $_POST['order_type'] : (isset($_GET['order_type']) ? $_GET['order_type'] : "true");

// Handle download request (Excel export)
if (isset($_GET['download']) && $_GET['download'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=all_user_data.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";
    echo "<tr style='background: #f4f4f4;'>
        <th>No</th><th>ID</th><th>Username</th><th>Balance</th><th>Total Deposit</th>
        <th>Total Withdraw</th><th>Total Bet Amount</th><th>Total Sports Bet</th>
        <th>Sports P&L</th><th>Sports Profit</th><th>Sports Loss</th>
        <th>Mobile</th><th>User IP</th><th>Date & Time</th><th>Status</th>
    </tr>";

    $index = 1;
    $query = "SELECT * FROM tblusersdata ORDER BY id DESC";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $uniq_id = $row['tbl_uniq_id'];
        $username = $row['tbl_full_name'];
        $balance = $row['tbl_balance'];
        $mobile = $row['tbl_mobile_num'];
        $joined = $row['tbl_user_joined'];
        $status_raw = $row['tbl_account_status'];

        $ip = 'N/A';
        $ip_res = mysqli_query($conn, "SELECT tbl_device_ip FROM tblusersactivity WHERE tbl_user_id='$uniq_id' ORDER BY id ASC LIMIT 1");
        if ($ip_row = mysqli_fetch_assoc($ip_res)) $ip = $ip_row['tbl_device_ip'];

        $deposit = 0;
        $d_q = mysqli_query($conn, "SELECT SUM(tbl_recharge_amount) AS total FROM tblusersrecharge WHERE tbl_user_id='$uniq_id' AND tbl_request_status='success'");
        if ($d_r = mysqli_fetch_assoc($d_q)) $deposit = $d_r['total'] ?? 0;

        $withdraw = 0;
        $w_q = mysqli_query($conn, "SELECT SUM(tbl_withdraw_amount) AS total FROM tbluserswithdraw WHERE tbl_user_id='$uniq_id' AND tbl_request_status='success'");
        if ($w_r = mysqli_fetch_assoc($w_q)) $withdraw = $w_r['total'] ?? 0;

        $total_bet = 0;
        $b_q = mysqli_query($conn, "SELECT SUM(tbl_match_cost) AS total FROM tblmatchplayed WHERE tbl_user_id='$uniq_id'");
        if ($b_r = mysqli_fetch_assoc($b_q)) $total_bet = $b_r['total'] ?? 0;

        $s_bet = 0; $s_p_total = 0; $s_p = 0; $s_l = 0;
        $s_q = mysqli_query($conn, "SELECT tbl_match_cost, tbl_match_profit FROM tblmatchplayed WHERE tbl_user_id='$uniq_id' AND LOWER(tbl_project_name) IN ('saba sports', 'lucksport', 'lucksportgaming')");
        while ($s_r = mysqli_fetch_assoc($s_q)) {
            $c = $s_r['tbl_match_cost']; $p = $s_r['tbl_match_profit'];
            $n = $p - $c; $s_bet += $c; $s_p_total += $p;
            if ($n > 0) $s_p += $n; elseif ($n < 0) $s_l += abs($n);
        }

        $st = ($status_raw == 'true') ? 'Active' : (($status_raw == 'ban') ? 'Banned' : 'Not Active');
        echo "<tr>
            <td>$index</td><td>$uniq_id</td><td>$username</td><td>$balance</td>
            <td>₹".number_format($deposit,2)."</td><td>₹".number_format($withdraw,2)."</td>
            <td>₹".number_format($total_bet,2)."</td><td>₹".number_format($s_bet,2)."</td>
            <td>₹".number_format($s_p_total,2)."</td><td>₹".number_format($s_p,2)."</td>
            <td>₹".number_format($s_l,2)."</td><td>$mobile</td><td>$ip</td>
            <td>$joined</td><td>$st</td>
        </tr>";
        $index++;
    }
    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Users Data</title>
    <link href='../style.css?v=<?php echo time(); ?>' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            flex-wrap: wrap; gap: 12px; padding: 20px 14px 18px;
            border-bottom: 1px solid var(--border-dim);
            margin-bottom: 20px;
        }
        .dash-header-left  { display: flex; align-items: center; gap: 14px; }
        .dash-header-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .dash-menu-btn {
            display:flex; align-items:center; justify-content:center;
            width:40px; height:40px; border-radius:10px; background: var(--input-bg);
            border:1px solid var(--border-dim); font-size:20px; color: var(--text-main); cursor:pointer;
            transition:background .2s, transform .2s;
        }
        .dash-menu-btn:hover { background: var(--table-row-hover); transform:scale(1.06); }

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

        .search-area {
            background: var(--panel-bg); border: 1px solid var(--border-dim);
            border-radius: 16px; padding: 24px; margin-bottom: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
        }
        .search-input-group { display: flex; gap: 12px; }
        .search-input {
            background: var(--input-bg); border: 1px solid var(--input-border);
            border-radius: 12px; padding: 12px 18px; color: var(--text-main); flex-grow: 1;
            transition: all 0.3s;
        }
        .search-input:focus {
            background: rgba(255,255,255,0.08); border-color: var(--accent-blue);
            outline: none; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .search-input::placeholder { color: #64748b !important; opacity: 1; }

        .btn-modern {
            padding: 12px 24px; border-radius: 12px; font-weight: 600; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.2s; cursor: pointer; border: none;
        }
        .btn-primary-modern {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-primary-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); }
        .btn-outline-modern {
            background: var(--input-bg); border: 1px solid var(--border-dim);
            color: var(--text-dim);
        }
        .btn-outline-modern:hover { background: var(--table-row-hover); color: var(--text-main); }

        .filter-options {
            background: rgba(255,255,255,0.02); border: 1px solid var(--border-dim);
            border-radius: 12px; padding: 18px; margin-top: 15px; display: none;
        }
        .filter-options.show { display: block; }
        .custom-check {
            display: inline-flex; align-items: center; gap: 10px; margin-right: 25px;
            cursor: pointer; font-size: 14px; color: var(--text-dim);
        }
        .custom-check input { width: 18px; height: 18px; accent-color: var(--accent-blue); }

        .r-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .r-table thead th {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: #94a3b8; padding: 0 16px 8px; border-bottom: 1px solid rgba(255,255,255,0.07);
            white-space: nowrap;
        }
        .r-table tbody td {
            padding: 14px 16px; font-size: 13px; font-weight: 500; color: var(--text-main);
            background: var(--table-header-bg); border-top: 1px solid var(--border-dim);
            border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid rgba(255,255,255,0.04); }
        .r-table tbody td:last-child  { border-radius: 0 12px 12px 0; border-right: 1px solid rgba(255,255,255,0.04); }
        .r-table tr:hover td { background: var(--table-row-hover); color: var(--text-main); border-color: var(--accent-blue); }

        .status-badge {
            padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .status-active  { background: rgba(16, 185, 129, 0.15); color: var(--status-success); border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-banned  { background: rgba(244, 63, 94, 0.15);  color: var(--status-danger); border: 1px solid rgba(244, 63, 94, 0.3); }
        .status-inactive { background: rgba(148, 163, 184, 0.1); color: var(--text-dim); border: 1px solid var(--border-dim); }

        .pagination-container { display: flex; justify-content: flex-end; margin-top: 24px; gap: 8px; }
        .page-btn {
            width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border-dim);
            border-radius: 10px; color: var(--text-dim); font-weight: 600; text-decoration: none;
            transition: all 0.2s;
        }
        .page-btn:hover { background: rgba(59, 130, 246, 0.1); color: #fff; border-color: var(--accent-blue); }
        .page-btn.active { background: var(--accent-blue); color: #fff; border-color: var(--accent-blue); }
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
                    <span class="dash-breadcrumb">User Management > Users Data</span>
                    <span class="dash-title">Users Data</span>
                </div>
            </div>
            <div class="dash-header-right">
                <button class="btn-modern btn-outline-modern" onclick="location.reload()">
                    <i class='bx bx-refresh'></i> Refresh List
                </button>
            </div>
        </div>

        <div style="padding: 24px 14px;">
            
            <div class="search-area">
                <form method="POST" class="search-input-group">
                    <input type="text" name="searchinp" placeholder="Search ID, Mobile, Name, or Date..." class="search-input" value="<?php echo htmlspecialchars($searched); ?>" />
                    <button class="btn-modern btn-primary-modern" name="submit" type="submit">
                        <i class='bx bx-search'></i> Search players
                    </button>
                    <button class="btn-modern btn-outline-modern filter-btn-toggle" type="button">
                        <i class='bx bx-filter-alt'></i> Filter Status
                    </button>
                    <button class="btn-modern btn-outline-modern" type="button" onclick="window.location.href='?download=excel'">
                        <i class='bx bx-cloud-download'></i> Bulk Export
                    </button>
                </form>

                <div class="filter-options <?php if($newRequestStatus != "true") echo 'show'; ?>">
                    <form method="POST" id="filter-form">
                        <label class="custom-check">
                            <input type="checkbox" name="order_type" value="true" onchange="this.form.submit()" <?php if($newRequestStatus=="true") echo "checked"; ?>>
                            Show Active
                        </label>
                        <label class="custom-check">
                            <input type="checkbox" name="order_type" value="ban" onchange="this.form.submit()" <?php if($newRequestStatus=="ban") echo "checked"; ?>>
                            Show Banned
                        </label>
                        <label class="custom-check">
                            <input type="checkbox" name="order_type" value="false" onchange="this.form.submit()" <?php if($newRequestStatus=="false") echo "checked"; ?>>
                            Show In-Active
                        </label>
                    </form>
                </div>
            </div>

            <div class="record-section" style="background: rgba(255,255,255,0.01); border-radius: 16px;">
                <div class="w-100 ovflw-x-scroll hide-native-scrollbar">
                    <table id="table" class="r-table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>User Info</th>
                                <th>Balance</th>
                                <th>Deposits</th>
                                <th>Withdraws</th>
                                <th>Total Bet</th>
                                <th>Sports Bet</th>
                                <th>Sports P&L</th>
                                <th>Mobile</th>
                                <th>IP Addr</th>
                                <th>Joined</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $indexVal = 1;
                            $grand_sports_total_bet = 0; $grand_sports_total_profit = 0;
                            $grand_sports_p_amt = 0; $grand_sports_l_amt = 0;

                            if($searched!=""){
                                $sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$newRequestStatus}' AND (tbl_uniq_id like '%$searched%' or tbl_mobile_num like '%$searched%' or tbl_full_name like '%$searched%' or tbl_email_id like '%$searched%' or tbl_user_joined LIKE '%$searched%') LIMIT 100";
                            }else{
                                $sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$newRequestStatus}' ORDER BY id DESC LIMIT {$offset},{$content}";
                            }
                    
                            $res = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($res) > 0){
                                while ($row = mysqli_fetch_assoc($res)){
                                    $uid = $row['tbl_uniq_id'];
                                    $st_raw = $row['tbl_account_status'];
                                    $bal = $row['tbl_balance'];
                                    $uname = $row['tbl_full_name'];

                                    // Recharge
                                    $d_q = mysqli_query($conn, "SELECT SUM(tbl_recharge_amount) AS total FROM tblusersrecharge WHERE tbl_user_id='{$uid}' AND tbl_request_status='success'");
                                    $dep = ($d_r = mysqli_fetch_assoc($d_q)) ? ($d_r['total'] ?? 0) : 0;

                                    // Withdraw
                                    $w_q = mysqli_query($conn, "SELECT SUM(tbl_withdraw_amount) AS total FROM tbluserswithdraw WHERE tbl_user_id='{$uid}' AND tbl_request_status='success'");
                                    $wit = ($w_r = mysqli_fetch_assoc($w_q)) ? ($w_r['total'] ?? 0) : 0;

                                    // Total matches
                                    $b_q = mysqli_query($conn, "SELECT SUM(tbl_match_cost) AS total FROM tblmatchplayed WHERE tbl_user_id='{$uid}'");
                                    $t_bet = ($b_r = mysqli_fetch_assoc($b_q)) ? ($b_r['total'] ?? 0) : 0;

                                    // Sports specific
                                    $s_bet = 0; $s_p_tot = 0; $s_p_amt = 0; $s_l_amt = 0;
                                    $s_q = mysqli_query($conn, "SELECT tbl_match_cost, tbl_match_profit FROM tblmatchplayed WHERE tbl_user_id='{$uid}' AND LOWER(tbl_project_name) IN ('saba sports', 'lucksport', 'lucksportgaming')");
                                    while ($s_r = mysqli_fetch_assoc($s_q)) {
                                        $c = $s_r['tbl_match_cost']; $p = $s_r['tbl_match_profit']; $n = $p - $c;
                                        $s_bet += $c; $s_p_tot += $p;
                                        if ($n > 0) $s_p_amt += $n; elseif ($n < 0) $s_l_amt += abs($n);
                                    }

                                    $grand_sports_total_bet += $s_bet; $grand_sports_total_profit += $s_p_tot;
                                    $grand_sports_p_amt += $s_p_amt; $grand_sports_l_amt += $s_l_amt;

                                    $ip = "N/A";
                                    $i_q = mysqli_query($conn, "SELECT tbl_device_ip FROM tblusersactivity WHERE tbl_user_id='{$uid}' ORDER BY id ASC LIMIT 1");
                                    if ($i_r = mysqli_fetch_assoc($i_q)) $ip = $i_r['tbl_device_ip'];
                                    ?>
                                    <tr onclick="window.location.href='manager.php?id=<?php echo $uid; ?>'" style="cursor: pointer;">
                                        <td style="font-size: 11px; color: var(--text-dim);"><?php echo $indexVal + $offset; ?></td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($uname); ?></div>
                                            <div style="font-size: 10px; color: var(--accent-blue); opacity: 0.8;"><?php echo htmlspecialchars($uid); ?></div>
                                        </td>
                                        <td style="font-weight: 800; color: var(--text-main);">₹<?php echo number_format($bal, 2); ?></td>
                                        <td style="color: var(--accent-emerald);">₹<?php echo number_format($dep, 2); ?></td>
                                        <td style="color: var(--accent-rose);">₹<?php echo number_format($wit, 2); ?></td>
                                        <td style="font-weight: 600;">₹<?php echo number_format($t_bet, 2); ?></td>
                                        <td style="font-weight: 600; color: var(--accent-amber);">₹<?php echo number_format($s_bet, 2); ?></td>
                                        <td style="font-weight: 700; color: <?php echo ($s_p_tot >= $s_bet) ? 'var(--accent-emerald)' : 'var(--accent-rose)'; ?>">
                                            ₹<?php echo number_format($s_p_tot, 2); ?>
                                        </td>
                                        <td style="font-size: 11px; font-weight: 600;"><?php echo $row['tbl_mobile_num']; ?></td>
                                        <td style="font-family: monospace; font-size: 11px; color: var(--text-dim);"><?php echo $ip; ?></td>
                                        <td style="font-size: 11px; white-space: nowrap;"><?php echo htmlspecialchars($row['tbl_user_joined']); ?></td>
                                        <td>
                                            <?php if($st_raw == "true"): ?>
                                                <span class="status-badge status-active">Active</span>
                                            <?php elseif($st_raw == "ban"): ?>
                                                <span class="status-badge status-banned">Banned</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">In-Active</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php $indexVal++; 
                                }
                            } else {
                                echo "<tr><td colspan='12' class='text-center py-5 text-muted'>No user found matching criteria</td></tr>";
                            } ?>
                        </tbody>
                        <?php if ($indexVal > 1) { ?>
                        <tfoot style="background: var(--table-header-bg); border-top: 1px solid var(--border-dim);">
                            <tr style="font-weight: 700;">
                                <td colspan="6" style="text-align: right; color: var(--text-dim);">Page Sports Stats:</td>
                                <td style="color: var(--status-warning);">₹<?php echo number_format($grand_sports_total_bet, 2); ?></td>
                                <td style="color: var(--text-main);">₹<?php echo number_format($grand_sports_total_profit, 2); ?></td>
                                <td colspan="4">
                                    <div style="display: flex; gap: 15px;">
                                        <span style="color: var(--status-success);">+ ₹<?php echo number_format($grand_sports_p_amt, 2); ?></span>
                                        <span style="color: var(--status-danger);">- ₹<?php echo number_format($grand_sports_l_amt, 2); ?></span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                        <?php } ?>
                    </table>
                </div>

                <?php
                $c_sql = "SELECT COUNT(*) as total FROM tblusersdata WHERE tbl_account_status='{$newRequestStatus}'";
                $c_res = mysqli_query($conn, $c_sql);
                $total_recs = ($c_row = mysqli_fetch_assoc($c_res)) ? (int)$c_row['total'] : 0;
                $total_p = ceil($total_recs / $content);

                if ($total_recs > 0) {
                ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div style="font-size: 12px; color: var(--text-dim); font-weight: 600;">
                        Showing page <?php echo $page_num; ?> of <?php echo $total_p; ?> (<?php echo $total_recs; ?> records)
                    </div>
                    <div class="pagination-container">
                        <a href="?page_num=<?php echo max(1, $page_num - 1); ?>&order_type=<?php echo $newRequestStatus; ?>" class="page-btn <?php if ($page_num <= 1) echo 'disabled'; ?>">
                            <i class='bx bx-chevron-left'></i>
                        </a>
                        <?php
                        $sp = max(1, $page_num - 2); $ep = min($total_p, $page_num + 2);
                        for($i=$sp; $i<=$ep; $i++){
                            $act = ($page_num == $i) ? 'active' : '';
                            echo "<a href='?page_num={$i}&order_type={$newRequestStatus}' class='page-btn {$act}'>{$i}</a>";
                        }
                        ?>
                        <a href="?page_num=<?php echo min($total_p, $page_num + 1); ?>&order_type=<?php echo $newRequestStatus; ?>" class="page-btn <?php if ($page_num >= $total_p) echo 'disabled'; ?>">
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
    document.querySelector(".filter-btn-toggle").addEventListener("click", () => {
        document.querySelector(".filter-options").classList.toggle("show");
    });
    
    // Ensure only one filter checkbox is checked at a time (radio behavior)
    const filters = document.querySelectorAll('.custom-check input');
    filters.forEach(f => {
        f.addEventListener('click', function() {
            filters.forEach(other => { if (other !== this) other.checked = false; });
        });
    });
</script>
</body>
</html>
