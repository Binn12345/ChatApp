<?php
include 'config.php';

if(!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM users WHERE id != $user_id");

while($row = $result->fetch_assoc()){
    echo "<li data-id='".$row['id']."' data-name='".$row['name']."'>";
    echo $row['name'];
    echo "</li>";
}
?>