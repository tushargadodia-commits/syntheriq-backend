<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide both username and password.']);
    exit();
}

$stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ?");
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    if ($row['status'] === 'disabled') {
        echo json_encode(['status' => 'error', 'message' => 'Your account has been disabled by the admin.']);
        exit();
    }

    if ($password === $row['password'] || password_verify($password, $row['password'])) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'username' => $row['username'],
            'role' => $row['role']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
}
?>
