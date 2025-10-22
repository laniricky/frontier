<?php
// sms_api.php - handles SMS sending via Termux:API

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed. Use POST"]);
    exit;
}

// Read POST body
$data = json_decode(file_get_contents("php://input"), true);

// Validate
if (!isset($data['number']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing number or message"]);
    exit;
}

$number = escapeshellarg($data['number']);
$message = escapeshellarg($data['message']);

// Try sending SMS
exec("termux-sms-send -n $number $message 2>&1", $output, $resultCode);

if ($resultCode === 0) {
    echo json_encode(["success" => true, "number" => $data['number'], "message" => "SMS sent successfully"]);
} else {
    echo json_encode(["success" => false, "error" => implode("\n", $output)]);
}
?>
