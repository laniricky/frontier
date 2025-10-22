<?php
header("Content-Type: application/json");

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "error" => "Only POST allowed"]);
    exit;
}

// Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$number = $data['number'] ?? null;
$message = $data['message'] ?? null;

// Validate
if (!$number || !$message) {
    echo json_encode(["status" => "error", "error" => "Missing 'number' or 'message' in JSON body"]);
    exit;
}

// Handle ping
if ($number === '0000') {
    echo json_encode(["status" => "success", "message" => "Ping OK"]);
    exit;
}

// Run SMS command
$output = shell_exec("termux-sms-send -n $number '$message' 2>&1");

// Log output
file_put_contents("debug_log.txt", date("[Y-m-d H:i:s] ") . "Sent to $number → $output\n", FILE_APPEND);

// Detect errors
if (str_contains(strtolower($output), "permission")) {
    echo json_encode(["status" => "error", "error" => "Permission denied. Enable SMS permission for Termux."]);
    exit;
}

if (str_contains(strtolower($output), "not found")) {
    echo json_encode(["status" => "error", "error" => "termux-api not installed. Run: pkg install termux-api -y"]);
    exit;
}

// Assume success if no error output
echo json_encode(["status" => "success", "message" => "SMS sent successfully", "number" => $number]);
?>
