<?php
// sms_api.php (runs inside Termux)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = escapeshellarg($_POST['number']);
    $message = escapeshellarg($_POST['message']);

    // send SMS using Termux API
    shell_exec("termux-sms-send -n $number $message");

    echo "Sent to $number";
} else {
    echo "Invalid request";
}
?>
