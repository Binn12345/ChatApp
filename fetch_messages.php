<?php
include 'config.php';

$sender = $_SESSION['user_id'];
$receiver = $_GET['receiver_id'];

$sql = "SELECT * FROM messages 
WHERE (sender_id='$sender' AND receiver_id='$receiver')
OR (sender_id='$receiver' AND receiver_id='$sender')
ORDER BY created_at ASC";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){
    $class = ($row['sender_id'] == $sender) ? 'sent' : 'received';

    echo "<div class='message $class'>";

    // TEXT
    if(!empty($row['message'])){
        echo "<div>".$row['message']."</div>";
    }

    // IMAGE (BLOB)
    if(!empty($row['image'])){
        $base64 = base64_encode($row['image']);
        echo "<img src='data:".$row['image_type'].";base64,".$base64."' style='max-width:200px;border-radius:10px;margin-top:5px;'>";
    }

    echo "</div>";
}
?>