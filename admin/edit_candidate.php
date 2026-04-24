<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

$error = "";
$success = "";
$candidate = null;

// Fetch candidate data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $candidate = $res->fetch_assoc();
    }
    else {
        $_SESSION['msg'] = "Candidate not found.";
        $_SESSION['msg_type'] = "error";
        header("Location: dashboard.php");
        exit;
    }
    $stmt->close();
}
else {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $party = trim($_POST['party']);

    // Check if new image was uploaded
    $update_image = false;
    $new_image_name = $candidate['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_image_name = time() . '_' . uniqid() . '.' . $ext;
            $destination = "../uploads/" . $new_image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $update_image = true;
                // Delete old image
                if (!empty($candidate['image'])) {
                    $old_path = "../uploads/" . $candidate['image'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }
            else {
                $error = "Failed to upload new image.";
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
        $stmt = $conn->prepare("UPDATE candidates SET name = ?, party = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $party, $new_image_name, $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Candidate updated successfully!";
            $_SESSION['msg_type'] = "success";
            header("Location: dashboard.php");
            exit;
        }
        else {
            $error = "Database error. Failed to update candidate.";
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
        <div class="logo">ONLINE VOTING SYSTEM</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container" style="max-width: 600px;">
        <div class="glass-panel" style="padding: 2.5rem;">
            <h2 style="margin-bottom: 1.5rem;">Edit Candidate</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php
endif; ?>
            
            <form action="edit_candidate.php?id=<?php echo $candidate['id']; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $candidate['id']; ?>">
                
                <div class="form-group">
                    <label>Candidate Full Name</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($name ?? $candidate['name']); ?>">
                </div>
                <div class="form-group">
                    <label>Political Party / Affiliation</label>
                    <input type="text" name="party" class="form-control" required value="<?php echo htmlspecialchars($party ?? $candidate['party']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Current Image</label>
                    <div style="margin-bottom: 1rem;">
                        <?php $img_src = empty($candidate['image']) ? '../assets/css/default-user.png' : '../uploads/' . htmlspecialchars($candidate['image']); ?>
                        <img src="<?php echo $img_src; ?>" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px; object-fit: cover;">
                    </div>
                    <label>Upload New Image (Leave blank to keep current)</label>
                    <input type="file" name="image" class="form-control" accept="image/*" style="padding: 0.5rem;">
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn" style="flex: 1; background: linear-gradient(135deg, var(--success), #047857);">Update Candidate</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
