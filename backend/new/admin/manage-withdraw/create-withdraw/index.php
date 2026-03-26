<?php
define("ACCESS_SECURITY", "true");
include '../../../security/config.php';
include '../../../security/constants.php';
include '../../access_validate.php';

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

function generateUniqId($length = 6)
{
    $characters = "0123456789ABCDEFGHIJKLMNOPRSTUZX";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$uniq_reward_id = generateUniqId();

// Update settings btn
if (isset($_POST['submit'])) {
    if (!isset($IS_PRODUCTION_MODE) || !$IS_PRODUCTION_MODE) {
        echo "Game is under Demo Mode. So, you cannot add or modify.";
        return;
    }

    if (!isset($conn)) {
        die("Database connection error.");
    }

    $salary_userid = trim($_POST['salary_userid']);
    $salary_amount = trim($_POST['salary_amount']);

    if (empty($salary_userid) || empty($salary_amount)) {
        echo "<script>alert('User ID and Withdraw Amount are required!'); window.history.back();</script>";
        exit;
    }

    date_default_timezone_set('Asia/Kolkata');
    $curr_date_time = date('d-m-Y h:i:s a');

    // Using prepared statements to prevent SQL injection
    $check_sql = "SELECT * FROM tblotherstransactions WHERE tbl_user_id = ? AND tbl_transaction_type = 'Play Matched' AND tbl_transaction_amount = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $salary_userid, $salary_amount);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows == 0) {
        $insert_sql = "INSERT INTO tblotherstransactions (tbl_user_id, tbl_received_from, tbl_transaction_type, tbl_transaction_amount, tbl_transaction_note, tbl_time_stamp) 
                       VALUES (?, 'app', 'Play Matched', ?, 'Play Matched', ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $salary_userid, $salary_amount, $curr_date_time);
        $insert_result = $stmt->execute();

        if ($insert_result) {
            echo "<script>alert('Updated!'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error while inserting!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Already Exists!'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../../header_contents.php"; ?>
  <title>Manage: New Withdraw</title>
  <link href='../../style.css' rel='stylesheet'>
<style>
form {
  width: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 15px 20px;
  border-radius: 5px;
  background: #ffffff;
  box-shadow: 0.1px 2px 8px 4px rgba(0, 0, 0, 0.05);
}

input[type="text"], input[type="number"], select {
   width: 100%;
   height: 50px;
   margin: 10px 0;
   font-size: 20px;
   padding: 0 10px;
   border: 1px solid rgba(0,0,0,0.09);
}

form textarea {
  width: 90%;
  height: 150px;
  padding: 10px;
  font-size: 20px;
  resize: none;
}

@media (max-width: 500px) {
  form {
    width: 95%;
  }
}
</style>
</head>
<body>

<div class="mh-100vh w-100 col-view dotted-back">
    
  <div class="w-100 col-view pd-15 bg-primary">
    <div class="dpl-flx a-center cl-white" onclick="window.history.back()">
        <i class='bx bx-left-arrow-alt ft-sz-30'></i>
        <div class="col-view ft-sz-20 mg-l-10">Create Withdraw</div>
    </div>
  </div>
  
  <div class="w-100 v-center mg-t-30">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

      <div class="w-100 mg-t-10">
        <span class="ft-sz-13">User ID</span>
        <input type="text" name="salary_userid" placeholder="User ID" required>
      </div>
      
      <div class="w-100 mg-t-10">
        <span class="ft-sz-13">Withdraw Amount</span>
        <input type="text" name="salary_amount" placeholder="Withdraw Amount" required>   
      </div>      
      
      <input type="submit" name="submit" value="Add Withdraw" class="w-100 br-r-5 mg-t-30 action-btn ft-sz-20">
    </form>    
  </div>
  
</div>

</body>
</html>
