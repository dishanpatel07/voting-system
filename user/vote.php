<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidate_id'])) {
    $user_id = $_SESSION['user_id'];
    $candidate_id = intval($_POST['candidate_id']);

    // Check if user has already voted
    $stmt = $conn->prepare("SELECT has_voted FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if ($user['has_voted']) {
        $_SESSION['msg'] = "You have already cast your vote!";
        $_SESSION['msg_type'] = "error";
    } else {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Update candidate votes
            $update_votes = $conn->prepare("UPDATE candidates SET votes = votes + 1 WHERE id = ?");
            $update_votes->bind_param("i", $candidate_id);
            $update_votes->execute();

            // Update user status
            $update_user = $conn->prepare("UPDATE users SET has_voted = 1 WHERE id = ?");
            $update_user->bind_param("i", $user_id);
            $update_user->execute();

            $conn->commit();

            $_SESSION['has_voted'] = 1;
            $_SESSION['msg'] = "Your vote has been cast successfully! You have already cast your vote.";
            $_SESSION['msg_type'] = "success";

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['msg'] = "An error occurred while casting your vote. Please try again.";
            $_SESSION['msg_type'] = "error";
        }
    }
} else {
    $_SESSION['msg'] = "Invalid request.";
    $_SESSION['msg_type'] = "error";
}

header("Location: dashboard.php");
exit;
?>