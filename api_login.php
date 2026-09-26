<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(strtolower($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Hardcoded credentials check matching your web CRM users
    $users = [
        'tushar' => ['role' => 'admin', 'name' => 'Tushar', 'pass' => '00001111'],
        'nishant' => ['role' => 'subadmin', 'name' => 'Nishant', 'pass' => 'nishant@123'],
        'ajay' => ['role' => 'subadmin', 'name' => 'Ajay', 'pass' => 'ajay@123'],
        'varunmaruya@syntheriq.com' => ['role' => 'subadmin', 'name' => 'Varun', 'pass' => 'varun8287'],
        'varun' => ['role' => 'subadmin', 'name' => 'Varun', 'pass' => 'varun8287'],
        'akankshamaurya@syntheriq.com' => ['role' => 'subadmin', 'name' => 'Akanksha', 'pass' => 'akku525650'],
        'akanksha' => ['role' => 'subadmin', 'name' => 'Akanksha', 'pass' => 'akku525650']
    ];

    if (isset($users[$username]) && $users[$username]['pass'] === $password) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "role" => $users[$username]['role'],
            "username" => $users[$username]['name']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid Username or Password"
        ]);
    }
}
?>