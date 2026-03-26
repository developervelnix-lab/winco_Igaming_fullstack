<?php
define("ACCESS_SECURITY", "true");
include "../../security/config.php";
include "../../security/constants.php";
include '../access_validate.php';
include "../../mainhandler/get-period-id.php";

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('d-m-Y');
$curr_time = date('h:i:s a');
$curr_date_time = $curr_date.' '.$curr_time;

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()=="true"){
    if($accessObj->isAllowed("access_match_control")=="false"){
        echo "You're not allowed to view this page. Please grant access!";
        return;
    }
}else{
    header('location:../logout-account');
}

if (!isset($_GET["game"])) {
    echo "request block";
    return;
} else {
    $game_code = mysqli_real_escape_string($conn, $_GET["game"]);
}


$controllerType = 1;
if($game_code=="AVIATOR" || $game_code=="DICE"){
 $controllerType = 2;   
}else if($game_code=="ANDARBAHAR"){
 $controllerType = 3; 
}else if($game_code=="WHEELOCITY"){
 $controllerType = 4; 
}



$data_total_bet_count = 0;
$data_total_red_invested = 0;
$data_total_green_invested = 0;
$data_total_violet_invested = 0;
$data_total_yellow_invested = 0;
$data_total_invested = 0;

$data_total_big_invested = 0;
$data_total_small_invested = 0;
$data_total_odd_invested = 0;
$data_total_even_invested = 0;

$data_total_andar_invested = 0;
$data_total_bahar_invested = 0;
$data_total_tie_invested = 0;

$data_tiger_invested = 0;
$data_cow_invested = 0;
$data_elephant_invested = 0;
$data_crown_invested = 0;

$data_invested_in_1 = 0;
$data_invested_in_2 = 0;
$data_invested_in_3 = 0;
$data_invested_in_4 = 0;
$data_invested_in_5 = 0;
$data_invested_in_6 = 0;
$data_invested_in_7 = 0;
$data_invested_in_8 = 0;
$data_invested_in_9 = 0;
$data_invested_in_10 = 0;
$data_invested_in_11 = 0;
$data_invested_in_12 = 0;
$data_invested_in_13 = 0;
$data_invested_in_14 = 0;
$data_invested_in_15 = 0;
$data_invested_in_16 = 0;
$data_invested_in_17 = 0;
$data_invested_in_18 = 0;
$data_invested_in_19 = 0;
$data_invested_in_20 = 0;
$data_invested_in_21 = 0;
$data_invested_in_22 = 0;
$data_invested_in_23 = 0;
$data_invested_in_24 = 0;
$data_invested_in_25 = 0;
$data_invested_in_26 = 0;
$data_invested_in_27 = 0;
$data_invested_in_28 = 0;
$data_invested_in_29 = 0;
$data_invested_in_30 = 0;
$data_invested_in_31 = 0;
$data_invested_in_32 = 0;
$data_invested_in_33 = 0;
$data_invested_in_34 = 0;
$data_invested_in_35 = 0;
$data_invested_in_36 = 0;


$data_bet_set_as = "";
$data_play_time = "";
$data_active_users = "";

// $resArr['active_users'] = 0;
// $resArr['bet_set_as'] = "";


$games_sql = "SELECT * FROM tblgamecontrols WHERE tbl_service_name='{$game_code}' ";
$games_result = mysqli_query($conn, $games_sql) or die('search failed');

if (mysqli_num_rows($games_result) > 0){
    $res_data = mysqli_fetch_assoc($games_result);
    $service_value = $res_data['tbl_service_value'];
    $service_times = $res_data['tbl_service_times'];
    
    $service_value_arr = explode(",", $service_value);
    $service_times_arr = explode(",", $service_times);
    
    $data_play_time = $service_times_arr[0];
    $data_bet_set_as = $service_value_arr[2];
}else{
    echo "Invalid Project Name!";
    return;
}
    
    
$generatePeriod = new GeneratePeriod($data_play_time);
$generatePeriod->setupTimes();
$new_match_period_id =
$generatePeriod->getDateTime() . $generatePeriod->getPeriodId();

function getTimeDiff($datetime_1,$datetime_2){
 $timestamp1 = strtotime($datetime_2);
 $timestamp2 = strtotime($datetime_1);

 $diff = $timestamp2 - $timestamp1;
 return $diff;   
}

$select_sql = "SELECT * FROM tblmatchplayed WHERE tbl_period_id='{$new_match_period_id}' AND tbl_project_name='{$game_code}' ";
$select_query = mysqli_query($conn,$select_sql);
$numRows = mysqli_num_rows($select_query);

