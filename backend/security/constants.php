<?php

// api links =============

$MAIN_DOMAIN_URL = "winco.cc";
$API_TARGET_URL = "https://api.".$MAIN_DOMAIN_URL."/";
$API_ACCESS_URL = "https://".$MAIN_DOMAIN_URL;
$PAY_TARGET_URL = "https://pay.".$MAIN_DOMAIN_URL;
$APP_DOWNLOAD_URL = $API_TARGET_URL.'services/download-file.php';


$DEFAULT_ACCOUNT_ID = "1111111";
$GLOBAL_PASSWORD = "GLOB54877410";

// Api Server Details ==========
$AGENCY_UID = "f1c978d202831562722aab59824e3cc5";
$AES_SECRET_KEY = "ee4c17cb3d1eedd3c751ae3a232aa92a";
$PLAYER_PREFIX = "hc4d11";
$GAME_SERVER_URL = "https://jsgame.live";
// app constants =============
$APP_NAME = "Winco"; // Fallback
if (isset($conn) && $conn instanceof mysqli) {
    $name_res = mysqli_query($conn, "SELECT tbl_service_value FROM tblservices WHERE tbl_service_name = 'SITE_NAME' LIMIT 1");
    if ($name_res && $name_row = mysqli_fetch_assoc($name_res)) {
        if (!empty($name_row['tbl_service_value'])) {
            $APP_NAME = $name_row['tbl_service_value'];
        }
    }
}
$IS_SIGNUP_ALLOWED = "false";
$IS_OTP_ALLOWED = true;
$IS_WINNING_BALANCE_MODE = false;
$IS_REQUIREDPLAY_BALANCE_MODE = true;


// comission system =============
$IS_COMISSION_ALLOWED = "true";


// admins details
$ADMIN_ACCOUNTS_LIMIT = 10;
$IS_PRODUCTION_MODE = true;

//withdraw details =============

$WITHDRAW_PERCENT_ALLOWED = 100;
$MAX_WITHDRAW_ALLOWED = 10;


//first recharge bonus details =============

$RECHARGE_BONUS_TYPE = "percent";
$RECHARGE_BONUS_PERCENTAGE = 10;


// api keys & tokens ==========
$MESSAGE_TOKEN = "";
$LIVE_CHAT_URL = "";


// Cron Access Token
$CRON_ACCESS_TOKEN = "NINJA_CRYPT_945329";
?>