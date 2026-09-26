<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'db.php';

$agent_id = $_POST['agent_id'] ?? '';
$status = $_POST['status'] ?? '';

if (empty($agent_id) || empty($status)) {
    echo json_encode(['status' => 'error', 'message' => 'Agent ID and status are required.']);
    exit();
}

$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
if ($stmt->execute([$status, $agent_id])) {
    echo json_encode(['status' => 'success', 'message' => 'Agent status updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update agent status.']);
}
?>
