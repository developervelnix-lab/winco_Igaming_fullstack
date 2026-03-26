<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()!="true" || $accessObj->isAllowed("access_settings")=="false"){
    header('location:../logout-account');
    exit;
}

$action = $_POST['action_type'] ?? $_GET['action_type'] ?? '';

function upsert_service($conn, $name, $value) {
    $chk = mysqli_query($conn, "SELECT * FROM tblservices WHERE tbl_service_name='$name'");
    if(mysqli_num_rows($chk) > 0) {
        mysqli_query($conn, "UPDATE tblservices SET tbl_service_value='$value' WHERE tbl_service_name='$name'");
    } else {
        mysqli_query($conn, "INSERT INTO tblservices (tbl_service_name, tbl_service_value) VALUES ('$name', '$value')");
    }
}

if ($action == "update_logo") {
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $upload_dir = "../uploads/branding/";
        $filename = "logo_" . time() . "_" . basename($_FILES['site_logo']['name']);
        $target_file = $upload_dir . $filename;
        $db_path = "admin/uploads/branding/" . $filename;

        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_file)) {
            if (mysqli_query($conn, "UPDATE tblservices SET tbl_service_value = '$db_path' WHERE tbl_service_name = 'SITE_LOGO_URL'")) {
                header("Location: site-branding.php?msg=Logo updated successfully");
            } else {
                $err = urlencode(mysqli_error($conn));
                header("Location: site-branding.php?err=Database error: $err");
            }
        } else {
            header("Location: site-branding.php?err=File upload to directory failed. Check permissions.");
        }
    }
}

else if ($action == "update_contacts") {
    $tg = mysqli_real_escape_string($conn, $_POST['telegram_url']);
    $wa = mysqli_real_escape_string($conn, $_POST['whatsapp_num']);
    $sup = mysqli_real_escape_string($conn, $_POST['support_url']);

    $q1 = mysqli_query($conn, "UPDATE tblservices SET tbl_service_value = '$tg' WHERE tbl_service_name = 'TELEGRAM_URL'");
    $q2 = mysqli_query($conn, "UPDATE tblservices SET tbl_service_value = '$wa' WHERE tbl_service_name = 'CONTACT_WHATSAPP'");
    $q3 = mysqli_query($conn, "UPDATE tblservices SET tbl_service_value = '$sup' WHERE tbl_service_name = 'CONTACT_SUPPORT_URL'");

    if ($q1 && $q2 && $q3) {
        header("Location: site-branding.php?msg=Contacts updated");
    } else {
        $err = urlencode(mysqli_error($conn));
        header("Location: site-branding.php?err=Update failed: $err");
    }
}

else if ($action == "update_site_texts") {
    $address = mysqli_real_escape_string($conn, $_POST['site_address']);
    $tagline = mysqli_real_escape_string($conn, $_POST['site_tagline']);
    $marquee = mysqli_real_escape_string($conn, $_POST['site_marquee']);
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);

    upsert_service($conn, 'SITE_ADDRESS', $address);
    upsert_service($conn, 'SITE_TAGLINE', $tagline);
    upsert_service($conn, 'SITE_MARQUEE', $marquee);
    upsert_service($conn, 'SITE_NAME', $site_name);
    
    header("Location: site-branding.php?msg=Site texts updated");
}

else if ($action == "update_social_links") {
    $platforms = $_POST['platforms'] ?? [];
    $urls = $_POST['urls'] ?? [];
    
    $links = [];
    for($i = 0; $i < count($platforms); $i++) {
        if(!empty($platforms[$i]) && !empty($urls[$i])) {
            $links[] = [
                'platform' => $platforms[$i],
                'value' => $urls[$i]
            ];
        }
    }
    
    $json_links = mysqli_real_escape_string($conn, json_encode($links));
    upsert_service($conn, 'SITE_SOCIAL_LINKS', $json_links);
    
    header("Location: site-branding.php?msg=Social links updated");
}

else if ($action == "add_slider") {
    if (isset($_FILES['banner_img']) && $_FILES['banner_img']['error'] == 0) {
        $upload_dir = "../uploads/branding/";
        $filename = "slider_" . time() . "_" . basename($_FILES['banner_img']['name']);
        $target_file = $upload_dir . $filename;
        $db_path = "admin/uploads/branding/" . $filename;
        $url = mysqli_real_escape_string($conn, $_POST['action_url']);

        if (move_uploaded_file($_FILES['banner_img']['tmp_name'], $target_file)) {
            if (mysqli_query($conn, "INSERT INTO tblsliders (tbl_slider_img, tbl_slider_action, tbl_slider_status) VALUES ('$db_path', '$url', 'true')")) {
                header("Location: site-branding.php?msg=Slider added");
            } else {
                $err = urlencode(mysqli_error($conn));
                header("Location: site-branding.php?err=DB Error: $err");
            }
        } else {
            header("Location: site-branding.php?err=Slider upload failed");
        }
    }
}

else if ($action == "add_promo") {
    if (isset($_FILES['promo_img']) && $_FILES['promo_img']['error'] == 0) {
        $upload_dir = "../uploads/branding/";
        $filename = "promo_" . time() . "_" . basename($_FILES['promo_img']['name']);
        $target_file = $upload_dir . $filename;
        $db_path = "admin/uploads/branding/" . $filename;
        $url = mysqli_real_escape_string($conn, $_POST['action_url']);

        if (move_uploaded_file($_FILES['promo_img']['tmp_name'], $target_file)) {
            if (mysqli_query($conn, "INSERT INTO tbl_promotions (image_path, action_url, status) VALUES ('$db_path', '$url', 'true')")) {
                header("Location: site-branding.php?msg=Promo added successfully");
            } else {
                $err = urlencode(mysqli_error($conn));
                header("Location: site-branding.php?err=Promo DB Error: $err");
            }
        } else {
            header("Location: site-branding.php?err=Promo image upload failed");
        }
    }
}

else if ($action == "delete_asset") {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];
    
    if ($type == "slider") {
        mysqli_query($conn, "DELETE FROM tblsliders WHERE id = $id");
    } else if ($type == "promo") {
        mysqli_query($conn, "DELETE FROM tbl_promotions WHERE id = $id");
    }
    header("Location: site-branding.php?msg=Asset deleted");
}
?>
