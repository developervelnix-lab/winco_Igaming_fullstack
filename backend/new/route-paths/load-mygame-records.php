<?php
$user_id = "";
$project_name = "";
$page_num = 1;
$content = 40;


if(isset($_GET['USER_ID'])){
  $user_id = mysqli_real_escape_string($conn,$_GET['USER_ID']);
}


if($user_id!=""){

  $headerObj = new RequestHeaders();
  $headerObj -> checkAllHeaders();
  $secret_key = $headerObj -> getAuthorization();
   
  if($secret_key=="null" || $secret_key==""){
    $resArr['status_code'] = "authorization_error";
    echo json_encode($resArr);
    return;
  }
   
}else{
    $resArr['status_code'] = "invalid_params";
    echo json_encode($resArr);
    return;
}


$select_user_sql = "SELECT tbl_balance,tbl_account_status FROM tblusersdata WHERE tbl_uniq_id = '{$user_id}' AND tbl_auth_secret ='{$secret_key}' AND tbl_account_status ='true' ";
$select_user_query = mysqli_query($conn,$select_user_sql);

if (mysqli_num_rows($select_user_query) > 0) {

$offset = ($page_num-1)*$content;
$select_sql = "SELECT * FROM tblmatchplayed WHERE tbl_user_id='{$user_id}' ORDER BY id DESC LIMIT {$offset},{$content} ";
$select_query = mysqli_query($conn,$select_sql);
    
while($row = mysqli_fetch_assoc($select_query)){
  $index['r_match_name'] = $row['tbl_project_name'];
  $index['r_match_amount'] = $row['tbl_match_cost'];
  $index['r_match_bet'] = $row['tbl_invested_on'];
  $index['r_match_profit'] = $row['tbl_match_profit'];
  $index['r_match_status'] = $row['tbl_match_status'];
  $index['r_date'] = substr($row['tbl_time_stamp'], 0, 5);
  $index['r_time'] = substr($row['tbl_time_stamp'], 11);

  array_push($resArr['data'], $index);
}

$numRows = mysqli_num_rows($select_query);

if($page_num>1){
  if($numRows > 0){ 
    $resArr['status_code'] = "success";
  }else{
    $resArr['status_code'] = "no-more";
  }

  $resArr['pagination'] = "true";
}else{
  if($numRows > 0){ 
    $resArr['status_code'] = "success";
  }else{
    $resArr['status_code'] = "no-records-found";
  }

  if($numRows >= $content){
    $resArr['pagination'] = "true";
  }
}

}else{
   $resArr['status_code'] = "authorization_error"; 
}

mysqli_close($conn);
echo json_encode($resArr);
?>