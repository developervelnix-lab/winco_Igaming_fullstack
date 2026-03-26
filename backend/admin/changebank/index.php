<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY", "true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if ($accessObj->validate() == "true") {
    if ($accessObj->isAllowed("access_settings") == "false") {
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }   
} else {
    header('location:../../logout-account');
    exit;
}

$msg = "";

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $beneficiary = trim($_POST['tbl_beneficiary_name']);
    $bank = trim($_POST['tbl_bank_name']);
    $account = trim($_POST['tbl_bank_account']);
    $ifsc = trim($_POST['tbl_bank_ifsc_code']);

    $sql = "UPDATE tblallbankcards SET tbl_beneficiary_name=?, tbl_bank_name=?, tbl_bank_account=?, tbl_bank_ifsc_code=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $beneficiary, $bank, $account, $ifsc, $id);

    if ($stmt->execute()) {
        $msg = "✅ Bank card updated successfully.";
    } else {
        $msg = "❌ Failed to update bank card.";
    }
}

$search_results = [];
if (isset($_POST['search'])) {
    $search_input = trim($_POST['search_input']);
    $sql = "SELECT * FROM tblallbankcards WHERE tbl_user_id LIKE ? OR tbl_beneficiary_name LIKE ?";
    $like = "%$search_input%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $search_results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title>Manage Bank Cards</title>
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
            margin-bottom: 20px;
        }
        .dash-header-left { display: flex; align-items: center; gap: 14px; }
        .back-btn {
            width: 40px; height: 40px; border-radius: 10px; background: var(--input-bg);
            border: 1px solid var(--border-dim); color: var(--text-main); display: flex; align-items: center;
            justify-content: center; font-size: 24px; cursor: pointer; transition: all 0.2s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.1); transform: translateX(-4px); }

        .dash-breadcrumb { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--status-info); }
        .dash-title { font-size: 28px; font-weight: 800; color: var(--text-main); }

        .search-area {
            background: var(--panel-bg); border: 1px solid var(--border-dim);
            border-radius: 20px; padding: 24px; margin: 24px 20px;
            box-shadow: var(--card-shadow);
        }
        .cus-inp {
            height: 48px; background: var(--input-bg) !important;
            border: 1px solid var(--border-dim) !important; border-radius: 12px !important;
            padding: 0 16px !important; color: var(--text-main) !important; font-size: 14px !important;
        }
        .cus-inp:focus { border-color: var(--accent-blue) !important; box-shadow: none !important; }
        .cus-inp::placeholder { color: #64748b !important; opacity: 1; }

        .btn-modern {
            height: 48px; padding: 0 24px; border-radius: 12px; font-weight: 600;
            display: flex; align-items: center; gap: 8px; transition: all 0.2s;
            cursor: pointer; border: none;
        }
        .btn-primary-modern { background: var(--accent-blue); color: #fff; }
        .btn-primary-modern:hover { background: #2563eb; transform: translateY(-2px); }
        
        .update-btn {
            background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald);
            padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700;
            border: 1px solid rgba(16, 185, 129, 0.2); transition: all 0.2s;
        }
        .update-btn:hover { background: var(--accent-emerald); color: #fff; }

        .r-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .r-table thead th {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--text-dim); padding: 0 16px 8px; border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td {
            padding: 14px 16px; font-size: 13px; font-weight: 500; color: var(--text-main);
            background: var(--table-header-bg); border-top: 1px solid var(--border-dim);
            border-bottom: 1px solid var(--border-dim);
        }
        .r-table tbody td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid var(--border-dim); }
        .r-table tbody td:last-child { border-radius: 0 12px 12px 0; border-right: 1px solid var(--border-dim); }
        
        .table-inp {
            background: var(--input-bg) !important; border: 1px solid var(--border-dim) !important;
            border-radius: 8px !important; padding: 8px 12px !important; color: var(--text-main) !important;
            font-size: 13px !important; font-weight: 600 !important; transition: all 0.2s;
            width: 100%;
        }
        .table-inp:focus {
            background: rgba(255,255,255,0.06) !important; border-color: var(--accent-blue) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important; outline: none;
        }

        .msg-alert {
            margin: 0 20px 20px; padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 14px;
        }
        .msg-success { background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); }
        .msg-error { background: rgba(244, 63, 94, 0.1); color: var(--accent-rose); border: 1px solid rgba(244, 63, 94, 0.2); }
    </style>
</head>

<body class="bg-light">
<div class="admin-layout-wrapper">
    <?php include "../components/side-menu.php"; ?>
    <div class="admin-main-content hide-native-scrollbar">
        
        <div class="dash-header">
            <div class="dash-header-left">
                <div>
                    <span class="dash-breadcrumb">System Settings > Bank Control</span>
                    <h1 class="dash-title">Search & Change Bank Details</h1>
                </div>
            </div>
            <button class="btn-modern btn-outline-modern" onclick="window.location.reload()" style="background: var(--input-bg); border: 1px solid var(--border-dim); color: var(--text-main);">
                <i class='bx bx-refresh'></i> Sync Records
            </button>
        </div>

        <div class="search-area">
            <form method="POST" class="row g-3">
                <div class="col-md-9">
                    <input type="text" name="search_input" placeholder="Enter User ID or Beneficiary Name..." class="form-control cus-inp" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="search" class="btn-modern btn-primary-modern w-100">
                        <i class='bx bx-search'></i> Search Data
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="msg-alert <?php echo strpos($msg, '✅') !== false ? 'msg-success' : 'msg-error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="padding: 0 20px;">
            <?php if (isset($_POST['search']) && $search_results->num_rows > 0): ?>
                <table class="r-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Beneficiary Name</th>
                            <th>Bank Name</th>
                            <th>Account Number</th>
                            <th>IFSC Code</th>
                            <th style="text-align: right;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $search_results->fetch_assoc()): ?>
                            <tr>
                                <form method="POST">
                                    <td style="font-weight: 700; color: var(--accent-blue);"><?= htmlspecialchars($row['tbl_user_id']) ?></td>
                                    <td><input type="text" name="tbl_beneficiary_name" value="<?= htmlspecialchars($row['tbl_beneficiary_name']) ?>" class="table-inp"></td>
                                    <td><input type="text" name="tbl_bank_name" value="<?= htmlspecialchars($row['tbl_bank_name']) ?>" class="table-inp"></td>
                                    <td><input type="text" name="tbl_bank_account" value="<?= htmlspecialchars($row['tbl_bank_account']) ?>" class="table-inp"></td>
                                    <td><input type="text" name="tbl_bank_ifsc_code" value="<?= htmlspecialchars($row['tbl_bank_ifsc_code']) ?>" class="table-inp"></td>
                                    <td style="text-align: right;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="update" class="update-btn">Save Changes</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php elseif (isset($_POST['search'])): ?>
                <div style="text-align: center; padding: 60px; color: var(--text-dim);">
                    <i class='bx bx-search-alt' style="font-size: 48px; display: block; margin-bottom: 12px; opacity: 0.5;"></i>
                    No bank records found matching your query.
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>
</body>
</html>