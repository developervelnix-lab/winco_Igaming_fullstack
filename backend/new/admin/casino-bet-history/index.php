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
  <title><?php echo $APP_NAME; ?>: Casino Bet History</title>
  <link href='../style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
    .w-100.ovflw-x-scroll {
      overflow-x: auto;
    }
  </style>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Casino Bet History</p>

        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Casino Bet History</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">           
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="text" name="searchinp" placeholder="Search Provider, Game Round ID, User ID" class="w-100 cus-inp" />
                <br><br>
                <div class="row-view j-start">
                 <input type="submit" name="submit" value="Search Records" class="action-btn br-r-5 ft-sz-18 pd-10-15">
                 <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportPDF('casino-bet-history', 'table')" type="button">Export PDF</button>
                 <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportExcel('table', 'Casino-Bet-History.xlsx')">Export Excel</button>
                </div>
            </form>
            </br></br>
            <p>Bet History Records <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <div class="w-100 ovflw-x-scroll">
            <table id="table" class="cus-tbl mg-t-10 bg-white" style="table-layout: fixed;">
	          <thead>
	          <tr>
                 <th style="width:5%">No</th>
	  	         <th style="width:12%">Provider</th>
	  	         <th style="width:12%">Game Round ID</th>
	  	         <th style="width:12%">Date & Time</th>
	  	         <th style="width:10%">Description</th>
	  	         <th style="width:8%">Bet Type</th>
	  	         <th style="width:10%">Bet Amount (Debit)</th>
	  	         <th style="width:10%">Bet Amount (Credit)</th>
	  	         <th style="width:10%">Total Balance</th>
	  	         <th style="width:10%">Profit/Loss</th>
	  	         <th style="width:11%">Game Result</th>
	          </tr>
	          </thead>
	          <tbody>
	        <?php
              $indexVal = 1;
              $total_balance = 0;
              $total_profit_loss = 0;
              $footer_profit_amount = 0;
              $footer_loss_amount = 0;
              
              if($searched!=""){
                $play_records_sql = "SELECT * FROM tblmatchplayed WHERE tbl_period_id like '%$searched%' or tbl_project_name like '%$searched%' or tbl_user_id like '%$searched%' ORDER BY id DESC LIMIT 100";
              }else{
                $play_records_sql = "
                    SELECT * 
                    FROM tblmatchplayed 
                    WHERE tbl_project_name IN (
                        'Microgaming Lobby',
                        'Ezugi Lobby',
                        'Evolution Lobby',
                        'XXXtreme Lightning Roulette',
                        'Crazy Time',
                        'Lightning Roulette',
                        'MONOPOLY Live',
                        'Crazy Pachinko',
                        'Football Studio Dice',
                        'Fan Tan',
                        'Speed Roulette',
                        'Craps',
                        'Super Andar Bahar',
                        'Playtech Lobby',
                        'Power Blackjack',
                        'Infinite Blackjack',
                        'Dead or Alive Saloon',
                        'Caribbean Stud Poker',
                        'Blackjack B',
                        'Immersive Roulette',
                        'Baccarat B'
                    )
                    ORDER BY id DESC 
                    LIMIT {$offset}, {$content}";

              }
              
              $play_records_result = mysqli_query($conn, $play_records_sql) or die('search failed');
          
              if (mysqli_num_rows($play_records_result) > 0){
                while ($row = mysqli_fetch_assoc($play_records_result)){
    
                 $match_status = $row['tbl_match_status'];
                 $bet_amount = floatval($row['tbl_match_cost']);
                 $profit_loss = floatval($row['tbl_match_profit']);
                 
                 // Bet type: Debit when bet is placed, Credit when only win (no bet)
                 $bet_type = ($bet_amount > 0) ? "Debit" : "Credit";
                 
                 // Debit amount is the bet placed
                 $debit_amount = ($bet_amount > 0) ? $bet_amount : 0.00;
                 
                 // Credit amount is the win amount (profit when positive)
                 $credit_amount = ($profit_loss > 0) ? $profit_loss : 0.00;
                 
                 // Description combines game name and transaction status
                 $description = $row['tbl_project_name'] . " - " . ucfirst($match_status);
                 
                 // Accumulate totals
                 $total_balance += floatval($row['tbl_last_acbalance']);
                 $total_profit_loss += $profit_loss;
                 if (strtolower($match_status) === 'profit') {
                   $footer_profit_amount += $profit_loss;
                 } else {
                   $footer_loss_amount += $profit_loss;
                 }
                ?>
                 <tr>
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_project_name']; ?></td>
	               <td><?php echo $row['tbl_period_id']; ?></td>
	               <td><?php echo $row['tbl_time_stamp']; ?></td>
	               <td><?php echo $description; ?></td>
	               <td><?php echo $bet_type; ?></td>
	               <td>₹<?php echo number_format($debit_amount, 2); ?></td>
	               <td>₹<?php echo number_format($credit_amount, 2); ?></td>
	               <td>₹<?php echo number_format($row['tbl_last_acbalance'], 2); ?></td>
	               <td class="<?php if($match_status=='profit'){ echo 'cl-green'; }else{ echo 'cl-red'; } ?>">₹<?php echo number_format($profit_loss, 2); ?></td>
	               <td><?php echo $row['tbl_match_result']; ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	            <td colspan="11">No data found!</td>
	          </tr>
            <?php } ?>
            </tbody>
            <?php if (isset($total_profit_loss) && $indexVal > 1) { ?>
            <tfoot>
              <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="8" style="text-align: right;">Total (this page):</td>
                <td>₹<?php echo number_format($total_balance, 2); ?></td>
                <td>
                    <span class="cl-green">Profit: ₹<?php echo number_format($footer_profit_amount, 2); ?></span>
                    &nbsp;|&nbsp;
                    <span class="cl-red">Loss: ₹<?php echo number_format(abs($footer_loss_amount), 2); ?></span>
                </td>
                <td class="<?php echo ($total_profit_loss >= 0) ? 'cl-green' : 'cl-red'; ?>">₹<?php echo number_format($total_profit_loss, 2); ?></td>
              </tr>
            </tfoot>
            <?php } ?>
	        </table>
	        </div>
	        
	       <?php
	         if($searched!=""){
	           $count_sql = "SELECT COUNT(*) as total FROM tblmatchplayed WHERE tbl_period_id like '%$searched%' or tbl_project_name like '%$searched%' or tbl_user_id like '%$searched%'";
	         }else{
	           $count_sql = "SELECT COUNT(*) as total FROM tblmatchplayed";
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