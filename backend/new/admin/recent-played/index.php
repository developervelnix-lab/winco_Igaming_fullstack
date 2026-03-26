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
  <title><?php echo $APP_NAME; ?>: Recently Played</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Recently Played</p>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Recently Played</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">           
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="text" name="searchinp" placeholder="Search Id, Game" class="w-100 cus-inp" />
                <br><br>
                <div class="row-view j-start">
                 <input type="submit" name="submit" value="Search Records" class="action-btn br-r-5 ft-sz-18 pd-10-15"> 
                 <button class="filter_btn action-btn br-r-5 ft-sz-15 pd-10-15 mg-l-10" onclick="exportPDF('recent-played', 'table')" type="button">Export PDF</button>                 
                </div>
            </form>
            </br></br>
            <p>Recent Records <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a></p>
            <table id="table" class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">Id</th>
	  	         <th>Game</th>
	  	         <th>Bet Amount</th>
	  	         <th>Profit Amount</th>	  	
	  	         <th style="width:15%">Status</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              
              if($searched!=""){
                $play_records_sql = "SELECT * FROM tblmatchplayed WHERE tbl_period_id like '%$searched%' or tbl_project_name like '%$searched%' or tbl_user_id like '%$searched%' ORDER BY id DESC LIMIT 100";
              }else{
                $play_records_sql = "SELECT * FROM tblmatchplayed ORDER BY id DESC LIMIT {$offset},{$content}";
              }
              
              $play_records_result = mysqli_query($conn, $play_records_sql) or die('search failed');
          
              if (mysqli_num_rows($play_records_result) > 0){
                while ($row = mysqli_fetch_assoc($play_records_result)){
    
                 $match_status = $row['tbl_match_status'];
                ?>
                 <tr>
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_user_id']; ?></td>
	               <td><?php echo $row['tbl_project_name']; ?></td>
	               <td>₹<?php echo $row['tbl_match_cost']; ?></td>
	               <td>₹<?php echo $row['tbl_match_profit']; ?></td>
	               <td class="<?php if($match_status=='profit'){ echo 'cl-green'; }else{ echo 'cl-red'; } ?>"><?php if($match_status=="profit"){ echo "Profit"; }else{ echo "Loss"; } ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	            <td colspan="3">No data found!</td>
	            <td></td>
	          </tr>
            <?php } ?>
	        </table>
	        
	       <?php
	         $play_records_sql = "SELECT * FROM tblmatchplayed";
             $play_records_result = mysqli_query($conn, $play_records_sql) or die('fetch failed');

             if (mysqli_num_rows($play_records_result) > 0) {
               $total_records = mysqli_num_rows($play_records_result);
               $total_page = ceil($total_records/ $content);
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

</body>
</html>