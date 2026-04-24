<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINE VOTING SYSTEM</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar glass-panel container" style="margin-top: 1rem;">
        <div class="logo">ONLINE VOTING SYSTEM</div>
        <div class="nav-links">
            <a href="admin/login.php">Admin Login</a>
        </div>
    </nav>

    <div class="container hero">
        <h1>Secure & Transparent<br>Online Voting</h1>
        <p>Empower your voice with our next-generation digital voting platform. Participate in elections securely from anywhere in the world.</p>
        
        <div class="btn-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user/dashboard.php" class="btn">Go to Dashboard</a>
            <?php
else: ?>
                <a href="login.php" class="btn">Login to Vote</a>
                <a href="register.php" class="btn btn-secondary">Register Account</a>
            <?php
endif; ?>
            <a href="results.php" class="btn" style="background: linear-gradient(135deg, var(--success), #047857);">View Live Results</a>
        </div>
    </div>
</body>
</html>
