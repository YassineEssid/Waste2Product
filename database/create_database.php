<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'waste2product';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$sql = "CREATE DATABASE IF NOT EXISTS `$db`";
$conn->query($sql);
$conn->close();
