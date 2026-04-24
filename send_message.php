<?php
include 'config.php';

// safe session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? '2';
$message = $_POST['message'] ?? '';

$image = null;

// GET RAW IMAGE
if (!empty($_FILES['image']['tmp_name'])) {
    $image = file_get_contents($_FILES['image']['tmp_name']);
}

// DEBUG (optional)
// var_dump($_POST, $_FILES); die;

// INSERT
$stmt = $conn->prepare("
    INSERT INTO messages (sender_id, receiver_id, message, image)
    VALUES (?, ?, ?, ?)
");

$null = NULL;

// bind text + placeholder for blob
$stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $null);

// send actual binary data
$stmt->send_long_data(3, $image);

$stmt->execute();

echo "success";
?>