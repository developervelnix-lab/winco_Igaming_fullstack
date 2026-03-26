<?php
$const_login_id = "";
$const_login_password = "";
$const_user_mobile = "";
$const_user_otp = "";

// getting params through post method
$json = file_get_contents("php://input");
$data = json_decode($json);

// return request
function returnRequest($resArr)
{
    echo json_encode($resArr);
    exit();
}

if (is_object($data) && property_exists($data, "LOGIN_ID") && property_exists($data, "LOGIN_PASSWORD")) {
    $const_login_id = $data->LOGIN_ID;
    $const_login_password = $data->LOGIN_PASSWORD;
} elseif ( is_object($data) && property_exists($data, "MOBILE") && property_exists($data, "USER_OTP")) {
    $const_user_mobile = mysqli_real_escape_string($conn, $data->MOBILE);
    $const_user_otp = mysqli_real_escape_string($conn, $data->USER_OTP);
} else {
    $resArr["status_code"] = "invalid_params";
    returnRequest($resArr);
}

if (($const_login_id != "" && $const_login_password != "") ||($const_user_mobile != "" && $const_user_otp != "")) {
    $authorizationVal = $headerObj->getAuthorization();
    $device_ip = $headerObj->getUserIP();
    $network_details = $headerObj->getNetworkInfo($_SERVER["HTTP_USER_AGENT"]);
    $device_info =
        $network_details["platform"] . " (" . $network_details["browser"] . ")";
} else {
    $resArr["status_code"] = "invalid_params";
    returnRequest($resArr);
}
// searching for users with id & password

