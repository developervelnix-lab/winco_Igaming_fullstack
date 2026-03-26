<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()!="true"){
    header('location:../logout-account');
}

$searched="";
if (isset($_POST['submit'])){
  $searched = $_POST['searchinp'];
}

$content = 15;
if (isset($_GET['page_num'])){
 $page_num = $_GET['page_num'];
}else{
 $page_num = 1;
}

$offset = ($page_num-1)*$content;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Sports Bet History</title>
  <link href='../style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <style>
    body { background-color: #f4f6f9; }
    .search-btn, .filter_btn, .action-btn {
      background-color: #007bff;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .cl-green { color: green; }
    .cl-red { color: red; }
    .cl-black { color: black; }
    #table {
      border-collapse: collapse;
      width: 100%;
      table-layout: fixed;
      overflow-x: auto;
      display: block;
    }
    #table th, #table td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    #table th {
      background-color: #8B4513;
      color: white;
    }
    .w-100.ovflw-x-scroll { overflow-x: auto; }
  </style>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Sports Bet History</p>

        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Sports Bet History</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">           
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="text" name="searchinp" placeholder="Search Username, Bet ID, Sport Category" class="w-100 cus-inp" />
                <br><br>
                <div class="row-view j-start">
                 <input type="submit" name="submit" value="Search Records" class="action-btn br-r-5 ft-sz-18 pd-10-15">
                 <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportPDF('sports-bet-history', 'table')" type="button">Export PDF</button>
                 <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportExcel('table', 'Sports-Bet-History.xlsx')">Export Excel</button>
                </div>
            </form>
            </br></br>
            <p>Sports Bet History Records <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <div class="w-100 ovflw-x-scroll">
            <table id="table" class="cus-tbl mg-t-10 bg-white" style="table-layout: fixed;">
              <thead>
	          <tr>
                 <th style="width:5%">No</th>
	  	         <th style="width:12%">Username & ID</th>
	  	         <th style="width:10%">Bet ID</th>
	  	         <th style="width:10%">Sport Category</th>
	  	         <th style="width:12%">Match Details</th>
	  	         <th style="width:8%">Bet Type</th>
	  	         <th style="width:8%">Odds</th>
	  	         <th style="width:8%">Stake</th>
	  	         <th style="width:10%">Profit/Loss</th>
	  	         <th style="width:8%">Bet Status</th>
	  	         <th style="width:9%">Game Result</th>
	  	         <th style="width:10%">Date & Time</th>
	          </tr>
              </thead>
              <tbody>
	        <?php
              $indexVal = 1;
              $footer_total_pl = 0;
              $footer_profit_amount = 0;
              $footer_loss_amount = 0;
              
              // Base query with JOIN to get username
              $base_query = "SELECT m.*, u.tbl_full_name 
                             FROM tblmatchplayed m 
                             LEFT JOIN tblusersdata u ON m.tbl_user_id = u.tbl_uniq_id 
                             WHERE LOWER(m.tbl_project_name) IN ('saba sports', 'lucksport', 'lucksportgaming')";
              
              if($searched!=""){
                $play_records_sql = $base_query . " AND (m.tbl_period_id LIKE '%$searched%' OR m.tbl_project_name LIKE '%$searched%' OR m.tbl_user_id LIKE '%$searched%' OR u.tbl_full_name LIKE '%$searched%') ORDER BY m.id DESC LIMIT 100";
              }else{
                $play_records_sql = $base_query . " ORDER BY m.id DESC LIMIT {$offset},{$content}";
              }
              
              $play_records_result = mysqli_query($conn, $play_records_sql) or die('search failed');
          
              if (mysqli_num_rows($play_records_result) > 0){
                while ($row = mysqli_fetch_assoc($play_records_result)){
    
                 $match_status = $row['tbl_match_status'];
                 $stake = floatval($row['tbl_match_cost']);
                 $profit_loss = floatval($row['tbl_match_profit']);
                 $username = $row['tbl_full_name'] ? $row['tbl_full_name'] : 'N/A';
                 $user_id = $row['tbl_user_id'];
                 $bet_id = $row['tbl_period_id'];
                 $sport_category = $row['tbl_project_name'];
                 $match_details = $row['tbl_match_details'];
                 
                 // Bet type: Determine back/lay based on match status or default to Back
                 // Back = betting for, Lay = betting against
                 $bet_type = $row['tbl_bet_type']; // Default, can be modified based on business logic
                 
                 // Odds: Not stored in table, showing N/A or can be calculated if needed
                 $odds = $row['tbl_odds'];
                 
                 // Bet status
                 $bet_status = ucfirst($match_status);
                 
                 // Game result
                 $game_result = $row['tbl_match_result'] ? $row['tbl_match_result'] : 'Pending';

                 // Footer totals
                 $footer_total_pl += $profit_loss;
                 if (strtolower($match_status) === 'profit') {
                   $footer_profit_amount += $profit_loss;
                 } else {
                   $footer_loss_amount += $profit_loss;
                 }
                ?>
                 <tr>
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo htmlspecialchars($username); ?><br><small style="color:#666;"><?php echo $user_id; ?></small></td>
	               <td><?php echo $bet_id; ?></td>
	               <td><?php echo $sport_category; ?></td>
	               <td><?php echo htmlspecialchars($match_details); ?></td>
	               <td><?php echo $bet_type; ?></td>
	               <td><?php echo $odds; ?></td>
	               <td>₹<?php echo number_format($stake, 2); ?></td>
	               <td class="<?php if($match_status=='profit'){ echo 'cl-green'; }else{ echo 'cl-red'; } ?>">₹<?php echo number_format($profit_loss, 2); ?></td>
	               <td class="<?php if($match_status=='profit'){ echo 'cl-green'; }else{ echo 'cl-red'; } ?>"><?php echo $bet_status; ?></td>
                 <td><?php echo htmlspecialchars($game_result); ?></td>
	               <td><?php echo $row['tbl_time_stamp']; ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	            <td colspan="12">No data found!</td>
	          </tr>
            <?php } ?>
              </tbody>
              <?php if (isset($footer_total_pl) && $indexVal > 1) { ?>
              <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                  <td colspan="7" style="text-align: right;">Total (this page):</td>
                  <td class="<?php echo $footer_total_pl >= 0 ? 'cl-green' : 'cl-red'; ?>">₹<?php echo number_format($footer_total_pl, 2); ?></td>
                  <td>
                    <span class="cl-green">Profit: ₹<?php echo number_format($footer_profit_amount, 2); ?></span>
                    &nbsp;|&nbsp;
                    <span class="cl-red">Loss: ₹<?php echo number_format(abs($footer_loss_amount), 2); ?></span>
                  </td>
                  <td colspan="3"></td>
                </tr>
              </tfoot>
              <?php } ?>
	        </table>
	        </div>
	        
	       <?php
	         // Count query for pagination
	         $count_base_query = "SELECT COUNT(*) as total 
	                              FROM tblmatchplayed m 
	                              LEFT JOIN tblusersdata u ON m.tbl_user_id = u.tbl_uniq_id 
	                              WHERE LOWER(m.tbl_project_name) IN ('saba sports', 'lucksport', 'lucksportgaming')";
	         
	         if($searched!=""){
	           $count_sql = $count_base_query . " AND (m.tbl_period_id LIKE '%$searched%' OR m.tbl_project_name LIKE '%$searched%' OR m.tbl_user_id LIKE '%$searched%' OR u.tbl_full_name LIKE '%$searched%')";
	         }else{
	           $count_sql = $count_base_query;
	         }
	         $count_result = mysqli_query($conn, $count_sql) or die('count failed');
	         $count_row = mysqli_fetch_assoc($count_result);
	         $total_records = $count_row['total'];
	         $total_page = ceil($total_records/ $content);

             if ($total_records > 0) {
            ?>
	         <div class="w-100 row-view j-end mg-t-15">
               <div>Page: <?php echo $page_num.' / Records:'.$total_records; ?></div>
               <?php if ($page_num > 1) { ?>
                 <a class="action-btn br-r-5 ft-sz-16 pd-10-15 mg-l-10" type="button" onclick="window.history.back()">Back</a>
               <?php } ?>
               <?php if ($page_num != $total_page) { ?>
                 <a href="?page_num=<?php echo $page_num+1; ?>" class="action-btn br-r-5 ft-sz-16 txt-deco-n pd-10-15 mg-l-10" type="button">Next</a>
               <?php } ?>
             </div>
            <?php } ?>
	        
           </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=02"></script>
<script>
function exportExcel(tableID, filename = '') {
    const table = document.getElementById(tableID);
    const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet 1" });
    return XLSX.writeFile(wb, filename || 'Export.xlsx');
}
</script>

</body>
</html>
