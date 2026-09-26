<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'db.php';

$agent_id = $_POST['agent_id'] ?? '';

if (empty($agent_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Agent ID is required.'
    ]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $agent_id);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Agent deleted successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete agent.'
    ]);
}

$stmt->close();
$conn->close();
?>
