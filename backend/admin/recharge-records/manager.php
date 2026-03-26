<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_recharge")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
    exit;
}

if(!isset($_GET['uniq-id'])){
  echo "invalid request";
  return;
}else{
  $uniq_id = mysqli_real_escape_string($conn,$_GET['uniq-id']);
}

$select_sql = "SELECT * FROM tblusersrecharge WHERE tbl_uniq_id='$uniq_id'";
$select_result = mysqli_query($conn, $select_sql) or die('error');

if(mysqli_num_rows($select_result) > 0){
  $select_res_data = mysqli_fetch_assoc($select_result);

  $user_id = $select_res_data['tbl_user_id'];
  $recharge_amount = $select_res_data['tbl_recharge_amount'];
  $recharge_mode = $select_res_data['tbl_recharge_mode'];
  $recharge_details = $select_res_data['tbl_recharge_details'];
  $request_status = $select_res_data['tbl_request_status'];
  $request_date_time = $select_res_data['tbl_time_stamp'];
}else{
  echo 'Invalid order-id!';
  return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Manage Recharge</title>
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
            flex-grow: 1; height: 100vh; overflow-y: auto;
            background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.05), transparent);
            padding: 24px;
        }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 40px;
        }
        .dash-title h1 { font-size: 28px; font-weight: 800; color: #f1f5f9; margin: 0; }
        .dash-breadcrumb { font-size: 11px; font-weight: 700; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 1px; }

        .glass-card {
            background: rgba(22, 27, 34, 0.6); backdrop-filter: blur(12px);
            border: 1px solid var(--border-dim); border-radius: 24px;
            padding: 40px; width: 100%; max-width: 550px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4); margin: 0 auto;
        }

        .profile-header { text-align: center; margin-bottom: 32px; }
        .amount-display {
            font-size: 48px; font-weight: 800; color: var(--text-main); margin: 12px 0;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .amount-display span { font-size: 24px; color: var(--accent-emerald); margin-top: 8px; }

        .status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 16px; border-radius: 12px; font-size: 12px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px; margin-top: 8px;
        }
        .status-success { background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); }
        .status-rejected { background: rgba(244, 63, 94, 0.1); color: var(--accent-rose); }
        .status-pending { background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); }

        .info-grid {
            display: grid; grid-template-columns: 1fr; gap: 16px;
            margin-bottom: 32px;
        }
        .info-item {
            padding: 16px; background: rgba(0,0,0,0.2); border-radius: 16px;
            border: 1px solid var(--border-dim);
        }
        .info-label {
            font-size: 10px; font-weight: 800; color: var(--text-dim);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;
        }
        .info-value { font-size: 15px; font-weight: 700; color: #f1f5f9; }
        .info-value.blue { color: var(--accent-blue); font-family: monospace; }

        .details-box {
            background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);
            border-radius: 16px; padding: 20px; margin-top: 24px;
        }
        .details-header {
            font-size: 11px; font-weight: 800; color: var(--accent-blue);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .details-content {
            font-size: 14px; color: #cbd5e1; word-break: break-all;
            cursor: pointer; transition: color 0.2s;
        }
        .details-content:hover { color: #fff; text-decoration: underline; }

        .action-container { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 32px; }
        .btn-action {
            height: 52px; border: none; border-radius: 14px; font-weight: 800;
            font-size: 15px; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-approve { background: var(--accent-emerald); color: #fff; }
        .btn-approve:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        .btn-reject { background: rgba(244, 63, 94, 0.1); color: var(--accent-rose); border: 1px solid rgba(244, 63, 94, 0.2); }
        .btn-reject:hover { background: var(--accent-rose); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(244, 63, 94, 0.3); }

        .back-link {
            display: inline-flex; align-items: center; gap: 8px; color: var(--text-dim);
            text-decoration: none; font-weight: 700; font-size: 11px; text-transform: uppercase;
            margin-bottom: 24px; cursor: pointer; transition: color 0.2s;
        }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body class="bg-light">
<div class="admin-layout-wrapper">
    <?php include "../components/side-menu.php"; ?>
    <div class="admin-main-content hide-native-scrollbar">
        <div class="dash-header">
            <div class="dash-title">
                <div class="back-link" onclick="window.history.back()">
                    <i class='bx bx-left-arrow-alt ft-sz-18'></i> Back
                </div><br>
                <span class="dash-breadcrumb">Recharge Verification</span>
                <h1>Manage Recharge</h1>
            </div>
        </div>

        <div style="padding-bottom: 80px;">
            <div class="glass-card">
                <div class="profile-header">
                    <span class="dash-breadcrumb">Requested Amount</span>
                    <div class="amount-display"><span>₹</span><?php echo number_format($recharge_amount, 2); ?></div>
                    
                    <?php if($request_status=="success"){ ?>
                        <div class="status-pill status-success"><i class='bx bxs-check-circle'></i> <?php echo $request_status; ?></div>
                    <?php }else if($request_status=="rejected"){ ?>
                        <div class="status-pill status-rejected"><i class='bx bxs-x-circle'></i> <?php echo $request_status; ?></div>
                    <?php }else{ ?>
                        <div class="status-pill status-pending"><i class='bx bxs-time'></i> <?php echo $request_status; ?></div>
                    <?php } ?>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Player ID (Mobile)</div>
                        <div class="info-value"><?php echo $user_id; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Transaction ID (Unique)</div>
                        <div class="info-value blue"><?php echo $uniq_id; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value"><?php echo $recharge_mode ?: 'N/A'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Request Date & Time</div>
                        <div class="info-value"><?php echo $request_date_time; ?></div>
                    </div>
                </div>

                <?php if($recharge_details!=""){ ?>
                    <div class="details-box">
                        <div class="details-header"><i class='bx bx-paperclip'></i> Verification Proof</div>
                        <div class="details-content recharge_details_content">
                            <?php echo $recharge_details; ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="action-container">
                    <?php if($request_status=="success"){ ?>
                        <button class="btn-action btn-reject" onclick="RejectRequest()">
                            <i class='bx bx-undo'></i> Revert & Reject Request
                        </button>
                    <?php }else if($request_status=="pending"){ ?>
                        <button class="btn-action btn-approve" onclick="SucessRequest()">
                            <i class='bx bx-check-double'></i> Approve & Add Funds
                        </button>
                        <button class="btn-action btn-reject" onclick="RejectRequest()">
                            <i class='bx bx-x-circle'></i> Reject Transaction
                        </button>
                    <?php }else{ ?>
                        <button class="btn-action btn-approve" onclick="SucessRequest()">
                            <i class='bx bx-refresh'></i> Re-Approve Request
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  function RejectRequest(){
    if(confirm("Are you sure you want to reject this recharge?")){
        window.open("update-request.php?type=rejected&uniq-id=<?php echo $uniq_id; ?>");
        setTimeout(() => { window.location.reload(); }, 500);
    }
  }

  function SucessRequest(){
    if(confirm("Are you sure you want to approve this recharge?")){
        window.open("update-request.php?type=success&uniq-id=<?php echo $uniq_id; ?>");
        setTimeout(() => { window.location.reload(); }, 500);
    }
  }
  
  let detailsText = document.querySelector(".recharge_details_content");
  if(detailsText){
      detailsText.addEventListener("click", ()=>{
        let screenshotURL = detailsText.innerText.trim();
        if(screenshotURL.includes("screenshots") || screenshotURL.startsWith("http")){
          window.open(screenshotURL, "_blank");
        }
      });
  }
</script>
</body>
</html>