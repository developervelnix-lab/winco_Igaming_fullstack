<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_users_data")=="false"){
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
  $newRequestStatus = "true";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Users Data</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Users Data</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Users Data</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="text" name="searchinp" placeholder="Search Id, Mobile Number, Name" class="w-100 cus-inp" />
                <br><br>
                <div class="row-view j-start">
                 <input type="submit" name="submit" value="Search Records" class="action-btn br-r-5 ft-sz-18 pd-10-15">
                 <button class="filter_btn action-btn br-r-5 ft-sz-18 pd-10-15 mg-l-10" type="button">Filter</button>
                </div>

                <div class="w-100 pd-15 mg-t-10 bg-l-blue br-r-5 filter_options hide_view">
                  <input type="checkbox" id="success_orders" name="order_type" value="true" <?php if($newRequestStatus=="true"){ ?> checked <?php } ?>>
                  <label for="success_orders"> Show Active</label><br>
                  
                  <input type="checkbox" id="rejected_orders" class="mg-t-10" name="order_type" value="ban" <?php if($newRequestStatus=="ban"){ ?> checked <?php } ?>>
                  <label for="rejected_orders"> Show Banned</label><br>
                  
                  <input type="checkbox" id="pending_orders" class="mg-t-10" name="order_type" value="false" <?php if($newRequestStatus=="false"){ ?> checked <?php } ?>>
                  <label for="pending_orders"> Show In-Active</label>
                </div>
            </form>
            
            </br></br>
            <p>All Records <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <table class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">Id</th>
	  	         <th>Mobile</th>
	  	         <th style="width:25%">Status</th>
                 <th style="width:25%">24hr Deposit</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              $paginationAvailable = false;
              
              if($searched!=""){
                $user_records_sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$newRequestStatus}' AND (tbl_uniq_id like '%$searched%' or tbl_mobile_num like '%$searched%' or tbl_full_name like '%$searched%' or tbl_email_id like '%$searched%') LIMIT 100";
              }else{
                $user_records_sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$newRequestStatus}' ORDER BY id DESC LIMIT {$offset},{$content}";
              }
      
              $user_records_result = mysqli_query($conn, $user_records_sql) or die('search failed');
          
              if (mysqli_num_rows($user_records_result) > 0){
                $paginationAvailable = true;
                
                while ($row = mysqli_fetch_assoc($user_records_result)){
                    
                 $uniq_id = $row['tbl_uniq_id'];
                 $request_status = $row['tbl_account_status'];

                 // Calculate 24hr deposit amount
                 $deposit_sql = "SELECT SUM(amount) as total_deposit FROM tbldeposits WHERE user_id = '{$uniq_id}' AND deposit_time >= NOW() - INTERVAL 24 HOUR";
                 $deposit_result = mysqli_query($conn, $deposit_sql);
                 $deposit_row = mysqli_fetch_assoc($deposit_result);
                 $deposit_24hr = $deposit_row['total_deposit'] ? $deposit_row['total_deposit'] : 0;
                ?>
                 <tr onclick="window.location.href='manager.php?id=<?php echo $uniq_id; ?>'">
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $uniq_id; ?></td>
	               <td><?php echo $row['tbl_mobile_num']; ?></td>
	               <td class="<?php if($request_status=='true'){ echo 'cl-green'; }else if($request_status=='ban'){ echo 'cl-red'; }else{ echo 'cl-black'; } ?>"><?php if($request_status=="true"){ echo "Active"; }else if($request_status=="ban"){ echo "Banned"; }else{ echo "Not-Active"; } ?></td>
                   <td><?php echo number_format($deposit_24hr, 2); ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	               <td colspan="5">No data found!</td>
	          </tr>
            <?php } ?>
	        </table>
	        
	        <?php
	         $user_records_sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$newRequestStatus}'";
             $user_records_result = mysqli_query($conn, $user_records_sql) or die('fetch failed');

             if (mysqli_num_rows($user_records_result) > 0) {
               $total_records = mysqli_num_rows($user_records_result);
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

<script src="../script.js"></script>
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