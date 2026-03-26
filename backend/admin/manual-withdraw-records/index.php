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
    if($accessObj->isAllowed("access_withdraw")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

$searched="";
if (isset($_POST['submit'])){
   $searched = $_POST['searchinp'];
}

$content = 15;
if (isset($_GET['page_num'])){
  $page_num = $_GET['page_num'];
  $offset = ($page_num-1)*$content;
}else{
  $page_num = 1;
  $offset = ($page_num-1)*$content;
}

if(isset($_POST['order_type'])){
  $newRequestStatus = $_POST['order_type'];
}else{
  $newRequestStatus = "pending";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Manual Withdraw Records</title>
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

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 24px 20px; border-bottom: 1px solid var(--border-dim);
        }
        .dash-header-left { display: flex; align-items: center; gap: 14px; }
        .back-btn {
            width: 40px; height: 40px; border-radius: 10px; background: var(--input-bg);
            border: 1px solid var(--border-dim); color: var(--text-main); display: flex; align-items: center;
            justify-content: center; font-size: 24px; cursor: pointer; transition: all 0.2s;
        }
        .back-btn:hover { background: var(--table-row-hover); transform: translateX(-4px); }

        .dash-breadcrumb { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--status-info); }
        .dash-title { font-size: 28px; font-weight: 800; color: var(--text-main); }

        .search-section {
            padding: 24px 20px; background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border-dim);
        }

        .cus-inp {
            height: 50px; background: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important; border-radius: 12px !important;
            padding: 0 16px !important; color: var(--text-main) !important; font-size: 14px !important;
            transition: all 0.2s;
        }
        .cus-inp:focus { border-color: var(--accent-blue) !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important; outline: none; }
        .cus-inp::placeholder { color: var(--text-dim) !important; opacity: 0.5; }

        .btn-modern {
            height: 50px; padding: 0 24px; border-radius: 12px; font-weight: 700; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            cursor: pointer; border: none; text-decoration: none;
        }
        .btn-primary-modern { background: var(--accent-blue); color: #fff; }
        .btn-primary-modern:hover { background: #2563eb; transform: translateY(-2px); }
        .btn-outline-modern { background: var(--input-bg); border: 1px solid var(--border-dim); color: var(--text-main); }
        .btn-outline-modern:hover { background: var(--table-row-hover); }

        /* Filter Area */
        .filter-options {
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-dim);
            border-radius: 16px; padding: 20px; margin: 20px;
        }
        .cus-checkbox-group { display: flex; gap: 24px; align-items: center; flex-wrap: wrap; }
        .cus-checkbox { display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-dim); font-size: 14px; font-weight: 600; }
        .cus-checkbox input { accent-color: var(--accent-blue); width: 18px; height: 18px; }

        /* Table Stylings */
        .r-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .r-table thead th {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--text-dim); padding: 0 16px 8px;
        }
        .r-table tbody td {
            padding: 14px 16px; font-size: 13px; font-weight: 600; color: var(--text-main);
            background: var(--table-header-bg); border-top: 1px solid var(--border-dim);
            border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td:first-child { border-radius: 12px 0 0 12px; }
        .r-table tbody td:last-child { border-radius: 0 12px 12px 0; }
        .r-table tr:hover td { background: var(--table-row-hover); color: var(--text-main); transform: scale(1.002); cursor: pointer; }

        .tag { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; display: inline-block; }
        .tag-success { background: var(--status-success); color: #fff; border: 1px solid var(--border-dim); }
        .tag-danger { background: var(--status-danger); color: #fff; border: 1px solid var(--border-dim); }
        .tag-warning { background: var(--status-warning); color: #fff; border: 1px solid var(--border-dim); }
        .tag-info { background: var(--status-info); color: #fff; border: 1px solid var(--border-dim); }

        .pagination-container { display: flex; justify-content: flex-end; align-items: center; margin-top: 24px; padding: 0 10px; }

        .hide_view { display: none !important; }
    </style>
</head>

<body class="bg-light">
<div class="admin-layout-wrapper">
    <?php include "../components/side-menu.php"; ?>
    <div class="admin-main-content hide-native-scrollbar">
        
        <div class="dash-header">
            <div class="dash-header-left">
                <div class="back-btn" onclick="window.history.back()"><i class='bx bx-left-arrow-alt'></i></div>
                <div>
                    <span class="dash-breadcrumb">Dashboard > Payouts</span>
                    <h1 class="dash-title">Manual Withdraw Records</h1>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-modern btn-outline-modern filter_btn"><i class='bx bx-filter'></i> Filter</button>
                <button class="btn-modern btn-primary-modern" onclick="exportPDF('manual-withdraw-records', 'table')"><i class='bx bxs-file-pdf'></i> Export PDF</button>
            </div>
        </div>

        <div class="search-section">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="row g-3">
                <div class="col-md-9">
                    <input type="text" name="searchinp" placeholder="Search Id, Amount, Date..." class="form-control cus-inp" value="<?php echo htmlspecialchars($searched); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" name="submit" class="btn-modern btn-primary-modern w-100">
                        <i class='bx bx-search'></i> Search Records
                    </button>
                </div>
            </form>
        </div>

        <div class="filter-options hide_view">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <p style="font-size: 11px; font-weight: 800; color: var(--accent-blue); text-transform: uppercase; margin-bottom: 16px; letter-spacing: 1px;">Status Configuration</p>
                <div class="cus-checkbox-group mb-3">
                    <label class="cus-checkbox">
                        <input type="checkbox" name="order_type" value="success" <?php if($newRequestStatus=="success"){ ?> checked <?php } ?>>
                        <span>Show Success</span>
                    </label>
                    <label class="cus-checkbox">
                        <input type="checkbox" name="order_type" value="rejected" <?php if($newRequestStatus=="rejected"){ ?> checked <?php } ?>>
                        <span>Show Rejected</span>
                    </label>
                    <label class="cus-checkbox">
                        <input type="checkbox" name="order_type" value="pending" <?php if($newRequestStatus=="pending"){ ?> checked <?php } ?>>
                        <span>Show Pending</span>
                    </label>
                    <label class="cus-checkbox">
                        <input type="checkbox" name="order_type" value="approve" <?php if($newRequestStatus=="approve"){ ?> checked <?php } ?>>
                        <span>Show Approved</span>
                    </label>
                </div>
                <button type="submit" class="btn-modern btn-primary-modern" style="height: 40px; font-size: 12px;">Apply Filter</button>
            </form>
        </div>

        <div style="padding: 0 20px;">
            <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
                <p style="font-size: 14px; font-weight: 700; color: var(--text-main); margin: 0;">
                    <?php echo ucfirst($newRequestStatus); ?> Records
                </p>
                <a class="btn-modern btn-outline-modern" onclick="window.location.reload()" style="height: 32px; font-size: 11px; padding: 0 12px;">
                    <i class='bx bx-refresh'></i> Refresh Feed
                </a>
            </div>

            <table id="table" class="r-table">
                <thead>
                    <tr>
                        <th style="width: 80px">No</th>
                        <th style="width: 250px">Withdrawal ID</th>	  	         
                        <th>Stake Amount</th>
                        <th>Request Date</th>
                        <th style="text-align: center; width: 200px;">Current Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $indexVal = $offset + 1;
                    $paginationAvailable = false;
                    
                    if($searched!=""){
                        $recharge_records_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_request_status='{$newRequestStatus}' AND (tbl_uniq_id like '%$searched%' or tbl_user_id like '%$searched%' or tbl_withdraw_amount like '%$searched%' or tbl_time_stamp like '%$searched%') LIMIT 100";
                    }else{
                        $recharge_records_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_request_status='{$newRequestStatus}' ORDER BY id DESC LIMIT {$offset},{$content}";
                    }
            
                    $recharge_records_result = mysqli_query($conn, $recharge_records_sql) or die('search failed');
                
                    if (mysqli_num_rows($recharge_records_result) > 0){
                        $paginationAvailable = true;
                        
                        while ($row = mysqli_fetch_assoc($recharge_records_result)){
                            $request_uniq_id = $row['tbl_uniq_id'];
                            $request_status = $row['tbl_request_status']; 
                    ?>
                         <tr onclick="window.location.href='manager.php?uniq-id=<?php echo $request_uniq_id; ?>'">
                            <td style="color: var(--text-dim);"><?php echo $indexVal; ?></td>
                            <td style="font-weight: 800; color: var(--text-main);"><?php echo $row['tbl_user_id']; ?></td>
                            <td style="color: var(--accent-rose); font-weight: 800;">₹<?php echo number_format($row['tbl_withdraw_amount'], 2); ?></td>
                            <td style="font-size: 12px;"><?php echo $row['tbl_time_stamp']; ?></td>
                            <td style="text-align: center;">
                                <?php 
                                if($request_status == 'success') echo '<span class="tag tag-success">Success</span>';
                                elseif($request_status == 'rejected') echo '<span class="tag tag-danger">Rejected</span>';
                                elseif($request_status == 'pending') echo '<span class="tag tag-warning">Pending</span>';
                                elseif($request_status == 'approve') echo '<span class="tag tag-info">Approved</span>';
                                else echo '<span class="tag tag-dark">'.ucfirst($request_status).'</span>';
                                ?>
                            </td>
                         </tr>
                    <?php 
                        $indexVal++; 
                        } 
                    } else { ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 60px; color: var(--text-dim);">
                                <i class='bx bx-wallet' style="font-size: 48px; display: block; margin-bottom: 12px; opacity: 0.5;"></i>
                                No withdrawal records matching your criteria.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php
            $count_sql = "SELECT id FROM tbluserswithdraw WHERE tbl_request_status='{$newRequestStatus}'";
            $count_result = mysqli_query($conn, $count_sql) or die('fetch failed');

            if (mysqli_num_rows($count_result) > 0) {
                $total_records = mysqli_num_rows($count_result);
                $total_page = ceil($total_records/ $content);
            ?>
                <div class="pagination-container">
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-dim); text-transform: uppercase;">Page <?php echo $page_num; ?> / <?php echo $total_page; ?></span>
                        <div class="d-flex gap-2">
                            <?php if ($page_num > 1): ?>
                                <a class="btn-modern btn-outline-modern" onclick="window.history.back()" style="height: 40px; font-size: 12px;">Back</a>
                            <?php endif; ?>
                            <?php if ($page_num < $total_page): ?>
                                <a href="?page_num=<?php echo $page_num+1; ?>&order_type=<?php echo $newRequestStatus; ?>" class="btn-modern btn-outline-modern" style="height: 40px; font-size: 12px;">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        
    </div>
</div>

<script src="../script.js?v=1"></script>
<script>
    document.querySelector(".filter_btn").addEventListener("click", () => {
        document.querySelector(".filter-options").classList.toggle("hide_view");
    });

    const checkboxes = document.querySelectorAll('input[name="order_type"]');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                checkboxes.forEach(other => {
                    if (other !== this) other.checked = false;
                });
            }
        });
    });
</script>
</body>
</html>