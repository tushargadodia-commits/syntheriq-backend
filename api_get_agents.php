<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include 'db.php';

$stmt = $conn->query("SELECT id, username, role, status FROM users");
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'agents' => $agents
]);
?>
