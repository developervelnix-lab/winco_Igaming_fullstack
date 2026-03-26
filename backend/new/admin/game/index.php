<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()!="true"){
    header('location:../logout-account');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Dashboard</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Control  1</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">Control Matches</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            <p>All Games</p>
            <table class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th>Game</th>
	  	         <th>Time</th>
	  	         <th>Status</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              $games_sql = "SELECT * FROM tblgamecontrols WHERE tbl_service_status='true'";
              $games_result = mysqli_query($conn, $games_sql) or die('search failed');
          
              if (mysqli_num_rows($games_result) > 0){
                while ($row = mysqli_fetch_assoc($games_result)){
               
                 $service_name = $row['tbl_service_name'];
                 $service_time = $row['tbl_service_times'];
                 $service_status = $row['tbl_service_status'];
                 $service_time_arr = explode(",", $service_time);
                ?>
                 <tr onclick="window.location.href='control-game.php?game=<?php echo $service_name; ?>'">
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $service_name; ?></td>
	               <td><?php if($service_time_arr[0]/60 < 1){ echo $service_time_arr[0].' sec'; }else{ echo ($service_time_arr[0]/60).' min'; } ?></td>
	               <td class="<?php if($service_status=='true'){ echo 'cl-green'; }else{ echo 'cl-red'; } ?>"><?php if($service_status=="true"){ echo "Active"; }else{ echo "In-Active"; } ?></td>
	             </tr>
                 
            <?php $indexVal++; }} ?>
	        </table>
           </div>
      </div>
        
    </div>

</div>

       <!--advanced settings-->
      <div class="w-100 pd-10-15 mg-t-50 bg-l-blue">
          Access Advance Game Settings?</br>
          <div class="row-view j-start mg-t-5">
            <button class="action-btn ft-sz-16 pd-10-15" onclick="restartGame('<?php echo $selectedGame; ?>')">Restart Game</button>
            
            <a class="action-btn ft-sz-16 pd-10-15 mg-l-10" href="update-game-settings.php?id=<?php echo $selectedGame; ?>">Advanced Settings</a>
          </div>
          </br>
      </div>
      
      </div>
      
      <?php include '../scripts/script-control-game.php'; ?>
   </body>
</html>