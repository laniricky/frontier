<?php
// sms_api.php - Termux-side HTTP receiver for sending SMS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method Not Allowed. Use POST"]);
    exit;
}

// Read JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Validate input
if (!is_array($data) || empty($data['number']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing 'number' or 'message' in JSON body"]);
    exit;
}

$number = $data['number'];
$message = $data['message'];

// Basic sanitize (do NOT remove plus sign)
$number_safe = escapeshellarg($number);
$message_safe = escapeshellarg($message);

// Execute termux-sms-send
exec("termux-sms-send -n $number_safe $message_safe 2>&1", $output, $exitCode);

if ($exitCode === 0) {
    echo json_encode(["success" => true, "number" => $number]);
} else {
    echo json_encode(["success" => false, "error" => implode("\n", $output)]);
}
?>
