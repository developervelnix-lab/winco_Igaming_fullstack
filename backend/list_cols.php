<?php
require 'd:/xampp/htdocs/security/config.php';
$res = mysqli_query($conn, 'SHOW COLUMNS FROM tblusersdata');
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
