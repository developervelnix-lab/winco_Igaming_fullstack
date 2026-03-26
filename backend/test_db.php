<?php
include 'security/config.php';
$sql = "SELECT count(*) as count FROM tbl_offer_promotions";
$query = mysqli_query($conn, $sql);
if($query) {
    $row = mysqli_fetch_assoc($query);
    echo "Promotions count: " . $row['count'] . "\n";
    
    $sql_active = "SELECT count(*) as count FROM tbl_offer_promotions WHERE status = 'active'";
    $query_active = mysqli_query($conn, $sql_active);
    $row_active = mysqli_fetch_assoc($query_active);
    echo "Active promotions count: " . $row_active['count'] . "\n";
} else {
    echo "Error: Table tbl_offer_promotions might not exist. " . mysqli_error($conn) . "\n";
}
?>
