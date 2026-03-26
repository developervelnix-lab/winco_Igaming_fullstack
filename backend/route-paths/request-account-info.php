<?php
$resArr["slideShowList"] = [];
$resArr["noticeArr"] = [];

function formatNumber($number){
    return number_format($number, 2, ".", "");
}

$user_id = "";

if (isset($_GET["USER_ID"])) {
    $user_id = mysqli_real_escape_string($conn, $_GET["USER_ID"]);
}
 
if($user_id!=""){

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

 
$select_sql = "SELECT * FROM tblusersdata WHERE tbl_uniq_id='{$user_id}' AND tbl_auth_secret ='{$secret_key}' ";
$select_query = mysqli_query($conn, $select_sql);

if (mysqli_num_rows($select_query) > 0 || $user_id=="guest") {
    $account_status = "true";
    $const_account_level = "";
    $const_avatar_id = "";
    $const_fullname = "";
    $const_mobile_num = "";
    $const_account_balance = "";
    $const_account_withdrawl_balance = "";
    $const_account_commission_balance = "";
    $const_account_last_active = "";
    $const_account_casino_bonus = "0.00";
    $const_account_sports_bonus = "0.00";
    $const_account_total_balance = "0.00";
    $const_account_bonus_balance = "0.00";
    
    if($user_id!="guest"){
      $res_data = mysqli_fetch_assoc($select_query);
      $account_balance = $res_data["tbl_balance"] ?? 0;
      $bonus_balance = $res_data["tbl_bonus_balance"] ?? 0;
      $sports_bonus = $res_data["tbl_sports_bonus"] ?? 0;
      $total_balance = $account_balance + $bonus_balance + $sports_bonus;

      $account_status = $res_data["tbl_account_status"];
      
      $const_account_level = $res_data["tbl_account_level"];
      $const_avatar_id = $res_data["tbl_avatar_id"];
      $const_fullname = $res_data["tbl_full_name"];
      $const_mobile_num = $res_data["tbl_mobile_num"];
      $const_account_balance = formatNumber($account_balance);
      $const_account_casino_bonus = formatNumber($bonus_balance);
      $const_account_sports_bonus = formatNumber($sports_bonus);
      $const_account_total_balance = formatNumber($total_balance);
      $const_account_bonus_balance = formatNumber($bonus_balance);
      $const_account_withdrawl_balance = formatNumber($res_data["tbl_withdrawl_balance"]);
      $const_account_commission_balance = formatNumber($res_data["tbl_commission_balance"]);
      $const_account_last_active = $res_data["tbl_last_active_date"].' '.$res_data["tbl_last_active_time"];
      
      $notices_sql = "SELECT * FROM tblallnotices WHERE tbl_user_id='{$user_id}' AND tbl_notice_status='true' ORDER BY id DESC LIMIT 1";
      $notices_query = mysqli_query($conn, $notices_sql);
      if(mysqli_num_rows($notices_query) > 0){
        $noticeResp = mysqli_fetch_assoc($notices_query);
        $noticeTitle = $noticeResp['tbl_notice_title'];
        $noticeNote = $noticeResp['tbl_notice_note'];

        array_push($resArr['noticeArr'], $noticeTitle, $noticeNote);
      
        $update_sql = "UPDATE tblallnotices SET tbl_notice_status = 'false' WHERE tbl_user_id = '{$user_id}'";
        $update_query = mysqli_query($conn, $update_sql);
      }
    }
    
    if($account_status!="true"){
      $resArr["status_code"] = "account_suspended";  
    }else{
        
    $service_app_status = "";
    $service_min_recharge = "";
    $service_recharge_option = "";
    $service_telegram_url = "";
    $service_imp_message = "";
    $service_imp_alert = "";
    $service_site_logo = "";
    $service_whatsapp = "";
    $service_support_url = "";
    
    $service_site_address = "";
    $service_site_tagline = "";
    $service_site_marquee = "";
    $service_site_name = "";
    $service_social_links = [];
        
    $services_sql = "SELECT * FROM tblservices";
    $services_query = mysqli_query($conn, $services_sql);
    while($row = mysqli_fetch_array($services_query)){
        if($row['tbl_service_name']=="APP_STATUS"){
            $service_app_status = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="RECHARGE_OPTIONS"){
            $service_recharge_option = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="MIN_RECHARGE"){
            $service_min_recharge = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="TELEGRAM_URL"){
            $service_telegram_url = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="SITE_LOGO_URL"){
            $service_site_logo = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="CONTACT_WHATSAPP"){
            $service_whatsapp = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="CONTACT_SUPPORT_URL"){
            $service_support_url = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="IMP_MESSAGE"){
            $service_imp_message = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="IMP_ALERT"){
            $service_imp_alert = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="SITE_ADDRESS"){
            $service_site_address = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="SITE_TAGLINE"){
            $service_site_tagline = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="SITE_MARQUEE"){
            $service_site_marquee = $row['tbl_service_value'];
        }else if($row['tbl_service_name']=="SITE_SOCIAL_LINKS"){
            $service_social_links = json_decode($row['tbl_service_value'], true) ?: [];
        }else if($row['tbl_service_name']=="SITE_NAME"){
            $service_site_name = $row['tbl_service_value'];
        }
        
        $service_active_payment = "";
    }
    
    
    $sliders_sql = "SELECT * FROM tblsliders WHERE tbl_slider_status='true' ";
    $sliders_query = mysqli_query($conn, $sliders_sql);
    while($row = mysqli_fetch_array($sliders_query)){
        $slideIndex['slider_img'] = $row["tbl_slider_img"];
        $slideIndex['slider_action'] = $row["tbl_slider_action"];

        array_push($resArr['slideShowList'], $slideIndex);
    }
                
    $index["account_id"] = $user_id;
    $index["account_level"] = $const_account_level;
    $index["account_avatar_id"] = $const_avatar_id;
    $index["account_username"] = $const_fullname;
    $index["account_mobile_num"] = $const_mobile_num;
    $index["account_balance"] = $const_account_balance;
    $index["account_b_balance"] = $const_account_bonus_balance;
    $index["account_w_balance"] = $const_account_withdrawl_balance;
    $index["account_c_balance"] = $const_account_commission_balance;
    $index["account_last_active"] = $const_account_last_active;
    
    $index["account_casino_bonus"] = $const_account_casino_bonus;
    $index["account_sports_bonus"] = $const_account_sports_bonus;
    $index["account_total_balance"] = $const_account_total_balance;
        
    $index["service_app_status"] = $service_app_status;
    $index["service_min_recharge"] = $service_min_recharge;
    $index["service_recharge_option"] = $service_recharge_option;
    $index["service_telegram_url"] = $service_telegram_url;
    $index["service_whatsapp"] = $service_whatsapp;
    $index["service_support_url"] = $service_support_url;
    $index["service_site_logo"] = $service_site_logo;

    $index["service_address"] = $service_site_address;
    $index["service_tagline"] = $service_site_tagline;
    $index["service_marquee"] = $service_site_marquee;
    $index["service_social_links"] = $service_social_links;
    $index["service_site_name"] = $service_site_name;

    $index["service_livechat_url"] = $LIVE_CHAT_URL;
    $index["service_app_download_url"] = $APP_DOWNLOAD_URL;
    $index["service_payment_url"] = $PAY_TARGET_URL;
    $index["service_imp_message"] = $service_imp_message;
    
    $resArr["promo_banners"] = [];
    $promos = mysqli_query($conn, "SELECT * FROM tbl_promotions WHERE status='true'");
    while($p = mysqli_fetch_assoc($promos)){
        array_push($resArr["promo_banners"], [
            "image" => $p['image_path'],
            "action" => $p['action_url']
        ]);
    }
    
    $important_alert = explode(",", $service_imp_alert);
    if(count($important_alert) > 1 && count($resArr['noticeArr']) <= 0){
        array_push($resArr['noticeArr'], $important_alert[0], $important_alert[1]);
    }
        
    array_push($resArr["data"], $index);

    $update_sql = "UPDATE tblusersdata SET tbl_last_active_date = '{$curr_date}', tbl_last_active_time = '{$curr_time}' WHERE tbl_uniq_id = '{$user_id}'";
    $update_query = mysqli_query($conn, $update_sql);
        
    $resArr["status_code"] = "success";
    }
} else {
    $resArr["status_code"] = "authorization_error";
}

mysqli_close($conn);
echo json_encode($resArr);

?>