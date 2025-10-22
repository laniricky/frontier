<?php
header("Content-Type: application/json");

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Only POST allowed"]);
    exit;
}

// Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$number = $data['number'] ?? null;
$message = $data['message'] ?? null;

// Validate
if (!$number || !$message) {
    echo json_encode(["success" => false, "error" => "Missing 'number' or 'message' in JSON body"]);
    exit;
}

// Handle "ping" test requests
if ($number === '0000') {
    echo json_encode(["success" => true, "message" => "Ping OK"]);
    exit;
}

// Run SMS command and capture output
$output = shell_exec("termux-sms-send -n $number '$message' 2>&1");

// Log raw output for debugging
file_put_contents("debug_log.txt", date("[Y-m-d H:i:s] ") . "Sent to $number → $output\n", FILE_APPEND);

// Check for common errors
if (str_contains(strtolower($output), "permission")) {
    echo json_encode(["success" => false, "error" => "Permission denied. Allow SMS permission in Android settings."]);
    exit;
}

if (str_contains(strtolower($output), "not found")) {
    echo json_encode(["success" => false, "error" => "termux-api not installed. Run: pkg install termux-api -y"]);
    exit;
}

// If no errors, assume success
echo json_encode(["success" => true, "number" => $number, "message" => $message]);
?>
