<?php
define("ACCESS_SECURITY","true");
include '../../../security/config.php';
include '../../../security/constants.php';
include '../../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_gift")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../../logout-account');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Explore Bonus</title>
    <link href='../../style.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
<style><?php include "../../components/theme-variables.php"; ?></style>
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
            margin-bottom: 30px; border-bottom: 1px solid var(--border-dim);
            padding-bottom: 15px;
        }
        .dash-title h1 { font-size: 26px; font-weight: 800; color: var(--text-main); margin: 0; }
        .form-label {
            display: block; font-size: 11px; font-weight: 800; color: var(--text-dim);
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
        }
        .form-label span { color: #f43f5e; margin-left: 2px; }

        .cus-inp {
            width: 100%; height: 48px; background: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important; border-radius: 10px !important;
            padding: 0 16px !important; color: var(--text-main) !important; font-size: 14px !important;
            transition: all 0.3s ease;
        }
        .cus-inp:focus {
            border-color: var(--accent-blue) !important; outline: none;
            background: var(--table-row-hover) !important;
        }

        .action-btn {
            background: var(--accent-blue); color: #fff; border: none;
            padding: 0 32px; font-weight: 700; font-size: 14px;
            cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center;
        }
        .action-btn:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3); }

        .dash-breadcrumb { font-size: 11px; font-weight: 700; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 1px; }

        .glass-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-dim); border-radius: 20px;
            padding: 40px; width: 100%; max-width: 1000px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4); margin: 0 auto;
            text-align: center;
        }

        .empty-icon {
            font-size: 64px; color: var(--accent-blue); opacity: 0.5; margin-bottom: 20px;
        }
        .empty-title { font-size: 20px; font-weight: 700; color: var(--text-main); margin-bottom: 10px; }
        .empty-desc { font-size: 14px; color: var(--text-dim); margin-bottom: 30px; }

    </style>
</head>
<body class="bg-light">
<div class="admin-layout-wrapper">
    <?php include "../../components/side-menu.php"; ?>
    <div class="admin-main-content hide-native-scrollbar">
        <div class="dash-header">
            <div class="dash-title">
                <span class="dash-breadcrumb">Redeem Bonus</span>
                <h1>Explore Bonus</h1>
            </div>
        </div>

        <div class="glass-card">
            <form action="" method="POST">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-5">
                        <label class="form-label" style="text-align: left;">Username <span>*</span></label>
                        <div class="d-flex gap-3">
                            <input type="text" name="username" class="cus-inp" placeholder="Enter username..." required>
                            <button type="submit" class="action-btn" style="height: 48px; border-radius: 10px;">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>
</div>

</body>
</html>
