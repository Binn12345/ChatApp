<?php
include 'config.php';
// session_start();

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'] ?? '';

$image_path = null;

// var_dump($_FILES);
// exit;


// var_dump('<pre>',$_POST);die;

// IMAGE UPLOAD
// if (!empty($_FILES['image']['name'])) {
//     $targetDir = "uploads/";
//     if (!is_dir($targetDir)) {
//         mkdir($targetDir, 0777, true);
//     }

//     $fileName = time() . "_" . basename($_FILES["image"]["name"]);
//     $targetFile = $targetDir . $fileName;

//     move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

//     $image_path = $targetFile;
// }

if (!empty($_FILES['image']['tmp_name'])) {
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    $profile_pic = $imageData;
      $image_type = $_FILES['image']['type']; // e.g. image/jpeg
}

// INSERT TO DB
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, image, image_type) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $sender_id, $receiver_id, $message, $profile_pic, $image_type);
$stmt->execute();
