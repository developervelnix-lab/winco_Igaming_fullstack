<?php
if(defined("ACCESS_SECURITY")){
 date_default_timezone_set('Asia/Kolkata');
 $is_db_connected = "false";
 // database config
 $server_db = "localhost";
 $hostname_db = "winco";
 $username_db = "root";
 $password_db = "";

 try{
    if ($conn = mysqli_connect($server_db ,$username_db, $password_db, $hostname_db ))
    {
        $is_db_connected = "true";
    }
    else
    {
        throw new Exception('Unable to connect');
    }
 }catch (Throwable $e) {
    // Handle error
    echo $e->getMessage();
    echo "Please setup extension properly.";
  }   
    
}else{
 echo "permission denied!";
 return;
}
?>