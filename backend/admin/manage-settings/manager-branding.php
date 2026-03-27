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
    $db_path = "";
    $upload_dir = "../uploads/branding/";
    
    // Check for cropped data (base64)
    if (!empty($_POST['cropped_data'])) {
        $data = $_POST['cropped_data'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc
            $data = base64_decode($data);
            $filename = "logo_" . time() . ".jpg"; // Prefer jpg for cropped
            if (file_put_contents($upload_dir . $filename, $data)) {
                $db_path = "admin/uploads/branding/" . $filename;
            }
        }
    } 
    // Fallback to standard upload
    else if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $filename = "logo_" . time() . "_" . basename($_FILES['site_logo']['name']);
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_dir . $filename)) {
            $db_path = "admin/uploads/branding/" . $filename;
        }
    }

    if (!empty($db_path)) {
        upsert_service($conn, 'SITE_LOGO_URL', $db_path);
        header("Location: site-branding.php?msg=Logo updated successfully");
    } else {
        $error_code = $_FILES['site_logo']['error'] ?? 'No file OR empty cropped data';
        header("Location: site-branding.php?err=Upload failed (Code: $error_code). Please try again.");
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
    $db_path = "";
    $upload_dir = "../uploads/branding/";
    $url = mysqli_real_escape_string($conn, $_POST['action_url'] ?? '');

    if (!empty($_POST['cropped_data'])) {
        $data = $_POST['cropped_data'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);
            $filename = "slider_" . time() . ".jpg";
            if (file_put_contents($upload_dir . $filename, $data)) {
                $db_path = "admin/uploads/branding/" . $filename;
            }
        }
    } else if (isset($_FILES['banner_img']) && $_FILES['banner_img']['error'] == 0) {
        $filename = "slider_" . time() . "_" . basename($_FILES['banner_img']['name']);
        if (move_uploaded_file($_FILES['banner_img']['tmp_name'], $upload_dir . $filename)) {
            $db_path = "admin/uploads/branding/" . $filename;
        }
    }

    if (!empty($db_path)) {
        if (mysqli_query($conn, "INSERT INTO tblsliders (tbl_slider_img, tbl_slider_action, tbl_slider_status) VALUES ('$db_path', '$url', 'true')")) {
            header("Location: site-branding.php?msg=Slider added");
        } else {
            header("Location: site-branding.php?err=DB Error: " . urlencode(mysqli_error($conn)));
        }
    } else {
        header("Location: site-branding.php?err=Slider upload failed");
    }
}

else if ($action == "add_promo") {
    $db_path = "";
    $upload_dir = "../uploads/branding/";
    $url = mysqli_real_escape_string($conn, $_POST['action_url'] ?? '');

    if (!empty($_POST['cropped_data'])) {
        $data = $_POST['cropped_data'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);
            $filename = "promo_" . time() . ".jpg";
            if (file_put_contents($upload_dir . $filename, $data)) {
                $db_path = "admin/uploads/branding/" . $filename;
            }
        }
    } else if (isset($_FILES['promo_img']) && $_FILES['promo_img']['error'] == 0) {
        $filename = "promo_" . time() . "_" . basename($_FILES['promo_img']['name']);
        if (move_uploaded_file($_FILES['promo_img']['tmp_name'], $upload_dir . $filename)) {
            $db_path = "admin/uploads/branding/" . $filename;
        }
    }

    if (!empty($db_path)) {
        if (mysqli_query($conn, "INSERT INTO tbl_promotions (image_path, action_url, status) VALUES ('$db_path', '$url', 'true')")) {
            header("Location: site-branding.php?msg=Promo added successfully");
        } else {
            header("Location: site-branding.php?err=Promo DB Error: " . urlencode(mysqli_error($conn)));
        }
    } else {
        header("Location: site-branding.php?err=Promo image upload failed");
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
