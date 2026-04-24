<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit;
}

require_once "includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill all fields.";
    }
    else {
        $stmt = $conn->prepare("SELECT id, name, password, has_voted FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['has_voted'] = $user['has_voted'];
                session_write_close();
                header("Location: user/dashboard.php");
                exit;
            }
            else {
                $error = "Invalid email or password.";
            }
        }
        else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINE VOTING SYSTEM</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container glass-panel">
        <h2>Welcome Back</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php
endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p style="margin-top: 1.5rem; color: var(--text-secondary);">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        <p style="margin-top: 0.5rem;">
            <a href="index.php" style="font-size: 0.875rem;">&larr; Back to Home</a>
        </p>
    </div>
</body>
</html>
