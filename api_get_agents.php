<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection details (InfinityFree MySQL details yahan daalein)
$host = "sql133.infinityfree.com"; // Apka InfinityFree MySQL host
$username = "if0_42865625";          // Apka DB username
$password = "tushar0006";     // Apka DB password
$database = "if0_42865625_syncro_lead_manager";      // Apka DB name

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit();
}

// Fetch all agents and users from the database
$query = "SELECT id, username, role, status FROM users";
$result = mysqli_query($conn, $query);

if ($result) {
    $agents = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $agents[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'agents' => $agents
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch agents list'
    ]);
}

mysqli_close($conn);
?>
