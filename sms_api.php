<?php
header("Content-Type: application/json");

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Only POST allowed"]);
    exit;
}

// Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$number = $data['number'] ?? null;
$message = $data['message'] ?? null;

// Validate
if (!$number || !$message) {
    echo json_encode(["status" => "error", "message" => "Missing 'number' or 'message' in JSON body"]);
    exit;
}

// ✅ Handle ping request
if ($number === '0000' && strtolower(trim($message)) === 'ping') {
    echo json_encode(["status" => "success", "message" => "Ping OK"]);
    exit;
}

// ✅ Safely escape parameters
$escaped_number = escapeshellarg($number);
$escaped_message = escapeshellarg($message);

// Execute SMS command
$output = shell_exec("termux-sms-send -n $escaped_number $escaped_message 2>&1");

// Log all output for debugging
file_put_contents("debug_log.txt", date("[Y-m-d H:i:s] ") . "Sent to $number → $output\n", FILE_APPEND);

// ✅ Error detection
if ($output && str_contains(strtolower($output), "permission")) {
    echo json_encode(["status" => "error", "message" => "Permission denied. Enable SMS permission for Termux."]);
    exit;
}

if ($output && str_contains(strtolower($output), "not found")) {
    echo json_encode(["status" => "error", "message" => "Termux:API not installed. Run: pkg install termux-api -y"]);
    exit;
}

// ✅ Default success if no error found
echo json_encode(["status" => "success", "message" => "SMS sent successfully", "number" => $number]);
?>
