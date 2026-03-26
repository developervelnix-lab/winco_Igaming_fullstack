<?php
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

if(!isset($_GET['uniq-id'])){
  echo "invalid request";
  return;
}else{
  $uniq_id = mysqli_real_escape_string($conn,$_GET['uniq-id']);
}

$select_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_uniq_id='$uniq_id'";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  // Assign variables with guards
  $user_id = $select_res_data['tbl_user_id'] ?? "Unknown";
  $withdraw_amount = $select_res_data['tbl_withdraw_amount'] ?? 0;
  $withdraw_request = $select_res_data['tbl_withdraw_request'] ?? 0;
  $withdraw_details = $select_res_data['tbl_withdraw_details'] ?? "";
  $request_status = $select_res_data['tbl_request_status'] ?? "pending";
  $request_date_time = $select_res_data['tbl_time_stamp'] ?? date('Y-m-d H:i:s');
  
  // Extract withdraw details with null/array guards
  $withdraw_details_arr = explode(',', $withdraw_details);
  $actual_name = $withdraw_details_arr[0] ?? "N/A";
  $bank_account = $withdraw_details_arr[1] ?? "N/A";
  $bank_ifsc_code = $withdraw_details_arr[2] ?? "N/A";
  $bank_name = $withdraw_details_arr[3] ?? "N/A";
  
}else{
  echo 'Invalid order-id or order-id already confirmed!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Withdraw Record Manager</title>
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
            min-height: 100vh; color: var(--text-main); margin: 0; padding: 0;
        }

        .manager-card {
            width: 100%; max-width: 520px; background: var(--panel-bg);
            border-radius: 24px; border: 1px solid var(--border-dim);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden;
            animation: slideUp 0.4s ease-out; margin: 20px;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .card-header {
            padding: 24px; border-bottom: 1px solid var(--border-dim);
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(255,255,255,0.02);
        }
        .back-btn {
            width: 40px; height: 40px; border-radius: 12px; background: var(--input-bg);
            border: 1px solid var(--border-dim); color: var(--text-main); display: flex; align-items: center;
            justify-content: center; font-size: 24px; cursor: pointer; transition: all 0.2s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.1); transform: translateX(-4px); }

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

        .action-btns { display: flex; flex-direction: column; gap: 12px; }
        .btn-modern {
            height: 52px; border-radius: 14px; font-weight: 700; font-size: 15px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: all 0.2s; cursor: pointer; border: none; width: 100%;
        }
        .btn-success-gradient {
            background: linear-gradient(135deg, #10b981, #059669); color: #ffffff;
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
        <div class="v-center" style="min-height: calc(100vh - 40px); display: flex; align-items: center; justify-content: center;">
            <div class="manager-card">
    <div class="card-header">
        <div class="back-btn" onclick="window.history.back()"><i class='bx bx-left-arrow-alt'></i></div>
        <div style="text-align: right;">
            <div style="font-size: 10px; font-weight: 800; color: var(--text-dim); text-transform: uppercase;">Transaction ID</div>
            <div style="font-size: 13px; font-weight: 700; color: var(--accent-blue); font-family: monospace;"><?php echo htmlspecialchars($uniq_id); ?></div>
        </div>
    </div>

    <div class="card-body">
        <div class="section-label">Player Identity</div>
        <div class="info-group">
            <div class="info-row">
                <span class="info-label">User ID / Username</span>
                <span class="info-value"><?php echo htmlspecialchars($user_id); ?></span>
            </div>
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

        <div class="section-label">Withdrawal Amount</div>
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
            <?php if ($bank_ifsc_code != "null" && $bank_ifsc_code != "N/A"): ?>
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

        <?php if ($request_status == "approve" || $request_status == "pending") { ?>
            <div class="section-label">Actions</div>
            <div class="action-btns">
                <button class="btn-modern btn-success-gradient" onclick="SucessRequest('success')">
                    <i class='bx bx-check-double'></i> Success Request
                </button>
                <button class="btn-modern btn-danger-outline" onclick="RejectRequest()">
                    <i class='bx bx-x-circle'></i> Reject Withdrawal
                </button>
            </div>
        <?php } ?>
    </div>
            </div>
        </div>
    </div>
</div>

<script>
  function RejectRequest(){
    window.open("update-request.php?order-type=rejected&order-id=<?php echo $uniq_id; ?>");
  }

  function SucessRequest(status){
    window.open("update-request.php?order-type="+status+"&order-id=<?php echo $uniq_id; ?>");
  }
</script>

    </div>
</div>