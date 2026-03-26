<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_gift")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

$searched="";
if (isset($_POST['submit'])){
   $searched = $_POST['searched'];
}

$content = 15;
if (isset($_GET['page_num'])){
 $page_num = $_GET['page_num'];
 $offset = ($page_num-1)*$content;
}else{
 $page_num = 1;
 $offset = ($page_num-1)*$content;
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM tblotherstransactions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully!'); window.history.back();</script>";
    } else {
        echo "<script>alert('Error deleting record!');
        window.history.back();</script>";
    }
    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Withdraw</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Manage Withdraw</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">All Withdrawal</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">

            <a href="create-withdraw" class="txt-deco-n cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary"><i class='bx bx-plus'></i>&nbsp;Create Withdraw</a>
            
            <div class="w-100 ovflw-x-scroll">
            <table class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">UserID</th>
	  	         <th style="width:35%">Amount</th>
	  	         <th style="width:30%">Time</th>
	  	         <th style="width:30%">Action</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              $paginationAvailable = false;
              
              $recharge_records_sql = "SELECT * FROM tblotherstransactions WHERE tbl_transaction_type = 'Play Matched' ORDER BY id DESC LIMIT 20";
      
              $recharge_records_result = mysqli_query($conn, $recharge_records_sql) or die('search failed');
          
              if (mysqli_num_rows($recharge_records_result) > 0){
                $paginationAvailable = true;
                
                while ($row = mysqli_fetch_assoc($recharge_records_result)){
    
                ?>
                 <tr>
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_user_id']; ?></td>
	               <td><?php echo $row['tbl_transaction_amount']; ?></td>
	               <td><?php echo $row['tbl_time_stamp']; ?></td>
	               <td><a class="white-space-nw txt-deco-n cur-pointer pd-5-10 cl-white br-r-5 bg-primary bg-red" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>	              
	                </td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	               <td colspan="3">No data found!</td>
	               <td></td>
	          </tr>
            <?php } ?>
	        </table>
	        </div>
        
           </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=1"></script>

</body>
</html>