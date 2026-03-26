<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_withdraw")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

$searched="";
if (isset($_POST['submit'])){
   $searched = $_POST['searchinp'];
}

$content = 15;
if (isset($_GET['page_num'])){
 $page_num = $_GET['page_num'];
 $offset = ($page_num-1)*$content;
}else{
 $page_num = 1;
 $offset = ($page_num-1)*$content;
}

if(isset($_POST['order_type'])){
  $newRequestStatus = $_POST['order_type'];
}else{
  $newRequestStatus = "pending";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Manual Withdraw Records</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Manual Withdraw Records</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Manual Withdraw Records</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="text" name="searchinp" placeholder="Search Id, Amount, Date" class="w-100 cus-inp" />
                <br><br>
                <div class="row-view j-start">
                 <input type="submit" name="submit" value="Search Records" class="action-btn br-r-5 ft-sz-18 pd-10-15">
                 <button class="filter_btn action-btn br-r-5 ft-sz-18 pd-10-15 mg-l-10" type="button">Filter</button>
                  <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportPDF('manual-withdraw-records', 'table')" type="button">Export PDF</button>
                </div>

                <div class="w-100 pd-15 mg-t-10 bg-l-blue br-r-5 filter_options hide_view">
                  <input type="checkbox" id="success_orders" name="order_type" value="success" <?php if($newRequestStatus=="success"){ ?> checked <?php } ?>>
                  <label for="success_orders"> Show success</label><br>
                  
                  <input type="checkbox" id="rejected_orders" class="mg-t-10" name="order_type" value="rejected" <?php if($newRequestStatus=="rejected"){ ?> checked <?php } ?>>
                  <label for="rejected_orders"> Show rejected</label><br>
                  
                  <input type="checkbox" id="pending_orders" class="mg-t-10" name="order_type" value="pending" <?php if($newRequestStatus=="pending"){ ?> checked <?php } ?>>
                  <label for="pending_orders"> Show pending</label><br>
                  
                  <input type="checkbox" id="approved_orders" class="mg-t-10" name="order_type" value="approve" <?php if($newRequestStatus=="approve"){ ?> checked <?php } ?>>
                  <label for="approved_orders"> Show approved</label>
                </div>
            </form>
            
            </br></br>
            <p><?php echo $newRequestStatus; ?> Records <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <table id="table" class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">Id</th>	  	         
	  	         <th>Amount</th>
	  	         <th>Date & Time</th>
	  	         <th style="width:25%">Status</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              $paginationAvailable = false;
              
              if($searched!=""){
                $recharge_records_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_request_status='{$newRequestStatus}' AND (tbl_uniq_id like '%$searched%' or tbl_user_id like '%$searched%' or tbl_withdraw_amount like '%$searched%' or tbl_time_stamp like '%$searched%') LIMIT 100";
              }else{
                $recharge_records_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_request_status='{$newRequestStatus}' ORDER BY id DESC LIMIT {$offset},{$content}";
              }
      
              $recharge_records_result = mysqli_query($conn, $recharge_records_sql) or die('search failed');
          
              if (mysqli_num_rows($recharge_records_result) > 0){
                $paginationAvailable = true;
                
                while ($row = mysqli_fetch_assoc($recharge_records_result)){
                 $request_uniq_id = $row['tbl_uniq_id'];
                 $request_status = $row['tbl_request_status']; ?>
                 
                 <tr onclick="window.location.href='manager.php?uniq-id=<?php echo $request_uniq_id; ?>'">
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_user_id']; ?></td>
	               <td><?php echo $row['tbl_withdraw_amount']; ?></td>
	               <td><?php echo $row['tbl_time_stamp']; ?></td>
	               <td class="<?php if($request_status=='success'){ echo 'cl-green'; }else if($request_status=='rejected'){ echo 'cl-red'; }else{ echo 'cl-black'; } ?>"><?php if($request_status=="success"){ echo "Success"; }else{ echo $request_status; } ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	             <td colspan="5">No data found!</td>
	          </tr>
            <?php } ?>
	        </table>
	        
	        <?php
	         $recharge_records_sql = "SELECT * FROM tbluserswithdraw WHERE tbl_request_status='{$newRequestStatus}'";
             $recharge_records_result = mysqli_query($conn, $recharge_records_sql) or die('fetch failed');

             if (mysqli_num_rows($recharge_records_result) > 0) {
               $total_records = mysqli_num_rows($recharge_records_result);
               $total_page = ceil($total_records/ $content);
            ?>
	         <div class="w-100 row-view j-end mg-t-15">
               <div>Page: <?php echo $page_num.' / Records:'.$total_records; ?></div>
               <?php if ($page_num > 1) { ?>
                 <a class="action-btn br-r-5 ft-sz-16 pd-10-15 mg-l-10" type="button" onclick="window.history.back()">Back</a>
               <?php } ?>
               <?php if ($page_num != $total_page) { ?>
                 <a href="?page_num=<?php echo $page_num+1; ?>&order_type=<?php echo $newRequestStatus; ?>" class="action-btn br-r-5 ft-sz-16 txt-deco-n pd-10-15 mg-l-10" type="button">Next</a>
               <?php } ?>
             </div>
            <?php } ?>
        
           </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=1"></script>
<script>
  document.querySelector(".filter_btn").addEventListener("click", ()=>{
    document.querySelector(".filter_options").classList.toggle("hide_view")
  });

  var filterOp = document.querySelector(".filter_options");
    var option = filterOp.getElementsByTagName("input");
    for (var i = 0; i < option.length; i++) {
      option[i].onclick = function () {
        for (var i = 0; i < option.length; i++) {
          if (option[i] != this && this.checked) {
            option[i].checked = false;
          }
        }
      };
    }
</script>

</body>
</html>