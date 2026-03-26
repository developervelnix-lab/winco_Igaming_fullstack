<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY", "true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if ($accessObj->validate() == "true") {
    if ($accessObj->isAllowed("access_message") == "false") {
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}
else {
    header('location:../logout-account');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php"; ?>
    <title><?php echo $APP_NAME; ?>: Send Message</title>
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
            margin-bottom: 40px; border-bottom: 1px solid var(--border-dim);
            padding-bottom: 20px;
        }
        .dash-title h1 { font-size: 28px; font-weight: 800; color: var(--text-main); margin: 0; }
        .dash-breadcrumb { font-size: 11px; font-weight: 700; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 1px; }

        .glass-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-dim); border-radius: 24px;
            padding: 40px; width: 100%; max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4); margin: 0 auto;
        }

        .form-group { margin-bottom: 24px; }
        .form-label {
            display: block; font-size: 11px; font-weight: 800; color: var(--text-dim);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
        }
        .cus-inp {
            width: 100%; height: 52px; background: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important; border-radius: 14px !important;
            padding: 0 16px !important; color: var(--text-main) !important; font-size: 15px !important;
            transition: all 0.3s ease;
        }
        .cus-inp:focus {
            border-color: var(--accent-blue) !important; background: var(--table-row-hover) !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }
        .cus-inp::placeholder { color: var(--text-dim); opacity: 0.5; }

        textarea.cus-inp { height: 150px !important; padding: 16px !important; resize: none; }

        .action-btn {
            width: 100%; height: 52px; background: var(--accent-blue);
            color: #fff; border: none; border-radius: 14px; font-weight: 800;
            font-size: 15px; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .action-btn:hover {
            transform: translateY(-2px); background: #2563eb;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        
        .btn-success-modern { background: var(--accent-emerald); }
        .btn-success-modern:hover { background: #059669; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }

        .back-link {
            display: inline-flex; align-items: center; gap: 8px; color: var(--text-dim);
            text-decoration: none; font-weight: 700; font-size: 11px; text-transform: uppercase;
            margin-bottom: 15px; cursor: pointer; transition: color 0.2s;
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
                
                <span class="dash-breadcrumb">Communication Center</span>
                <h1>Send Messages</h1>
            </div>
        </div>

        <div class="v-center" style="min-height: calc(100vh - 200px);">
            <div class="glass-card">
                <div class="form-group">
                    <label class="form-label">Message Title</label>
                    <input type="text" name="message_title" id="message_title" class="cus-inp" placeholder="Enter message title" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Message Description</label>
                    <textarea name="message_description" id="message_description" class="cus-inp" placeholder="Enter message content..." required></textarea>
                </div>

                <div class="d-grid gap-3 mt-4">
                    <button class="action-btn" onclick="SendMessage()">
                        <i class='bx bx-message-detail'></i> Send new message
                    </button>
                    <button class="action-btn btn-success-modern" onclick="SendNotice()">
                        <i class='bx bx-send'></i> Send Notice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../script.js?v=2"></script>
<script>
    let in_msg_title, in_msg_desc;
    let msg_title = document.querySelector("#message_title");
    let msg_description = document.querySelector("#message_description");

    function SendMessage() {
        in_msg_title = msg_title.value;
        in_msg_desc = msg_description.value;

        if (in_msg_title != "" && in_msg_desc != "") {
            window.open("manage-message.php?title=" + encodeURIComponent(in_msg_title) + "&message=" + encodeURIComponent(in_msg_desc));
        } else {
            alert("Invalid data!");
        }
    }

    function SendNotice() {
        in_msg_title = msg_title.value;
        in_msg_desc = msg_description.value;

        if (in_msg_title != "" && in_msg_desc != "") {
            window.open("manage-notice.php?title=" + encodeURIComponent(in_msg_title) + "&message=" + encodeURIComponent(in_msg_desc));
        } else {
            alert("Invalid data!");
        }
    }
</script>
</body>
</html>