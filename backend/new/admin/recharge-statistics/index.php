<?php

define("ACCESS_SECURITY", "true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if ($accessObj->validate() == "true") {
    if ($accessObj->isAllowed("access_recharge") == "false") {
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
} else {
    header('location:../logout-account');
}

$selectedDate = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');
$totalAmount = 0;
$error = '';

if (isset($_POST['submit'])) {
    $sql = "SELECT COALESCE(SUM(tbl_recharge_amount), 0) AS total_amount 
            FROM tblusersrecharge 
            WHERE DATE(tbl_time_stamp) = '$selectedDate' 
            AND tbl_request_status = 'success'";

    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        var_dump($row); // Debugging: Check if the result is correctly retrieved
        $totalAmount = isset($row['total_amount']) ? $row['total_amount'] : 0;
    } else {
        $error = "Error executing query: " . mysqli_error($conn);
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Total Recharge Amount</title>
  <link href='../style.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    
<div class="mh-100vh w-100">
    <div class="row-view sb-view">
      <?php include "../components/side-menu.php"; ?>
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Total Recharge Amount</p>
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white">
                <i class='bx bx-menu'></i>
            </div>
            <h1 class="mg-l-15">Total Recharge Amount</h1> 
        </div>
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="selected_date">Select Date:</label>
                <input type="date" id="selected_date" name="selected_date" value="<?php echo $selectedDate; ?>" class="cus-inp" required>
                <br><br>
                <input type="submit" name="submit" value="Get Total Amount" class="action-btn br-r-5 ft-sz-18 pd-10-15">
            </form>
            <div class="mg-t-20">
                <?php if ($error): ?>
                    <p class="cl-red"><?php echo $error; ?></p>
                <?php else: ?>
                    <h2>Total Recharge Amount for <?php echo date('F j, Y', strtotime($selectedDate)); ?>:</h2>
                    <p class="ft-sz-24 ft-w-bold"> <?php echo number_format($totalAmount, 2); ?> </p>
                <?php endif; ?>
            </div>
        </div>
      </div>
    </div>
</div>
<script src="../script.js?v=1"></script>
</body>
</html>