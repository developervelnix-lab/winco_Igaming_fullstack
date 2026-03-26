<?php
// Secure the page
define("ACCESS_SECURITY", "true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();

// Validate access
if ($accessObj->validate() == "true") {
    if ($accessObj->isAllowed("access_withdraw") == "false") {
        die("You're not allowed to view this page. Please grant access!");
    }
} else {
    header('location:../logout-account');
    exit;
}

// Validate and sanitize input
if (!isset($_GET['uniq-id'])) {
    die("Invalid request!");
}

$uniq_id = mysqli_real_escape_string($conn, $_GET['uniq-id']);

// Fetch withdraw request details
$query = "SELECT * FROM tbluserswithdraw WHERE tbl_uniq_id = '$uniq_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Invalid order-id or order-id already confirmed!");
}

$data = mysqli_fetch_assoc($result);

// Assign variables
$user_id = $data['tbl_user_id'] ?? "Unknown";
$request_status = $data['tbl_request_status'] ?? "pending";
$request_date_time = $data['tbl_time_stamp'] ?? date('Y-m-d H:i:s');
$remark = $data['tbl_remark'] ?? "";
$withdraw_request = $data['tbl_withdraw_request'] ?? 0;
$withdraw_amount = $data['tbl_withdraw_amount'] ?? 0;
$withdraw_details = $data['tbl_withdraw_details'] ?? "";

// Extract withdraw details
$withdraw_details_arr = explode(',', $withdraw_details);
$actual_name = $withdraw_details_arr[0] ?? "N/A";
$bank_account = $withdraw_details_arr[1] ?? "N/A";
$bank_ifsc_code = isset($withdraw_details_arr[2]) ? $withdraw_details_arr[2] : "N/A";
$bank_name = isset($withdraw_details_arr[3]) ? $withdraw_details_arr[3] : "N/A";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php"; ?>
    <title><?php echo $APP_NAME; ?>: Withdrawal Manager</title>
    <link href='../style.css' rel='stylesheet'>
<style><?php include "../components/theme-variables.php"; ?></style>
<style>
/* Page specific variable overrides only if needed */
        body {
            font-family: var(--font-body) !important;
            background-color: var(--page-bg) !important;
            min-height: 100vh; color: var(--text-main); margin: 0; padding: 0;
            overflow: hidden;
        }

        .manager-container {
            max-width: 600px; margin: 40px auto; padding: 0 20px;
        }

        .glass-card {
            background: var(--panel-bg); border: 1px solid var(--border-dim); border-radius: 24px;
            padding: 40px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); position: relative;
        }
