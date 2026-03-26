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

$content = 15;
if (isset($_GET['page_num'])){
 $page_num = $_GET['page_num'];
 $offset = ($page_num-1)*$content;
}else{
 $page_num = 1;
 $offset = ($page_num-1)*$content;
}

// Default to showing active accounts
$accountStatus = "true";
if(isset($_POST['order_type'])){
  $accountStatus = $_POST['order_type'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Top User Balances</title>
    <link href='../style.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f4f6f9;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-btn, .filter_btn, .action-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-options {
            background-color: #e9ecef;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .table-container {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .data-table th {
            background-color: #f0f0f0;
        }
        .data-table tr:hover {
            background-color: #f9f9f9;
            cursor: pointer;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }
        .pagination a {
            padding: 10px 15px;
            margin-left: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        .pagination a:hover {
            background-color: #f0f0f0;
        }
        .cl-green { color: green; }
        .cl-red { color: red; }
        .cl-black { color: black; }
        .hide_view { display: none; }
        .rank-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .rank-1 {
            background-color: gold;
            color: #333;
        }
        .rank-2 {
            background-color: silver;
            color: #333;
        }
        .rank-3 {
            background-color: #cd7f32; /* bronze */
            color: white;
        }
    </style>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Top User Balances</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Top User Balances</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="row-view j-start">
                 <button class="filter_btn action-btn br-r-5 ft-sz-18 pd-10-15" type="button">Filter</button>
                 <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportPDF('top-balances', 'table')" type="button">Export PDF</button>
                </div>

                <div class="w-100 pd-15 mg-t-10 bg-l-blue br-r-5 filter_options hide_view">
                  <input type="checkbox" id="success_orders" name="order_type" value="true" <?php if($accountStatus=="true"){ ?> checked <?php } ?>>
                  <label for="success_orders"> Show Active</label><br>
                  
                  <input type="checkbox" id="rejected_orders" class="mg-t-10" name="order_type" value="ban" <?php if($accountStatus=="ban"){ ?> checked <?php } ?>>
                  <label for="rejected_orders"> Show Banned</label><br>
                  
                  <input type="checkbox" id="pending_orders" class="mg-t-10" name="order_type" value="false" <?php if($accountStatus=="false"){ ?> checked <?php } ?>>
                  <label for="pending_orders"> Show In-Active</label>
                  
                  <div class="row-view j-start mg-t-10">
                    <input type="submit" name="submit" value="Apply Filter" class="action-btn br-r-5 ft-sz-16 pd-10-15">
                  </div>
                </div>
            </form>
            
            </br></br>
            <p>Top Balances <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <table id="table" class="cus-tbl mg-t-10 bg-white">
              <tr>
                 <th style="width:10%">Rank</th>
                 <th style="width:25%">Id</th>
                 <th>Username</th>
                 <th>Balance</th>
                 <th>Mobile</th>
                 <th>User IP</th>
                 <th>Date & Time</th>
                 <th style="width:15%">Status</th>    
              </tr>
              
            <?php
              $indexVal = 1;
              $paginationAvailable = false;
              
              // Query to get users sorted by balance in descending order
              $user_records_sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$accountStatus}' ORDER BY tbl_balance DESC LIMIT {$offset},{$content}";
              $user_records_result = mysqli_query($conn, $user_records_sql) or die('query failed');
          
              if (mysqli_num_rows($user_records_result) > 0){
                $paginationAvailable = true;                   
                while ($row = mysqli_fetch_assoc($user_records_result)){
                    
                 $uniq_id = $row['tbl_uniq_id'];
                 $request_status = $row['tbl_account_status'];
                 $balance = $row['tbl_balance'];
                 $username = $row['tbl_full_name'];    
                 $data_sql = "SELECT tbl_device_ip FROM tblusersactivity WHERE tbl_user_id='{$uniq_id}' ORDER BY id ASC LIMIT 1";
                 $data_query = mysqli_query($conn, $data_sql);
                 $ip = "N/A";
                 if ($data_row = mysqli_fetch_assoc($data_query)) {
                    $ip = $data_row['tbl_device_ip'];
                 }
                 
                 // Determine rank class for styling
                 $rankClass = "";
                 if($indexVal == 1) {
                     $rankClass = "rank-1";
                 } else if($indexVal == 2) {
                     $rankClass = "rank-2";
                 } else if($indexVal == 3) {
                     $rankClass = "rank-3";
                 }
            ?>
                 <tr onclick="window.location.href='manager.php?id=<?php echo $uniq_id; ?>'">
                   <td><div class="rank-badge <?php echo $rankClass; ?>"><?php echo $indexVal; ?></div></td>
                   <td><?php echo $uniq_id; ?></td>
                   <td><?php echo $username; ?></td>
                   <td><strong><?php echo $balance; ?></strong></td>
                   <td><?php echo $row['tbl_mobile_num']; ?></td>
                   <td><?php echo $ip; ?></td>
                   <td><?php echo $row['tbl_user_joined']; ?></td>
                   <td class="<?php if($request_status=='true'){ echo 'cl-green'; }else if($request_status=='ban'){ echo 'cl-red'; }else{ echo 'cl-black'; } ?>"><?php if($request_status=="true"){ echo "Active"; }else if($request_status=="ban"){ echo "Banned"; }else{ echo "Not-Active"; } ?></td>
                 </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
                   <td colspan="8">No data found!</td>
              </tr>
            <?php } ?>
            </table>
            
            <?php
             $user_records_sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='{$accountStatus}'";
             $user_records_result = mysqli_query($conn, $user_records_sql) or die('fetch failed');

             if (mysqli_num_rows($user_records_result) > 0) {
               $total_records = mysqli_num_rows($user_records_result);
               $total_page = ceil($total_records/ $content);
            ?>
             <div class="w-100 row-view j-end mg-t-15">
               <div>Page: <?php echo $page_num.' / Records:'.$total_records; ?></div>
               <?php if ($page_num > 1) { ?>
                 <a href="?page_num=<?php echo $page_num-1; ?>&order_type=<?php echo $accountStatus; ?>" class="action-btn br-r-5 ft-sz-16 txt-deco-n pd-10-15 mg-l-10" type="button">Previous</a>
               <?php } ?>
               <?php if ($page_num != $total_page) { ?>
                 <a href="?page_num=<?php echo $page_num+1; ?>&order_type=<?php echo $accountStatus; ?>" class="action-btn br-r-5 ft-sz-16 txt-deco-n pd-10-15 mg-l-10" type="button">Next</a>
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