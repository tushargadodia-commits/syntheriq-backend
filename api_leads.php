<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include 'db.php';

$username = $_GET['username'] ?? '';
$role = $_GET['role'] ?? '';

if (empty($username)) {
    echo json_encode(["status" => "error", "message" => "Username is required"]);
    exit();
}

if ($role === 'admin') {
    $query = "SELECT * FROM leads ORDER BY callback_time ASC";
} else {
    // Sub-admin gets only their assigned leads
    $stmt = $conn->prepare("SELECT * FROM leads WHERE assigned_to = ? OR added_by = ? ORDER BY callback_time ASC");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $leads = [];
    while ($row = $result->fetch_assoc()) {
        $leads[] = $row;
    }
    echo json_encode(["status" => "success", "leads" => $leads]);
    exit();
}

$result = $conn->query($query);
$leads = [];
while ($row = $result->fetch_assoc()) {
    $leads[] = $row;
}

echo json_encode(["status" => "success", "leads" => $leads]);
?>