<style>
/* Page specific variable overrides only if needed */
        body {
            font-family: var(--font-body) !important;
            background-color: var(--page-bg) !important;
            min-height: 100vh; color: var(--text-main); margin: 0; padding: 0;
            display: flex; align-items: center; justify-content: center;
        }

        .manager-card {
            width: 100%; max-width: 520px; background: var(--panel-bg);
            border-radius: 24px; border: 1px solid var(--border-dim);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden;
            animation: slideUp 0.4s ease-out; margin: 20px;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 24px 20px; border-bottom: 1px solid var(--border-dim);
            margin-bottom: 20px;
        }
        .dash-header-left { display: flex; align-items: center; gap: 14px; }
        .back-btn {
            width: 40px; height: 40px; border-radius: 10px; background: var(--input-bg);
            border: 1px solid var(--border-dim); color: var(--text-main); display: flex; align-items: center;
            justify-content: center; font-size: 24px; cursor: pointer; transition: all 0.2s;
        }
        .back-btn:hover { background: var(--table-row-hover); transform: translateX(-4px); }

        .card-body { padding: 32px 24px; }

        .section-label {
            font-size: 10px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1.5px; color: var(--accent-blue); margin-bottom: 16px;
        }

        .info-group { margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .info-label { font-size: 13px; color: var(--text-dim); }
        .info-value { font-size: 14px; font-weight: 700; color: var(--text-main); }

        .amount-display {
            background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.2);
            border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 32px;
        }
        .amount-val { font-size: 32px; font-weight: 800; color: var(--accent-emerald); }
        .amount-sub { font-size: 12px; color: var(--text-dim); text-decoration: line-through; opacity: 0.6; }

        .payout-box {
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-dim);
            border-radius: 16px; padding: 20px; margin-bottom: 32px;
        }
        .payout-item { margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 12px; }
        .payout-item:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }

        .tag { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .tag-success { background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); }
        .tag-warning { background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); }
        .tag-danger { background: rgba(244, 63, 94, 0.1); color: var(--accent-rose); }
        .tag-info { background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); }

        .remark-area {
            width: 100%; background: var(--input-bg) !important; border: 1px solid var(--border-dim) !important;
            border-radius: 12px; padding: 12px; color: var(--text-main); font-size: 14px; resize: none; margin-bottom: 24px;
            transition: all 0.2s;
        }
        .remark-area:focus { border-color: var(--accent-blue) !important; outline: none; background: var(--table-row-hover) !important; }

        .action-btns { display: flex; flex-direction: column; gap: 12px; }
        .btn-modern {
            height: 52px; border-radius: 14px; font-weight: 700; font-size: 15px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all 0.2s; cursor: pointer; border: none; width: 100%;
        }
        .btn-success-gradient {
            background: linear-gradient(135deg, #10b981, #059669); color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-success-gradient:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); }
        .btn-danger-outline {
            background: rgba(244, 63, 94, 0.05); color: var(--accent-rose);
            border: 1px solid rgba(244, 63, 94, 0.2);
        }
        .btn-danger-outline:hover { background: rgba(244, 63, 94, 0.1); color: #fff; border-color: var(--accent-rose); }
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
                    <span style="font-size: 10px; font-weight: 800; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px;">Payout Management</span>
                    <h1 style="font-size: 24px; font-weight: 800; color: var(--text-main); margin: 0;">Withdrawal Details</h1>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 10px; font-weight: 800; color: var(--text-dim); text-transform: uppercase;">Transaction ID</div>
                <div style="font-size: 13px; font-weight: 700; color: var(--accent-blue); font-family: monospace;"><?php echo htmlspecialchars($uniq_id); ?></div>
            </div>
        </div>

        <div class="manager-container">
            <div class="glass-card">
            <div class="info-row">
                <span class="info-label">Request Status</span>
                <?php 
                    $tagClass = "tag-warning";
                    if($request_status == "success") $tagClass = "tag-success";
                    elseif($request_status == "rejected") $tagClass = "tag-danger";
                    elseif($request_status == "approve") $tagClass = "tag-info";
                ?>
                <span class="tag <?php echo $tagClass; ?>"><?php echo ucfirst($request_status); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Submission Date</span>
                <span class="info-value"><?php echo htmlspecialchars($request_date_time); ?></span>
            </div>
        </div>

        <div class="amount-display">
            <div class="amount-val">₹<?php echo number_format((float)($withdraw_amount ?: 0), 2); ?></div>
            <?php if ($withdraw_request && $withdraw_request != $withdraw_amount): ?>
                <div class="amount-sub">Requested: ₹<?php echo number_format((float)$withdraw_request, 2); ?></div>
            <?php endif; ?>
        </div>

        <div class="section-label">Payout Destination</div>
        <div class="payout-box">
            <div class="payout-item">
                <div class="info-label" style="font-size: 11px; margin-bottom: 4px;">Beneficiary Name</div>
                <div class="info-value"><?php echo htmlspecialchars($actual_name); ?></div>
            </div>
            <?php if ($bank_ifsc_code != "N/A"): ?>
                <div class="payout-item">
                    <div class="info-label" style="font-size: 11px; margin-bottom: 4px;">Bank & Account Info</div>
                    <div class="info-value"><?php echo htmlspecialchars($bank_name); ?></div>
                    <div style="font-size: 12px; color: var(--text-dim); margin-top: 4px;">
                        Acc: <?php echo htmlspecialchars($bank_account); ?> | IFSC: <?php echo htmlspecialchars($bank_ifsc_code); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="payout-item">
                    <div class="info-label" style="font-size: 11px; margin-bottom: 4px;">UPI Identity</div>
                    <div class="info-value"><?php echo htmlspecialchars($bank_account); ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-label">Administrative Remark</div>
        <textarea id="remark" class="remark-area" rows="3" placeholder="Add a note for this transaction..."><?php echo htmlspecialchars($remark); ?></textarea>

        <?php if ($request_status == "approve" || $request_status == "pending") { ?>
            <div class="action-btns">
                <button class="btn-modern btn-success-gradient" onclick="updateRequest('success')">
                    <i class='bx bx-check-double'></i> Mark as Success
                </button>
                <button class="btn-modern btn-danger-outline" onclick="updateRequest('rejected')">
                    <i class='bx bx-x-circle'></i> Reject Withdrawal
                </button>
            </div>
        <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
    function updateRequest(status) {
        var remark = document.getElementById("remark").value.trim();
        var url = "update-request.php?order-type=" + status + "&order-id=<?php echo $uniq_id; ?>";
        
        if (remark !== "") {
            url += "&remark=" + encodeURIComponent(remark);
        }
        
        window.location.href = url;
    }
</script>

    </div>
</div>

