<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = $_POST['number'];
    $message = $_POST['message'];
    shell_exec("termux-sms-send -n $number '$message'");
    echo "Sent to $number";
}
?>
