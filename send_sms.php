<?php
// connect to database
$conn = new mysqli("localhost", "root", "", "test_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch all users
$result = $conn->query("SELECT name, phone, code FROM users");

// your Termux phone's IP and port
$termux_server = "http://192.168.1.253:8080/sms_api.php"; // change to your phone IP

while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $phone = $row['phone'];
    $code = $row['code'];

    $message = "Hello $name, please DO NOT FORGET THAT your unique code: $code.";

    // prepare data for POST
    $data = http_build_query([
        'number' => $phone,
        'message' => $message
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $data
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($termux_server, false, $context);

    echo "Sent to $name ($phone) → $response <br>";
    sleep(3); // pause a few seconds between messages
}

$conn->close();
?>
