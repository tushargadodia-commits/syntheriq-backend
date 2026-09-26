<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lead_id = $_POST['lead_id'] ?? '';
    $disposition = $_POST['disposition'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (empty($lead_id) || empty($disposition)) {
        echo json_encode(["status" => "error", "message" => "Lead ID and Disposition are required"]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE leads SET disposition = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $disposition, $notes, $lead_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Lead disposition updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update lead"]);
    }
    $stmt->close();
}
?>