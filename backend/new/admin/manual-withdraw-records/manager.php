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
$user_id = $data['tbl_user_id'];
$withdraw_amount = $data['tbl_withdraw_amount'];
$withdraw_request = $data['tbl_withdraw_request'];
$withdraw_details = $data['tbl_withdraw_details'];
$request_status = $data['tbl_request_status'];
$request_date_time = $data['tbl_time_stamp'];
$remark = $data['tbl_remark'];
// Extract withdraw details
$withdraw_details_arr = explode(',', $withdraw_details);
$actual_name = $withdraw_details_arr[0];
$bank_account = $withdraw_details_arr[1];
$bank_ifsc_code = isset($withdraw_details_arr[2]) ? $withdraw_details_arr[2] : "N/A";
$bank_name = isset($withdraw_details_arr[3]) ? $withdraw_details_arr[3] : "N/A";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php"; ?>
    <title><?php echo $APP_NAME; ?>: Manual Withdraw Records</title>
    <link href='../style.css' rel='stylesheet'>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    <div class="w-100 col-view pd-15 bg-primary">
        <h3 class="dpl-flx a-center cl-white" onclick="window.history.back()">
            <i class='bx bx-left-arrow-alt ft-sz-30'></i>
            <div class="col-view mg-l-10">Manual Manage Withdraw</div>
        </h3>
    </div>

    <div class="w-100 col-view v-center">
        <div class="res-w-480 col-view pd-10-15 mg-t-15 br-r-5 bg-white bx-shdw direct-p-mg-t">
            <br>
            <h3>Requested By: <?php echo htmlspecialchars($user_id); ?></h3>
            <br>
            <p>User ID: <?php echo htmlspecialchars($user_id); ?></p>
            <p>Unique ID: <span class="cl-blue"><?php echo htmlspecialchars($uniq_id); ?></span></p>
            <p>Withdraw Request: ₹<s><?php echo htmlspecialchars($withdraw_request); ?></s></p>
            <p>Withdraw Amount: ₹<?php echo htmlspecialchars($withdraw_amount); ?></p>
            <p>Withdraw DateTime: <?php echo htmlspecialchars($request_date_time); ?></p>

            <p>Status: <label id="status_label"><?php echo htmlspecialchars($request_status); ?></label></p>

            <br>
            <p>Withdraw Details:</p>
            <div class="light_back">
                <?php if ($bank_ifsc_code != "N/A") { ?>
                    <p>
                        <?php echo 'Actual Name: ' . htmlspecialchars($actual_name) . '<br>Bank Name: ' . htmlspecialchars($bank_name) .
                            '<br>Bank Account: ' . htmlspecialchars($bank_account) . '<br>IFSC Code: ' . htmlspecialchars($bank_ifsc_code); ?>
                    </p>
                <?php } else { ?>
                    <p>
                        <?php echo 'Actual Name: ' . htmlspecialchars($actual_name) . '<br>UPI ID: ' . htmlspecialchars($bank_account); ?>
                    </p>
                <?php } ?>
            </div>

            <br>
            <label for="remark">Remark :</label>
<textarea id="remark" class="remark-input" rows="3" placeholder="Enter a remark..."></textarea>
            <br>

            <?php if ($request_status == "approve" || $request_status == "pending") { ?>
                <button class="action-btn ft-sz-18 pd-10-15 bg-green" onclick="updateRequest('success')">Mark as Success</button>
                <button class="action-btn ft-sz-18 pd-10-15 mg-t-10 bg-red" onclick="updateRequest('rejected')">Reject Request</button>
            <?php } ?>
            <br>
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

</body>
</html>
