<?php
$host = 'localhost';
$db = 'club';
$user = 'root'; // Replace with your MySQL username
$pass = ''; // Replace with your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>