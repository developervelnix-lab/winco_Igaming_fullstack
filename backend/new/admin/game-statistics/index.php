<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_pandl")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../index.php');
}

date_default_timezone_set('Asia/Kolkata');
$curr_date_time = date('d-m-Y');
 
 $searched="";
 if (isset($_POST['submit'])){
   $searched = $_POST['searched'];
 }

 if (isset($_POST['reset'])){
   $searched = $curr_date_time;
 }

?>

<!DOCTYPE html>
<html>
<head>
<?php include "../header_contents.php" ?>
<title>Admin: Profit & Loss Records</title>

<style>
*{
	margin: 0;
  padding: 0;
  box-sizing: border-box;
	font-family: Arial, Helvetica, sans-serif;
}
.main{
	padding: 10px;
}
.admin-control-view{
    width: 100%;
}
.admin-control-view p{
    margin-top: 8px;
}
.head{
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	padding: 10px;
	width: auto;
	border-bottom: 1px dashed #000000;
}
         
#data{
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-top: 10px;
}

#title{
  font-weight: bold;
}

#data td, #data th{
  border: 1px solid #ddd;
  padding: 8px;
}
.main span{
    height: 10px;
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 15px;
    color: #FFFFFF !important;
    margin-left: 5px;
}
.main .color-green{
    color: #229954 !important;
}
.main .back-green{
    background: #229954 !important;
}
.main .color-red{
    color: #E74C3C !important;
}
.main .back-red{
    background: #E74C3C !important;
}

#data td a{
    text-decoration: none;
    padding: 8px 10px;
    color: #ffffff;
    display: inline-block;
    background: <?php echo $APP_COLOR; ?>;
}

#data tr:nth-child(even){
    background-color: #ffffff;
  }
#data tr:hover{
      background-color: #ddd;
  }
#data th{
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color:  <?php echo $ADMIN_COLOR; ?>;
    color: white;
}

.view{
    height: 0.5px;
    width: 100%;
    background-color:rgb(207, 202, 202);
}

.item{
    display: flex;
    align-items: center;
}

.info p{
    font-size: 1.1em;
}

.nxt_pre_btn a{
    text-decoration: none;
    padding: 5px 10px;
    color: #fff;
    display: inline-block;
    border-radius: 10px;
    margin-left: 10px;
    cursor: pointer;
    background: <?php echo $ADMIN_COLOR; ?>;
}

form{
  width: 100%;
  display: flex;
  flex-direction: column;
  margin-top: 10px;
}
form #in_search_bar{
  width: 100%;
  padding: 10px;
  font-size: 18px;
}

.control_btn{
  height: 40px;
  color: #ffffff;
  outline: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
  border-radius: 5px;
  padding: 5px 16px;
  background-color: <?php echo $ADMIN_COLOR; ?>;
}

.secondary_color{
  background: #34495E;
}

form .filter_options label{
  cursor: pointer;
  margin-left: 5px;
}

td label{
    padding: 3px 5px;
    color: #ffffff;
    background: #27AE60;
    margin-left: 5px;
    border-radius: 5px;
}

</style>

</head>
<body>

<div class="main">
  
  <div class="admin-control-view">
     <h2><i class='bx bx-grid-alt'></i>&nbsp;View In Detail</h2>
     <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
      <input type="text" name="searched" placeholder="Search Date Records (<?php echo $curr_date_time; ?>)" id="in_search_bar">
      <br>
      <div>
        <input type="submit" name="submit" class="control_btn" value="Search Records">
        <input type="submit" name="reset" class="control_btn back-green" value="Today Report">
      </div>
    </form>
  </div>
  
  <?php
$anlyt_total_users = 0;
$anlyt_total_recharge = 0;
$anlyt_total_withdraw = 0;
$anlyt_total_balance = 0;
$anlyt_final_result = 0;
$anlyt_today_active = 0;
$anlyt_number_withdraw = 0;
$anlyt_number_recharge = 0;
$analytic_sql = "SELECT tbl_uniq_id,tbl_user_joined,tbl_balance, tbl_last_active_date FROM tblusersdata";

