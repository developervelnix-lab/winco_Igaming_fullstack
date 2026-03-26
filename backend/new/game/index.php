<?php
error_reporting(0);
header('Content-Type: application/json');

include "../security/constants.php";
date_default_timezone_set("Asia/Kolkata");

$is_db_connected = "false";
$server_db = "localhost";
$hostname_db = "winco";
$username_db = "winco";
$password_db = "winco";

try {
    if ($conn = mysqli_connect($server_db, $username_db, $password_db, $hostname_db)) {
        $is_db_connected = "true";
    } else {
        throw new Exception("Unable to connect");
    }
} catch (Throwable $e) {
    echo $e->getMessage();
    echo "Please setup extension properly.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function decrypt($data, $key) {
        return openssl_decrypt(base64_decode($data), "AES-256-ECB", $key, OPENSSL_RAW_DATA);
    }

    function encrypt($data, $key) {
        return base64_encode(openssl_encrypt($data, "AES-256-ECB", $key, OPENSSL_RAW_DATA));
    }

    $json = file_get_contents("php://input");
    $json_data = json_decode($json, true);
    $payload = $json_data["payload"];
    if($payload) {
      $data = json_decode(decrypt($payload, $AES_SECRET_KEY), true);
    }
    $log_data = date('Y-m-d H:i:s') . " - Server Request: " . json_encode($json) ."\n - Bet Request: " . json_encode($data) . "\n";
    file_put_contents("bet_logs.txt", $log_data, FILE_APPEND);

    $const_game_uid = $data["game_uid"];
    $win_amount = $data["win_amount"];
    $bet_amount = $data["bet_amount"];

    $match_details = $data["bet_data"]["leagueName_en"];
    $bet_type = $data["bet_data"]["betChoice_en"];
    $odds = $data["bet_data"]["odds"];

    $parts = explode($PLAYER_PREFIX, $data["member_account"]);
    $const_user_id = $parts[1];

    $select_sql = "SELECT tbl_balance, tbl_requiredplay_balance, tbl_withdrawl_balance, tbl_joined_under, tbl_account_status 
                   FROM tblusersdata WHERE tbl_uniq_id='$const_user_id'";
    $select_query = mysqli_query($conn, $select_sql);

    if (mysqli_num_rows($select_query) > 0) {
        $res_data = mysqli_fetch_assoc($select_query);
        $user_refered_by = $res_data["tbl_joined_under"];

        if ($res_data["tbl_account_status"] == "true") {
            $credit_amount = $res_data["tbl_balance"] - $bet_amount + $win_amount;

            if ($bet_amount == 0 && $win_amount == 0) {
                $payloadData = json_encode([
                    "credit_amount" => $credit_amount,
                    "timestamp" => round(microtime(true) * 1000),
                ]);
                $payload = encrypt($payloadData, $AES_SECRET_KEY);

                echo json_encode([
                    "code" => 0,
                    "msg" => "",
                    "payload" => $payload
                ]);
                exit;
            }

            $updated_balance = floatval($res_data["tbl_balance"]) + floatval($win_amount) - floatval($bet_amount);
            $tbl_play_updated_balance = floatval($res_data["tbl_requiredplay_balance"]) + floatval($win_amount) - floatval($bet_amount);
            $match_status = ($win_amount > 0) ? "profit" : "loss";

            $select_sql = $conn->prepare("SELECT * FROM tblmatchplayed WHERE tbl_user_id = ? AND tbl_period_id = ? ORDER BY tbl_time_stamp DESC LIMIT 1");
            $select_sql->bind_param("ss", $const_user_id, $const_game_uid);
            $select_sql->execute();
            $result = $select_sql->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $tbl_match_profit = $row['tbl_match_profit'];
                $tbl_match_cost = $row['tbl_match_cost'];

                $tbl_match_profit += $win_amount;
                $tbl_match_cost += $bet_amount;
                $match_status = ($tbl_match_profit > $tbl_match_cost) ? "profit" : "loss";

                $match_details = $match_details;
                $bet_type = $bet_type;
                $odds = $odds;

                $update_sql = $conn->prepare(
                    "UPDATE tblmatchplayed SET 
                        tbl_invested_on = tbl_invested_on + ?, 
                        tbl_match_invested = tbl_match_invested + ?, 
                        tbl_match_profit = tbl_match_profit + ?,
                        tbl_match_cost = tbl_match_cost + ?, 
                        tbl_last_acbalance = ?, 
                        tbl_match_status = ?,
                        tbl_match_details = ?,
                        tbl_bet_type = ?,
                        tbl_odds = ?
                    WHERE tbl_user_id = ? AND tbl_period_id = ?"
                );

                $update_sql->bind_param(
                    "sssssssssss",
                    $bet_amount,
                    $bet_amount,
                    $win_amount,
                    $bet_amount,
                    $updated_balance,
                    $match_status,
                    $const_user_id,
                    $const_game_uid,
                    $match_details,
                    $bet_type,
                    $odds
                );

                if ($update_sql->execute()) {
                    $update_sql = $conn->prepare(
                        "UPDATE tblusersdata 
                        SET tbl_balance = ?, 
                            tbl_requiredplay_balance = ? 
                        WHERE tbl_uniq_id = ?"
                    );
                    $update_sql->bind_param("sss", $updated_balance, $tbl_play_updated_balance, $const_user_id);
                    $update_sql->execute();

                    $payloadData = json_encode([
                        "credit_amount" => $credit_amount,
                        "timestamp" => round(microtime(true) * 1000),
                    ]);
                    $payload = encrypt($payloadData, $AES_SECRET_KEY);

                    echo json_encode([
                        "code" => 0,
                        "msg" => "",
                        "payload" => $payload
                    ]);
                    exit;
                }
            } else {
                    $update_sql = $conn->prepare(
                        "UPDATE tblusersdata 
                        SET tbl_balance = ?, 
                            tbl_requiredplay_balance = ? 
                        WHERE tbl_uniq_id = ?"
                    );
                    $update_sql->bind_param("sss", $updated_balance, $tbl_play_updated_balance, $const_user_id);
                    $update_sql->execute();

                    $payloadData = json_encode([
                        "credit_amount" => $credit_amount,
                        "timestamp" => round(microtime(true) * 1000),
                    ]);
                    $payload = encrypt($payloadData, $AES_SECRET_KEY);

                    echo json_encode([
                        "code" => 0,
                        "msg" => "",
                        "payload" => $payload
                    ]);
                    exit;     
           }
        }
    }
}

mysqli_close($conn);
?>