if($numRows > 0){

 while($row = mysqli_fetch_assoc($select_query)){
  $investedOn = $row['tbl_invested_on'];
  $investedAmnt = $row['tbl_match_cost'];
  $data_total_invested += $investedAmnt;
  
  if($investedOn == "A"){
      $data_total_andar_invested += $investedAmnt;
  }else if($investedOn == "B"){
      $data_total_bahar_invested += $investedAmnt;
  }else if($investedOn == "T"){
      $data_total_tie_invested += $investedAmnt;
  }
  
  
  if($investedOn == "tiger"){
      $data_tiger_invested += $investedAmnt;
  }else if($investedOn == "cow"){
      $data_cow_invested += $investedAmnt;
  }if($investedOn == "elephant"){
      $data_elephant_invested += $investedAmnt;
  }else if($investedOn == "crown"){
      $data_crown_invested += $investedAmnt;
  }
  
  if($investedOn == "1"){
      $data_invested_in_1 += $investedAmnt;
  }else if($investedOn == "2"){
      $data_invested_in_2 += $investedAmnt;
  }else if($investedOn == "3"){
      $data_invested_in_3 += $investedAmnt;
  }else if($investedOn == "4"){
      $data_invested_in_4 += $investedAmnt;
  }else if($investedOn == "5"){
      $data_invested_in_5 += $investedAmnt;
  }else if($investedOn == "6"){
      $data_invested_in_6 += $investedAmnt;
  }else if($investedOn == "7"){
      $data_invested_in_7 += $investedAmnt;
  }else if($investedOn == "8"){
      $data_invested_in_8 += $investedAmnt;
  }else if($investedOn == "9"){
      $data_invested_in_9 += $investedAmnt;
  }else if($investedOn == "10"){
      $data_invested_in_10 += $investedAmnt;
  }else if($investedOn == "11"){
      $data_invested_in_11 += $investedAmnt;
  }else if($investedOn == "12"){
      $data_invested_in_12 += $investedAmnt;
  }else if($investedOn == "13"){
      $data_invested_in_13 += $investedAmnt;
  }else if($investedOn == "14"){
      $data_invested_in_14 += $investedAmnt;
  }else if($investedOn == "14"){
      $data_invested_in_14 += $investedAmnt;
  }else if($investedOn == "15"){
      $data_invested_in_15 += $investedAmnt;
  }else if($investedOn == "16"){
      $data_invested_in_16 += $investedAmnt;
  }else if($investedOn == "17"){
      $data_invested_in_17 += $investedAmnt;
  }else if($investedOn == "18"){
      $data_invested_in_18 += $investedAmnt;
  }else if($investedOn == "19"){
      $data_invested_in_19 += $investedAmnt;
  }else if($investedOn == "20"){
      $data_invested_in_20 += $investedAmnt;
  }else if($investedOn == "21"){
      $data_invested_in_21 += $investedAmnt;
  }else if($investedOn == "22"){
      $data_invested_in_22 += $investedAmnt;
  }else if($investedOn == "23"){
      $data_invested_in_23 += $investedAmnt;
  }else if($investedOn == "24"){
      $data_invested_in_24 += $investedAmnt;
  }else if($investedOn == "25"){
      $data_invested_in_25 += $investedAmnt;
  }else if($investedOn == "26"){
      $data_invested_in_26 += $investedAmnt;
  }else if($investedOn == "27"){
      $data_invested_in_27 += $investedAmnt;
  }else if($investedOn == "28"){
      $data_invested_in_28 += $investedAmnt;
  }else if($investedOn == "29"){
      $data_invested_in_29 += $investedAmnt;
  }else if($investedOn == "30"){
      $data_invested_in_30 += $investedAmnt;
  }else if($investedOn == "31"){
      $data_invested_in_31 += $investedAmnt;
  }else if($investedOn == "32"){
      $data_invested_in_32 += $investedAmnt;
  }else if($investedOn == "33"){
      $data_invested_in_33 += $investedAmnt;
  }else if($investedOn == "34"){
      $data_invested_in_34 += $investedAmnt;
  }else if($investedOn == "35"){
      $data_invested_in_35 += $investedAmnt;
  }else if($investedOn == "36"){
      $data_invested_in_36 += $investedAmnt;
  }
  
  if($investedOn == "b"){
      $data_total_big_invested += $investedAmnt;
  }else if($investedOn == "s"){
      $data_total_small_invested += $investedAmnt;
  }else if($investedOn == "o"){
      $data_total_odd_invested += $investedAmnt;
  }else if($investedOn == "e"){
      $data_total_even_invested += $investedAmnt;
  }
  
  if ($investedOn == "green" || $investedOn == "1" || $investedOn == "3" || $investedOn == "7" || $investedOn == "9"){
    $data_total_green_invested += $investedAmnt;
  }else if ($investedOn == "red" || $investedOn == "2" || $investedOn == "4" || $investedOn == "6" || $investedOn == "8"){
    $data_total_red_invested += $investedAmnt;
  }else if ($investedOn == "yellow"){
    $data_total_yellow_invested += $investedAmnt;
  }else if ($investedOn == "violet" || $investedOn == "0" || $investedOn == "5"){
    $data_total_violet_invested += $investedAmnt;
  }
  
 }
 
 $data_total_bet_count = $numRows;
}

    
// calculate active users
$select_sql = "SELECT * FROM tblusersdata WHERE tbl_account_status='true' AND tbl_last_active_date='{$curr_date}' ";
$select_query = mysqli_query($conn,$select_sql);
$numRows = mysqli_num_rows($select_query);

