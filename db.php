<?php
$host = "crm-syncromanager0009.e.aivencloud.com";
$port = "23715";
$dbname = "defaultdb";
$username = "avnadmin";
$password = "AVNS_JGIR29t1etGmn_Q1FnA";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit();
}
?>
