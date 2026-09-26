<?php
// login.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syncro Manager - Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #121212; color: #e0e0e0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: #1e1e1e; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); width: 100%; max-width: 400px; text-align: center; }
        .login-card h2 { color: #fff; margin-bottom: 24px; font-size: 24px; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; color: #b0b0b0; font-size: 14px; }
        .input-group input { width: 100%; padding: 12px; background: #2c2c2c; border: 1px solid #444; border-radius: 6px; color: #fff; font-size: 16px; }
        .input-group input:focus { border-color: #bb86fc; outline: none; }
        .btn-submit { width: 100%; padding: 12px; background: #bb86fc; border: none; border-radius: 6px; color: #121212; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.3s; }
        .btn-submit:hover { background: #9965f4; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Syncro Manager</h2>
        <form action="api_login.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Enter username">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
    </div>
</body>
</html>
