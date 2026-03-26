<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';

header('Content-Type: application/json');

$resArr = ["status_code" => "error"];

$input = json_decode(file_get_contents("php://input"), true);

$name = mysqli_real_escape_string($conn, $input['name'] ?? '');
$email = mysqli_real_escape_string($conn, $input['email'] ?? '');
$subject = mysqli_real_escape_string($conn, $input['subject'] ?? '');
$message = mysqli_real_escape_string($conn, $input['message'] ?? '');
$user_id = mysqli_real_escape_string($conn, $input['USER_ID'] ?? 'guest');

if(empty($name) || empty($email) || empty($subject) || empty($message)) {
    $resArr['msg'] = 'All fields are required';
    echo json_encode($resArr);
    exit;
}

// Create tickets table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS tbl_support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id VARCHAR(50) NOT NULL,
    user_id VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open','in_progress','closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
mysqli_query($conn, $create_table);

$ticket_id = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 8));

$sql = "INSERT INTO tbl_support_tickets (ticket_id, user_id, name, email, subject, message) 
        VALUES ('$ticket_id', '$user_id', '$name', '$email', '$subject', '$message')";

if(mysqli_query($conn, $sql)) {
    $resArr['status_code'] = 'success';
    $resArr['msg'] = 'Ticket submitted successfully';
    $resArr['ticket_id'] = $ticket_id;
} else {
    $resArr['msg'] = 'Failed to submit ticket';
}

mysqli_close($conn);
echo json_encode($resArr);
?>
