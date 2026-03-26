<?php

$resArr = array();
$resArr['status'] = "failed";
$resArr['promotions'] = array();

$sql = "SELECT * FROM tbl_offer_promotions WHERE status = 'active' ORDER BY id DESC";
$query = mysqli_query($conn, $sql);

if($query) {
    while($row = mysqli_fetch_assoc($query)) {
        array_push($resArr['promotions'], array(
            "id" => $row['id'],
            "title" => $row['title'],
            "description" => $row['description'],
            "category" => $row['category'],
            "end_date" => $row['end_date'],
            "image_path" => $row['image_path']
        ));
    }
    $resArr['status'] = "success";
}

echo json_encode($resArr);
?>
