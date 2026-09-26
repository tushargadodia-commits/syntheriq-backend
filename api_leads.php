<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include 'db.php';

$stmt = $conn->query("SELECT * FROM leads WHERE status = 'Pending' ORDER BY id ASC");
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'leads' => $leads
]);
?>
