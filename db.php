<?php
$host = "sql113.infinityfree.com";
$user = "if0_42865625";
$pass = "tushar0006";
$dbname = "if0_42865625_syncro_lead_manager";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>