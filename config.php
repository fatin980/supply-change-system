<?php
$host = "localhost";
$user = "root";  // Change if using another user
$pass = "";  // Change if you set a MySQL password
$db = "supply_change_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
