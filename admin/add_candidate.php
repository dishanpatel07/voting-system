<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $party = trim($_POST['party']);

    // Image Upload Handling
    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image_name = time() . '_' . uniqid() . '.' . $ext;
            $destination = "../uploads/" . $image_name;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $error = "Failed to upload image.";
                $image_name = ""; // Reset if failed
            }
        }
        else {
            $error = "Invalid image format. Only JPG, PNG, and GIF are allowed.";
        }
    }

    if (empty($name) || empty($party)) {
        $error = "Name and Party fields are required.";
    }
    elseif (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO candidates (name, party, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $party, $image_name);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Candidate added successfully!";
            $_SESSION['msg_type'] = "success";
            session_write_close();
            header("Location: dashboard.php");
            exit;
        }
        else {
            $error = "Database error. Failed to add candidate.";
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar glass-panel container" style="margin-top: 1rem;">
        <div class="logo">ONLINE VOTING SYSTEM <span style="font-size:0.8rem; color:var(--danger);">ADMIN</span></div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container" style="max-width: 600px;">
        <div class="glass-panel" style="padding: 2.5rem;">
            <h2 style="margin-bottom: 1.5rem;">Add New Candidate</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php
endif; ?>
            
            <form action="add_candidate.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Candidate Full Name</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Political Party </label>
                    <input type="text" name="party" class="form-control" required value="<?php echo isset($party) ? htmlspecialchars($party) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Candidate Image (Optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*" style="padding: 0.5rem;">
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.5rem;">Recommended size: Square (500x500px). Formats: JPG, PNG, GIF.</small>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-danger" style="flex: 1;">Save Candidate</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