$analytic_result = mysqli_query($conn, $analytic_sql) or die('Query failed');

if (mysqli_num_rows($analytic_result) > 0) {  
    $anlyt_total_users = mysqli_num_rows($analytic_result);           
    while ($row = mysqli_fetch_assoc($analytic_result)) {
        $date = date('d-m-Y', strtotime($row['tbl_user_joined']));
        if ($searched == $row['tbl_last_active_date']) {
            $anlyt_today_active++;
        }
        if ($searched == $date) {
         if (is_numeric($row['tbl_balance'])) {
            $anlyt_total_balance += $row['tbl_balance'];        
         }  
      }      
    }
}

$search_recharge_sql = "SELECT tbl_recharge_amount, tbl_time_stamp FROM tblusersrecharge 
                        WHERE tbl_request_status='success' 
                        AND (tbl_recharge_mode='ZEEPay' OR tbl_recharge_mode='UTRPay' OR tbl_recharge_mode='QRPay')";
$search_recharge_result = mysqli_query($conn, $search_recharge_sql) or die('Recharge Query failed');

while ($row = mysqli_fetch_assoc($search_recharge_result)) {
    $transaction_date = date('d-m-Y', strtotime($row['tbl_time_stamp']));

    if ($searched == $transaction_date) {
        $anlyt_number_recharge++;
        $anlyt_total_recharge += $row['tbl_recharge_amount'];
    }
}

$search_withdraw_sql = "SELECT tbl_withdraw_amount, tbl_time_stamp FROM tbluserswithdraw 
                        WHERE tbl_request_status='success'";
$search_withdraw_result = mysqli_query($conn, $search_withdraw_sql) or die('Withdraw Query failed');

while ($row = mysqli_fetch_assoc($search_withdraw_result)) {
    $transaction_date = date('d-m-Y', strtotime($row['tbl_time_stamp']));

    if ($searched == $transaction_date) {
        $anlyt_number_withdraw++;
        $anlyt_total_withdraw += $row['tbl_withdraw_amount'];
    }
}

$anlyt_p_and_l = $anlyt_total_recharge - $anlyt_total_withdraw;
$anlyt_final_result = $anlyt_total_recharge - ($anlyt_total_withdraw + $anlyt_total_balance);
    ?> 
   
   <br><br>
   <p>Transaction Details: <?php echo $searched; ?></p>
   <table id="data">
	<tr>
	  <th>Balance</th>
	  <th>Recharge</th>
	  <th>Withdraw</th>
	  <th>P & L</th>
	  <th>In Total</th>
	</tr>
	
	<tr>
      <td>₹<?php echo $anlyt_total_balance; ?></td>
      <td class="color-green">₹<?php echo $anlyt_total_recharge; ?></td>
      <td class="color-red">₹<?php echo $anlyt_total_withdraw; ?></td>
      <td><?php if($anlyt_p_and_l < 0){ echo $anlyt_p_and_l.'<span class="back-red">Loss</span>';}else if($anlyt_p_and_l > 1){echo $anlyt_p_and_l.'<span class="back-green">Profit</span>';}else{echo $anlyt_p_and_l;} ?></td>
	  <td><?php if($anlyt_final_result < 0){ echo $anlyt_final_result.'<span class="back-red">Loss</span>';}else if($anlyt_final_result > 1){echo $anlyt_final_result.'<span class="back-green">Profit</span>';}else{echo $anlyt_final_result;} ?></td>
	</tr>
	  
   </table>
   
   <br><br>
   <p>Users Details: <?php echo $searched; ?></p>
   <table id="data">
	<tr>
	  <th>Total Users</th>
	  <th>No. Recharge</th>
	  <th>No. Withdraw</th>
	  <th>Today Active</th>
	</tr>
	
	<tr>
      <td><?php echo $anlyt_total_users; ?></td>
      <td class="color-green"><?php echo $anlyt_number_recharge; ?></td>
      <td class="color-red"><?php echo $anlyt_number_withdraw; ?></td>
	  <td><?php echo $anlyt_today_active; ?></td>
	</tr>
	  
   </table>

  </div>
	
 </div>

</body>
</html>