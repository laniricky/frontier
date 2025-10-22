<?php
// sms_api.php
// Runs inside Termux to receive HTTP POST requests from your PC and send SMS

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

if (!isset($_POST['number']) || !isset($_POST['message'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

$number = escapeshellarg($_POST['number']);
$message = escapeshellarg($_POST['message']);

// Execute the SMS send command and capture any output or errors
$output = shell_exec("termux-sms-send -n $number $message 2>&1");

if ($output === NULL) {
    echo json_encode(["status" => "error", "message" => "Failed to execute termux-sms-send"]);
} else {
    echo json_encode(["status" => "success", "message" => "SMS sent to $number"]);
}
?>
