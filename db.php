<?php
// Aiven PostgreSQL Database Connection via PDO for Render
$host = getenv('DB_HOST') ?: 'crm-syncromanager0009.e.aivencloud.com'; // Yahan apna Aiven Host daalein
$port = getenv('DB_PORT') ?: '23715'; // Port (jaise 24712)
$dbname = getenv('DB_NAME') ?: 'defaultdb';     // Database name
$user = getenv('DB_USER') ?: 'avnadmin';        // Username
$pass = getenv('DB_PASS') ?: 'AVNS_JGIR29t1etGmn_Q1FnA'; // Password

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => "Database Connection Failed: " . $e->getMessage()
    ]));
}
?>