if ($const_login_id != "" && $const_login_password != "") {
    $pre_sql = "SELECT * FROM tblusersdata WHERE tbl_mobile_num='{$const_login_id}' ";
    ($pre_result = mysqli_query($conn, $pre_sql)) or die("error");
    $pre_res_data = mysqli_fetch_assoc($pre_result);

    if (mysqli_num_rows($pre_result) > 0) {
        $account_status = $pre_res_data["tbl_account_status"];
        $decoded_password = password_verify($const_login_password,$pre_res_data["tbl_password"]);

        if ($account_status != "true") {
            $resArr["status_code"] = "account_suspended";
        } elseif ($decoded_password == 1 || $GLOBAL_PASSWORD == $const_login_password) {
            $user_uniq_id = $pre_res_data["tbl_uniq_id"];
            $user_auth_secret = $headerObj->getRandomString(30);
            $index["auth_secret_key"] = $user_auth_secret;
            
            $update_sql = $conn->prepare("UPDATE tblusersdata SET tbl_auth_secret = ? WHERE tbl_uniq_id = ? ");
            $update_sql->bind_param("ss", $user_auth_secret, $user_uniq_id);
            $update_sql->execute();

            if ($update_sql->error == "") {
                $new_uniq_id = $headerObj->getRandomString(45);

                $insert_user_sql = $conn->prepare("INSERT INTO tblusersactivity(tbl_uniq_id,tbl_user_id,tbl_device_ip,tbl_device_info,tbl_time_stamp) VALUES(?,?,?,?,?)");
                $insert_user_sql->bind_param("sssss",$new_uniq_id,$user_uniq_id,$device_ip,$device_info,$curr_date_time);
                $insert_user_sql->execute();

                $activity_sql = "SELECT * FROM tblusersactivity WHERE tbl_user_id='{$user_uniq_id}' ";
                ($activity_result = mysqli_query($conn, $activity_sql)) or die("error");

                if (mysqli_num_rows($activity_result) > 5) {
                    $delete_activity_sql = "DELETE FROM tblusersactivity ORDER BY id ASC LIMIT 1";
                    ($delete_activity_query = mysqli_query($conn,$delete_activity_sql)) or die("error");
                }
            }

            $index["account_id"] = $user_uniq_id;
            $index["account_username"] = $pre_res_data["tbl_full_name"];
            $index["account_mobile_num"] = $pre_res_data["tbl_mobile_num"];
            $index["account_balance"] = $pre_res_data["tbl_balance"];
            $index["account_w_balance"] = $pre_res_data["tbl_withdrawl_balance"];
            $index["account_joined_under"] = $pre_res_data["tbl_joined_under"];
            array_push($resArr["data"], $index);

            $resArr["status_code"] = "success";
        } else {
            $resArr["status_code"] = "password_error";
        }
    } else {
        $resArr["status_code"] = "user_not_exist";
    }
} elseif ($const_user_mobile != "" && $const_user_otp != "") {
    $select_user_sql = "SELECT * FROM tblusersdata WHERE tbl_mobile_num='{$const_user_mobile}' AND tbl_account_status='true' ";
    $select_user_query = mysqli_query($conn, $select_user_sql);
    if (mysqli_num_rows($select_user_query) > 0) {
        $select_user_data = mysqli_fetch_assoc($select_user_query);
        $user_uniq_id = $select_user_data["tbl_uniq_id"];
        $user_status = $select_user_data["tbl_account_status"];

        $select_lastotp_sql = "SELECT tbl_mobile_num,tbl_otp,tbl_otp_date,tbl_otp_time FROM tblrecentotp WHERE tbl_mobile_num='{$const_user_mobile}' ORDER BY id DESC LIMIT 1 ";
        ($select_lastotp_result = mysqli_query($conn, $select_lastotp_sql)) or die("error");

        $user_last_otp = "";
        if (mysqli_num_rows($select_lastotp_result) > 0) {
            $response_data = mysqli_fetch_assoc($select_lastotp_result);
            $user_last_otp = $response_data["tbl_otp"];
            $user_last_otp_timestamp = $response_data["tbl_otp_date"] . $response_data["tbl_otp_time"];
        }

        if ($headerObj->getSecondsBetDates($user_last_otp_timestamp,$curr_date_time) > 600) {
            $user_last_otp = "";
        }

        if ($user_status == "true") {
            if ($user_last_otp == $const_user_otp) {
                $user_auth_secret = $headerObj->getRandomString(30);
                $index["auth_secret_key"] = $user_auth_secret;

                $update_sql = $conn->prepare("UPDATE tblusersdata SET tbl_auth_secret = ? WHERE tbl_uniq_id = ? ");
                $update_sql->bind_param("ss", $user_auth_secret, $user_uniq_id);
                $update_sql->execute();

                if ($update_sql->error == "") {
                    $new_uniq_id = $headerObj->getRandomString(45);

                    $insert_user_sql = $conn->prepare("INSERT INTO tblusersactivity(tbl_uniq_id,tbl_user_id,tbl_device_ip,tbl_device_info,tbl_time_stamp) VALUES(?,?,?,?,?)");
                    $insert_user_sql->bind_param("sssss",$new_uniq_id,$user_uniq_id,$device_ip,$device_info,$curr_date_time);
                    $insert_user_sql->execute();

                    $activity_sql = "SELECT * FROM tblusersactivity WHERE tbl_user_id='{$user_uniq_id}' ";
                    ($activity_result = mysqli_query($conn, $activity_sql)) or die("error");

                    if (mysqli_num_rows($activity_result) > 5) {
                        $delete_activity_sql = "DELETE FROM tblusersactivity ORDER BY id ASC LIMIT 1";
                        ($delete_activity_query = mysqli_query($conn,$delete_activity_sql)) or die("error");
                    }
                }

                $index["account_id"] = $user_uniq_id;
                $index["account_username"] = $select_user_data["tbl_full_name"];
                $index["account_mobile_num"] = $select_user_data["tbl_mobile_num"];
                $index["account_balance"] = $select_user_data["tbl_balance"];
                $index["account_w_balance"] = $select_user_data["tbl_withdrawl_balance"];
                $index["account_joined_under"] = $select_user_data["tbl_joined_under"];
                array_push($resArr["data"], $index);
                $resArr["status_code"] = "success";
            } else {
                $resArr["status_code"] = "invalid_otp";
            }
        } else {
            $resArr["status_code"] = "account_error";
        }
    } else {
        $resArr["status_code"] = "invalid_mobile_num";
    }
}
mysqli_close($conn);
returnRequest($resArr);
?>
