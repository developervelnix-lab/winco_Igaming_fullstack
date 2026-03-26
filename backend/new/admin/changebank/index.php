<?php
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
    <title>Manage Bank Cards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fa;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        input[type="text"],
        button {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
        }

        button {
            background: #007BFF;
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #0056d4;
        }

        .msg {
            margin: 10px 0;
            font-weight: bold;
            color: green;
        }

        table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
        }

        table thead {
            background: #007BFF;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
        }

        table td input {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
                text-align: left;
            }

            thead {
                display: none;
            }

            table tr {
                margin-bottom: 20px;
                background: #fff;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 0 5px rgba(0,0,0,0.1);
            }

            table td {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
            }

            table td:before {
                content: attr(data-label);
                font-weight: bold;
                flex: 1;
                color: #444;
            }

            table td input {
                width: 60%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🔍 Search & Chnage Bank account number </h2>

    <form method="POST">
        <input type="text" name="search_input" placeholder="Enter User ID or Beneficiary Name" required>
        <button type="submit" name="search">Search</button>
    </form>

    <?php if (!empty($msg)) echo "<div class='msg'>$msg</div>"; ?>

    <?php if (isset($_POST['search']) && $search_results->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Beneficiary</th>
                    <th>Bank Name</th>
                    <th>Account</th>
                    <th>IFSC</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $search_results->fetch_assoc()): ?>
                    <tr>
                        <form method="POST">
                            <td data-label="User ID"><?= htmlspecialchars($row['tbl_user_id']) ?></td>
                            <td data-label="Beneficiary"><input type="text" name="tbl_beneficiary_name" value="<?= htmlspecialchars($row['tbl_beneficiary_name']) ?>"></td>
                            <td data-label="Bank Name"><input type="text" name="tbl_bank_name" value="<?= htmlspecialchars($row['tbl_bank_name']) ?>"></td>
                            <td data-label="Account"><input type="text" name="tbl_bank_account" value="<?= htmlspecialchars($row['tbl_bank_account']) ?>"></td>
                            <td data-label="IFSC"><input type="text" name="tbl_bank_ifsc_code" value="<?= htmlspecialchars($row['tbl_bank_ifsc_code']) ?>"></td>
                            <td data-label="Action">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="update">Update</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($_POST['search'])): ?>
        <p style="margin-top:20px;">❌ No records found.</p>
    <?php endif; ?>
</div>

</body>
</html>