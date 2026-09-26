<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'db.php';

$lead_id = $_POST['lead_id'] ?? '';
$disposition = $_POST['disposition'] ?? '';
$notes = $_POST['notes'] ?? '';

if (empty($lead_id) || empty($disposition)) {
    echo json_encode(['status' => 'error', 'message' => 'Lead ID and disposition are required.']);
    exit();
}

$stmt = $conn->prepare("UPDATE leads SET status = 'Completed', disposition = ?, notes = ? WHERE id = ?");
if ($stmt->execute([$disposition, $notes, $lead_id])) {
    echo json_encode(['status' => 'success', 'message' => 'Lead updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update lead status.']);
}
?>
