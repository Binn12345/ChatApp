<?php
session_start();

$conn = new mysqli("localhost", "root", "", "chat_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>