if($numRows > 0){
  $tempActiveUsers = 0;
  while($row = mysqli_fetch_assoc($select_query)){
     $activeDateTime = $row['tbl_last_active_date'].' '.$row['tbl_last_active_time'];
    
    if(getTimeDiff($curr_date_time,$activeDateTime) <= 120){
      $tempActiveUsers++;
    }     
  }

  $data_active_users = (string) $tempActiveUsers;
}

// set game data
$generatePeriod = new GeneratePeriod($data_play_time);
$generatePeriod->setupTimes();
$new_match_period_id =
        $generatePeriod->getDateTime() . $generatePeriod->getPeriodId();
$match_remaining_seconds = $generatePeriod->getRemainingSec();





// $content = 15;
// if (isset($_GET["page_no"])) {
//     $page_no = $_GET["page_no"];
//     $offset = ($page_no - 1) * $content;
// } else {
//     $page_no = 1;
//     $offset = ($page_no - 1) * $content;
// }
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include "../header_contents.php" ?>
    <title><?php echo $APP_NAME; ?>: Control Game</title>
    <link href='../style.css' rel='stylesheet'>
   </head>
   <body>
   <div class="mh-100vh w-100 col-view">
       
      <input type="text" id="in_game_code" value="<?php echo $game_code; ?>" hidden>
          
      <div class="w-100 row-view sb-view pd-15 bg-primary">
        <h3 class="dpl-flx a-center cl-white" onclick="window.history.back()">
          <i class='bx bx-left-arrow-alt ft-sz-30'></i>
          
          <div class="col-view mg-l-10">
            Control Game
            <span class="ft-sz-13 mg-t-5" id="match_details_view"><?php echo $new_match_period_id; ?></span>
          </div>
        </h3>
        
        <div class="pd-10-15 br-r-5 bg-white cur-pointer set_bet_btn">
            <i class='bx bx-lock-open-alt'></i>&nbsp;<?php if($data_bet_set_as!="none"){ echo $data_bet_set_as; }else{ echo "Set Bet"; } ?>
        </div>
      </div>
      
      <div class="pd-15">
          
        <div class="res-g-v-4 g-gap-10-20">

          <div class="row-view sb-view w-100 pd-15 br-r-5 bg-white bx-shdw">
            <div class="col-view">
                <p class="ft-sz-25" id="match_timer_tv"><?php echo $match_remaining_seconds; ?></p>
                <p class="ft-sz-13 mg-t-5">Match Timer</p>
            </div>
            
            <i class='bx bx-time-five bg-l-blue ft-sz-25 pd-10 br-r-5'></i>
          </div>
        
          <div class="row-view sb-view w-100 pd-15 br-r-5 bg-white bx-shdw">
            <div class="col-view">
                <p class="ft-sz-25" id="match_active_users_tv"><?php echo $data_active_users; ?></p>
                <p class="ft-sz-13 mg-t-5">Active Users</p>
            </div>
            
            <i class='bx bx-user bg-l-blue ft-sz-25 pd-10 br-r-5'></i>
          </div>
        
          <div class="row-view sb-view w-100 pd-15 br-r-5 bg-white bx-shdw">
            <div class="col-view">
                <p class="ft-sz-25" id="match_total_bet_tv"><?php echo $data_total_bet_count; ?></p>
                <p class="ft-sz-13 mg-t-5">Total No. Bet</p>
            </div>
            
            <i class='bx bx-wallet bg-l-blue ft-sz-25 pd-10 br-r-5'></i>
          </div>
        
          <div class="row-view sb-view w-100 pd-15 br-r-5 bg-white bx-shdw">
            <div class="col-view">
                <p class="ft-sz-25" id="match_total_invested_tv">₹<?php echo $data_total_invested; ?></p>
                <p class="ft-sz-13 mg-t-5">Total Amount</p>
            </div>
            
            <i class='bx bx-wallet bg-l-blue ft-sz-25 pd-10 br-r-5'></i>
          </div>
        
        </div>
        
          
        <p class="mg-t-30"><i class='bx bx-receipt'></i>&nbsp;More Details:</p>
        <table class="cus-tbl">
          <tr>
            <th style="width: 60%"></th>
            <th></th>
          </tr>
          
          <?php if($controllerType ==1){ ?>
          
          <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-red">Red</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_red_invested; ?></td>
          </tr>
          
          <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-green">Green</span></td>
            <td id="match_green_invested_tv">₹<?php echo $data_total_green_invested; ?></td>
          </tr>
          
          <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-violet">Violet</span></td>
            <td id="match_violet_invested_tv">₹<?php echo $data_total_violet_invested; ?></td>
          </tr>
          
          <?php }else if($controllerType==3){ ?>
          
           <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-blue">Andar</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_andar_invested; ?></td>
           </tr>
           
           <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-red">Bahar</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_bahar_invested; ?></td>
           </tr>
           
           <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-yellow">Tie</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_tie_invested; ?></td>
           </tr>
          
          <?php }else if($controllerType==4){ ?>
          
           <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-yellow">Yellow</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_yellow_invested; ?></td>
           </tr>
           
           <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-red">Red</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_red_invested; ?></td>
           </tr>
           
           <tr>
            <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-green">Green</span></td>
            <td id="match_red_invested_tv">₹<?php echo $data_total_green_invested; ?></td>
           </tr>
           
           <?php if($data_tiger_invested > 0){ ?>
             <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-blue">Tiger</span></td>
              <td id="match_red_invested_tv">₹<?php echo $data_tiger_invested; ?></td>
             </tr>
           <?php } ?>
           
           <?php if($data_cow_invested > 0){ ?>
             <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-blue">Cow</span></td>
              <td id="match_red_invested_tv">₹<?php echo $data_cow_invested; ?></td>
             </tr>
           <?php } ?>
           
           <?php if($data_elephant_invested > 0){ ?>
             <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-blue">Elephant</span></td>
              <td id="match_red_invested_tv">₹<?php echo $data_elephant_invested; ?></td>
             </tr>
           <?php } ?>
           
           <?php if($data_crown_invested > 0){ ?>
             <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-blue">Crown</span></td>
              <td id="match_red_invested_tv">₹<?php echo $data_crown_invested; ?></td>
             </tr>
           <?php } ?>
          
          <?php } ?>
          
          
          <?php if($data_total_big_invested > 0){ ?>
            <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-orange">Big</span></td>
              <td>₹<?php echo $data_total_big_invested; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_total_small_invested > 0){ ?>
            <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-blue">Small</span></td>
              <td>₹<?php echo $data_total_small_invested; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_total_odd_invested > 0){ ?>
            <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-red">Odd</span></td>
              <td>₹<?php echo $data_total_odd_invested; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_total_even_invested > 0){ ?>
            <tr>
              <td>Invested In <span class="pd-5-10 ft-sz-13 br-r-5 cl-white bg-green">Even</span></td>
              <td>₹<?php echo $data_total_even_invested; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_1 > 0){ ?>
            <tr>
              <td>Invested In 1</td>
              <td>₹<?php echo $data_invested_in_1; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_2 > 0){ ?>
            <tr>
              <td>Invested In 2</td>
              <td>₹<?php echo $data_invested_in_2; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_3 > 0){ ?>
            <tr>
              <td>Invested In 3</td>
              <td>₹<?php echo $data_invested_in_3; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_4 > 0){ ?>
            <tr>
              <td>Invested In 4</td>
              <td>₹<?php echo $data_invested_in_4; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_5 > 0){ ?>
            <tr>
              <td>Invested In 5</td>
              <td>₹<?php echo $data_invested_in_5; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_6 > 0){ ?>
            <tr>
              <td>Invested In 6</td>
              <td>₹<?php echo $data_invested_in_6; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_7 > 0){ ?>
            <tr>
              <td>Invested In 7</td>
              <td>₹<?php echo $data_invested_in_7; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_8 > 0){ ?>
            <tr>
              <td>Invested In 8</td>
              <td>₹<?php echo $data_invested_in_8; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_9 > 0){ ?>
            <tr>
              <td>Invested In 9</td>
              <td>₹<?php echo $data_invested_in_9; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_10 > 0){ ?>
            <tr>
              <td>Invested In 10</td>
              <td>₹<?php echo $data_invested_in_10; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_11 > 0){ ?>
            <tr>
              <td>Invested In 11</td>
              <td>₹<?php echo $data_invested_in_11; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_12 > 0){ ?>
            <tr>
              <td>Invested In 12</td>
              <td>₹<?php echo $data_invested_in_12; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_13 > 0){ ?>
            <tr>
              <td>Invested In 13</td>
              <td>₹<?php echo $data_invested_in_13; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_14 > 0){ ?>
            <tr>
              <td>Invested In 14</td>
              <td>₹<?php echo $data_invested_in_14; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_15 > 0){ ?>
            <tr>
              <td>Invested In 15</td>
              <td>₹<?php echo $data_invested_in_15; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_16 > 0){ ?>
            <tr>
              <td>Invested In 16</td>
              <td>₹<?php echo $data_invested_in_16; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_17 > 0){ ?>
            <tr>
              <td>Invested In 17</td>
              <td>₹<?php echo $data_invested_in_17; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_18 > 0){ ?>
            <tr>
              <td>Invested In 18</td>
              <td>₹<?php echo $data_invested_in_18; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_19 > 0){ ?>
            <tr>
              <td>Invested In 19</td>
              <td>₹<?php echo $data_invested_in_19; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_20 > 0){ ?>
            <tr>
              <td>Invested In 20</td>
              <td>₹<?php echo $data_invested_in_20; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_21 > 0){ ?>
            <tr>
              <td>Invested In 21</td>
              <td>₹<?php echo $data_invested_in_21; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_22 > 0){ ?>
            <tr>
              <td>Invested In 22</td>
              <td>₹<?php echo $data_invested_in_22; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_23 > 0){ ?>
            <tr>
              <td>Invested In 23</td>
              <td>₹<?php echo $data_invested_in_23; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_24 > 0){ ?>
            <tr>
              <td>Invested In 24</td>
              <td>₹<?php echo $data_invested_in_24; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_25 > 0){ ?>
            <tr>
              <td>Invested In 25</td>
              <td>₹<?php echo $data_invested_in_25; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_26 > 0){ ?>
            <tr>
              <td>Invested In 26</td>
              <td>₹<?php echo $data_invested_in_26; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_27 > 0){ ?>
            <tr>
              <td>Invested In 27</td>
              <td>₹<?php echo $data_invested_in_27; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_28 > 0){ ?>
            <tr>
              <td>Invested In 28</td>
              <td>₹<?php echo $data_invested_in_28; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_29 > 0){ ?>
            <tr>
              <td>Invested In 29</td>
              <td>₹<?php echo $data_invested_in_29; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_30 > 0){ ?>
            <tr>
              <td>Invested In 30</td>
              <td>₹<?php echo $data_invested_in_30; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_31 > 0){ ?>
            <tr>
              <td>Invested In 31</td>
              <td>₹<?php echo $data_invested_in_31; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_32 > 0){ ?>
            <tr>
              <td>Invested In 32</td>
              <td>₹<?php echo $data_invested_in_32; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_33 > 0){ ?>
            <tr>
              <td>Invested In 33</td>
              <td>₹<?php echo $data_invested_in_33; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_34 > 0){ ?>
            <tr>
              <td>Invested In 34</td>
              <td>₹<?php echo $data_invested_in_34; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_35 > 0){ ?>
            <tr>
              <td>Invested In 35</td>
              <td>₹<?php echo $data_invested_in_35; ?></td>
            </tr>
          <?php } ?>
          
          <?php if($data_invested_in_36 > 0){ ?>
            <tr>
              <td>Invested In 36</td>
              <td>₹<?php echo $data_invested_in_36; ?></td>
            </tr>
          <?php } ?>
          
        </table>
        
        <br>
        <table class="cus-tbl mg-t-10 bg-white">
	          <tr style="background: rgba(0,0,0,0.1);">
                 <th style="width:10%">No</th>
	  	         <th>Match</th>
	  	         <th style="width:15%">Result</th>
	          </tr>
	          
	        <?php
              $indexVal = 1;
              
              $match_records_sql = "SELECT * FROM tblmatchrecords WHERE tbl_project_name='{$game_code}' ORDER BY id DESC LIMIT 5";   
              $match_records_result = mysqli_query($conn, $match_records_sql) or die('search failed');
          
              if (mysqli_num_rows($match_records_result) > 0){
                while ($row = mysqli_fetch_assoc($match_records_result)){

                ?>
                 <tr>
	               <td><?php echo $indexVal; ?></td>
	               <td><?php echo $row['tbl_period_id']; ?></td>
	               <td><?php echo $row['tbl_match_result']; ?></td>
	             </tr>
                 
            <?php $indexVal++; }}else{ ?>
              <tr>
	               <td colspan="3">No data found!</td>
	          </tr>
            <?php } ?>
	        </table>
        
      </div>
         
      <br>
      <div class="ps-fx w-100 h-100 col-view v-center bg-l-black z-i-100 manual_bet_control_view hide_view">
        <div class="res-w-320 pd-5-10 br-r-5 bg-white manual_bet_dialog hide_view">
            <h3>Setup Manual Bet</h3>
            <br><br>
            
            <?php if($controllerType==1){ ?>
             <div class=<?php if($controllerType!=1){ echo "hide_view"; } ?>>
               <input type="radio" id="rlabel" name="select_bet_list" value="red">
               <label for="rlabel" class="pd-5-10 cl-white br-r-5 bg-red">Red</label>&nbsp;&nbsp;
               <input type="radio" id="glabel" name="select_bet_list" value="green">
               <label for="glabel" class="pd-5-10 cl-white br-r-5 bg-green">Green</label>
               <br><br>
               <input type="radio" id="rvlabel" name="select_bet_list" value="redviolet">
               <label for="rvlabel" class="pd-5-10 cl-white br-r-5 bg-red-violet-back">Red -Violet</label>&nbsp;
               <input type="radio" id="gvlabel" name="select_bet_list" value="greenviolet">
               <label for="gvlabel" class="pd-5-10 cl-white br-r-5 bg-green-violet-back">Green -Violet</label>
               <br><br>
               <input type="radio" id="onelabel" name="select_bet_list" value="1">
               <label for="onelabel" class="pd-5-10 cl-white br-r-5 bg-green">1</label>&nbsp;&nbsp;
               <input type="radio" id="twolabel" name="select_bet_list" value="2">
               <label for="twolabel" class="pd-5-10 cl-white br-r-5 bg-red">2</label>
               <br><br>
               <input type="radio" id="threelabel" name="select_bet_list" value="3">
               <label for="threelabel" class="pd-5-10 cl-white br-r-5 bg-green">3</label>&nbsp;&nbsp;
               <input type="radio" id="fourlabel" name="select_bet_list" value="4">
               <label for="fourlabel" class="pd-5-10 cl-white br-r-5 bg-red">4</label>&nbsp;&nbsp;
               <input type="radio" id="sixlabel" name="select_bet_list" value="6">
               <label for="sixlabel" class="pd-5-10 cl-white br-r-5 bg-red">6</label>
               <br><br>
               <input type="radio" id="sevenlabel" name="select_bet_list" value="7">
               <label for="sevenlabel" class="pd-5-10 cl-white br-r-5 bg-green">7</label>&nbsp;&nbsp;
               <input type="radio" id="eightlabel" name="select_bet_list" value="8">
               <label for="eightlabel" class="pd-5-10 cl-white br-r-5 bg-red">8</label>&nbsp;&nbsp;
               <input type="radio" id="ninelabel" name="select_bet_list" value="9">
               <label for="ninelabel" class="pd-5-10 cl-white br-r-5 bg-green">9</label>
             </div>
            <?php } if($controllerType==2){ ?>
            
             <div>
                <input type="text" name="in_bet_value" placeholder="Enter here.." class="w-100 cus-inp ">
             </div>
             
            <?php } if($controllerType==3){ ?>
            
             <div>
               <input type="radio" id="andarlabel" name="select_bet_list" value="A">
               <label for="andarlabel" class="pd-5-10 cl-white br-r-5 bg-blue">Andar</label>&nbsp;&nbsp;
               <input type="radio" id="baharlabel" name="select_bet_list" value="B">
               <label for="baharlabel" class="pd-5-10 cl-white br-r-5 bg-red">Bahar</label>
               <br><br>
               <input type="radio" id="tielabel" name="select_bet_list" value="T">
               <label for="tielabel" class="pd-5-10 cl-white br-r-5 bg-yellow">Tie</label>
             </div>
             
            <?php } if($controllerType==4 || $controllerType==5){ ?>
            
              <input type="radio" id="tigerlabel" name="select_bet_list" value="tiger">
              <label for="tigerlabel" class="pd-5-10 cl-white br-r-5 bg-green">Tiger</label>&nbsp;&nbsp;
              <input type="radio" id="elephantlabel" name="select_bet_list" value="elephant">
              <label for="elephantlabel" class="pd-5-10 cl-white br-r-5 bg-green">Elephant</label>
              <br><br>
              <input type="radio" id="cowlabel" name="select_bet_list" value="cow">
              <label for="cowlabel" class="pd-5-10 cl-white br-r-5 bg-green">Cow</label>
              <input type="radio" id="crownlabel" name="select_bet_list" value="crown">
              <label for="crownlabel" class="pd-5-10 cl-white br-r-5 bg-green">Crown</label>
              <input type="radio" id="redlabel" name="select_bet_list" value="red">
              <label for="redlabel" class="pd-5-10 cl-white br-r-5 bg-red">Red</label>
              <br><br>
              <input type="radio" id="greenlabel" name="select_bet_list" value="green">
              <label for="greenlabel" class="pd-5-10 cl-white br-r-5 bg-green">Green</label>
              <input type="radio" id="yellowlabel" name="select_bet_list" value="yellow">
              <label for="yellowlabel" class="pd-5-10 cl-white br-r-5 bg-yellow">Yellow</label>
              <div>
                  
                <input type="text" name="in_bet_value" placeholder="Enter here.." class="w-100 cus-inp mg-t-10">
              </div>
             
            <?php } ?>
            
            </br>
            <div class="h-line-view"></div>
            <br>
            <button class="pd-10-20 br-n br-r-5 ft-sz-18 cl-white bg-primary set_bet_option control_btn">Set Bet</button>
            <button class="pd-10-15 br-n br-r-5 ft-sz-18 cl-white bg-red bet_dismiss_btn dismiss_btn">Close</button>
        </div>
      </div>
      
      
      </div>
      
      <div class="w-100 pd-10-15 bg-l-blue">
          </br>
          Would you like to restart the game?</br>
          <div class="mg-t-5">
            <button class="action-btn ft-sz-16 pd-10-15" onclick="restartGame('<?php echo $game_code; ?>')">Restart Game</button>

          </div>
          </br>
      </div>
      
      </div>
      <script>
         var gameTimer = null,RUNNING_TIME=0,PERIOD_ID="";
         let match_details_view = document.querySelector("#match_details_view");
         let in_game_code = document.querySelector("#in_game_code");
         let manual_bet_tv = document.querySelector("#manual_bet_tv");
         let match_timer_tv = document.querySelector("#match_timer_tv");
         
         let match_active_users_tv = document.querySelector("#match_active_users_tv");
         let manual_bet_btn = document.querySelector(".manual_bet_btn");
         let manual_bet_control_view = document.querySelector(".manual_bet_control_view");
         let manual_bet_dialog = document.querySelector(".manual_bet_dialog");
         let input_dialog = document.querySelector(".input_dialog");
         let set_bet_btn = document.querySelector(".set_bet_btn");
         let set_bet_option = document.querySelector(".set_bet_option");
         let bet_dismiss_btn = document.querySelector(".bet_dismiss_btn");
         let in_dismiss_btn = document.querySelector(".in_dismiss_btn");
         let match_contants_view = document.querySelector(".match_contants_view");
         let update_controlls_btn = document.querySelector(".update_controlls_btn");
         
         let in_high_profit = document.querySelector("#in_high_profit");
         let in_trigger_amnt = document.querySelector("#in_trigger_amnt");
         
         let period_id_tv = document.querySelector("#period_id_tv");
         let open_price_tv = document.querySelector("#open_price_tv");
         let tax_amount_tv = document.querySelector("#tax_amount_tv");
         let high_profit_tv = document.querySelector("#high_profit_tv");
         
         let match_red_invested_tv = document.querySelector("#match_red_invested_tv");
         let match_green_invested_tv = document.querySelector("#match_green_invested_tv");
         let match_violet_invested_tv = document.querySelector("#match_violet_invested_tv");
         let match_total_invested_tv = document.querySelector("#match_total_invested_tv");
         let match_color_count_tv = document.querySelector("#match_color_count_tv");
         let update_rule_btn = document.querySelector(".update_rule_btn");
         let in_match_rules = document.querySelector("#in_match_rules");
         
         function setBetOptionView(data){
           if(data!="false" && data!=""){
              if(data=="red"){
                 set_bet_btn.innerHTML = "<i class='bx bxs-lock-alt' ></i>&nbsp;Red";
              }else if(data=="green"){
                 set_bet_btn.innerHTML = "<i class='bx bxs-lock-alt' ></i>&nbsp;Green";
              }else if(data=="greenviolet"){
                 set_bet_btn.innerHTML = "<i class='bx bxs-lock-alt' ></i>&nbsp;Green Violet";
              }else if(data=="redviolet"){
                 set_bet_btn.innerHTML = "<i class='bx bxs-lock-alt' ></i>&nbsp;Red Violet";
              }else if(data!="none"){
                 set_bet_btn.innerHTML = "<i class='bx bxs-lock-alt' ></i>&nbsp;"+data;  
              }else{
                 set_bet_btn.innerHTML = "<i class='bx bx-lock-open-alt' ></i>&nbsp;Set Bet";         
              }
           }else{
            //   bet_set_as_tv.innerHTML = "Not set";
           }
         }
         
        function setUpTimer(totalSeconds){
          clearTimerInterval();
           
          function padTo2Digits(num) {
           return num.toString().padStart(2, '0');
          }
    
          function splitIntoArray(num) {
  	       return Array.from(String(num), Number);
          }

    	  gameTimer = setInterval(function() {
	    
           match_timer_tv.innerHTML = totalSeconds;
        
           if(totalSeconds <= 0){
            clearTimerInterval();
            window.location.reload();
           }else if(totalSeconds%5===0){
            window.location.reload();  
           }
	   
	       totalSeconds--;
        
  	      }, 1000);
           
        }
         
        set_bet_option.addEventListener("click",()=>{
           var color_list_option = document.getElementsByName('select_bet_list');
           var in_bet_value = document.getElementsByName('in_bet_value');
           
           let selected_bet = "";
           for(i = 0; i < color_list_option.length; i++) {
               if(color_list_option[i].checked)
               selected_bet = color_list_option[i].value;
           }
           
           if(in_bet_value.length > 0 && in_bet_value[0].value!=""){
              selected_bet = in_bet_value[0].value;
           }
           
           console.log(selected_bet);
           
           if(in_game_code.value=="AVIATOR" && Number(selected_bet) > 8){
               return;
           }else if(in_game_code.value=="DICE" && (Number(selected_bet) > 95 || Number(selected_bet) < 3)){
               return;
           }else if(in_game_code.value=="WHEELOCITY" && (Number(selected_bet) > 36 || Number(selected_bet) < 0)){
               return;
           }
         
           setManualBet(selected_bet);
        });
         
        set_bet_btn.addEventListener("click",()=>{
           manual_bet_control_view.classList.remove("hide_view");
           manual_bet_dialog.classList.remove("hide_view");
        });
         
        bet_dismiss_btn.addEventListener("click",()=>{
           manual_bet_control_view.classList.add("hide_view");
           manual_bet_dialog.classList.add("hide_view");
        });
         
        document.addEventListener("visibilitychange", () => {
           if (document.hidden) {
              clearTimerInterval();
           } else {
              window.location.reload();
           }
        });
         
        function setManualBet(setBet){
           async function requestFile() {
               try {
                   set_bet_option.classList.remove('hide_view');
                   const response =
                       await fetch("set-manual-bet.php?set-bet=" + setBet+"&project=<?php echo $game_code; ?>", {
                           method: "GET"
                       });
         
                   const resp = await response.json();
         
                   if (resp.status_code == "success") {
                     setBetOptionView(setBet);
                     manual_bet_control_view.classList.add("hide_view");
                     manual_bet_dialog.classList.add("hide_view");
                   } else {
                     alert('Oops! Failed to set bet!');
                   }
         
               } catch (error) {
                  set_bet_option.classList.remove('hide_view');
                  alert('Oops! Something went wrong! Failed to update!');  
               }
           }
         
           if (setBet!="" && setBet!=undefined) {
               set_bet_option.classList.add('hide_view');
               requestFile();
           }
        }
         
        function clearTimerInterval(){
          if(gameTimer!=null){
            clearInterval(gameTimer);
          }  
        }

        
        function restartGame(project_name){
          if(confirm("Are you sure you want to restart the game?")){
            window.open("restart-game.php?project="+project_name);
          }
        }
        
        window.addEventListener("load", (event) => {
            setUpTimer(match_timer_tv.innerHTML);
        });
         
      </script>
   </body>
</html>

