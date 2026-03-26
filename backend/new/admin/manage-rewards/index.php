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


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: GiftCards</title>
  <link href='../style.css' rel='stylesheet'>
</head>
<body>
    
<div class="mh-100vh w-100">
    
    <div class="row-view sb-view">
        
      <?php include "../components/side-menu.php"; ?>
        
      <div class="w-100 h-100vh pd-10 br-all ovflw-y-scroll hide-native-scrollbar">
        <p>Dashboard > Manage Rewards</p>
           
        <div class="w-100 row-view j-start mg-t-20">
            <div class="menu-open-btn col-view v-center pd-5 ft-sz-25 br-r-5 bx-shdw bg-white"><i class='bx bx-menu' ></i></div>
            <h1 class="mg-l-15">All Rewards</h1> 
        </div>
           
        <div class="pd-10 mg-t-30 bx-shdw br-r-5">
            
            <a href="create-giftcard" class="txt-deco-n cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary"><i class='bx bx-plus'></i>&nbsp;Create Rewards</a>
            <a class="cur-pointer pd-5-10 mg-l-5 cl-white br-r-5 bg-primary" onclick="window.location.reload()"><i class='bx bx-refresh'></i>&nbsp;Refresh</a>
            
            <div class="w-100 ovflw-x-scroll">
            <table class="cus-tbl mg-t-10 bg-white">
	          <tr>
                 <th style="width:10%">No</th>
	  	         <th style="width:25%">Id</th>
	  	         <th>Amount</th>
	  	         <th style="width:25%">Status</th>
	  	         <th style="width:25%">Action</th>
	          </tr>
	          
	          <?php
                $indexVal = 1;
                $paginationAvailable = false;
 
                $recharge_records_sql = "SELECT * FROM tblgiftcards ORDER BY id DESC LIMIT 100";
      
                $recharge_records_result = mysqli_query($conn, $recharge_records_sql) or die('search failed');
          
                if (mysqli_num_rows($recharge_records_result) > 0){
                  $paginationAvailable = true;
                
                  while ($row = mysqli_fetch_assoc($recharge_records_result)){
    
                   $request_status = $row['tbl_giftcard_status']; ?>
                   <tr>
	                 <td><?php echo $indexVal; ?></td>
	                 <td><?php echo $row['tbl_giftcard']; ?></td>
	                 <td><?php echo $row['tbl_giftcard_bonus']; ?></td>
	                 <td class="<?php if($request_status=='true'){ echo 'cl-green'; }else{ echo 'cl-red'; } ?>"><?php if($request_status=="true"){ echo "Active"; }else{ echo "Expired"; } ?></td>
	                 <td>
	                     <a onclick="copyText('<?php echo $MAIN_DOMAIN_URL.'/redeemgiftcard/?c='.$row['tbl_giftcard']; ?>')"><i class='bx bx-link' ></i></a>
	                     <a onclick="deleteItem('<?php echo $row['tbl_giftcard']; ?>')" class="white-space-nw txt-deco-n cur-pointer pd-5-10 cl-white br-r-5 bg-primary bg-red" target="_blank">Delete</a>
	                 </td>
	               </tr>
                 
              <?php $indexVal++; }}else{ ?>
                <tr>
	             <td colspan="5">No data found!</td>
	            </tr>
              <?php } ?>
	        </table>
	        </div>
        
           </div>
           
      </div>
        
    </div>
    
</div>

<script src="../script.js?v=1"></script>
<script>
    
function deleteItem(id){
    if(confirm("Are you sure you want to delete?")){
        window.open("delete.php?id="+id);
        window.location.reload();
    }
}
    
function copyText(text){
  var textArea = document.createElement("textarea");
  textArea.value = text;
  textArea.style.opacity = "0"; 
  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    document.execCommand('copy');
    document.body.removeChild(textArea);
  } catch (err) {
    return true;
  }
  
  alert('copied!');

  return true;
}

</script>

</body>
</html>