<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidate_id'])) {
    $candidate_id = intval($_POST['candidate_id']);

    // Fetch image to delete from server
    $stmt = $conn->prepare("SELECT image FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $candidate = $res->fetch_assoc();
        if (!empty($candidate['image'])) {
            $img_path = "../uploads/" . $candidate['image'];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
        }

        $delete_stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
        $delete_stmt->bind_param("i", $candidate_id);

        if ($delete_stmt->execute()) {
            $_SESSION['msg'] = "Candidate deleted successfully.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Failed to delete candidate.";
            $_SESSION['msg_type'] = "error";
        }
        $delete_stmt->close();
    } else {
        $_SESSION['msg'] = "Candidate not found.";
        $_SESSION['msg_type'] = "error";
    }
    $stmt->close();
} else {
    $_SESSION['msg'] = "Invalid request.";
    $_SESSION['msg_type'] = "error";
}

header("Location: dashboard.php");
exit;
?>