<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bepo_peso_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Manila');
?>