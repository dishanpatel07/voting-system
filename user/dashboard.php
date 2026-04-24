<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../includes/db.php";

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Refresh user status
$stmt = $conn->prepare("SELECT has_voted FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$has_voted = $user_data['has_voted'];
$_SESSION['has_voted'] = $has_voted;
$stmt->close();

// Get total votes and candidates
$total_votes = $conn->query("SELECT SUM(votes) as total FROM candidates")->fetch_assoc()['total'] ?? 0;
$total_candidates = $conn->query("SELECT COUNT(id) as total FROM candidates")->fetch_assoc()['total'] ?? 0;

// Get candidates
$candidates = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");

$msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
$msg_type = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : '';
unset($_SESSION['msg']);
unset($_SESSION['msg_type']);
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
            <span style="margin-right: 1.5rem; color: var(--text-secondary);">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?>"><?php echo htmlspecialchars($msg); ?></div>
        <?php
endif; ?>

        <div class="analytics-panel glass-panel">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($total_candidates); ?></div>
                    <div class="stat-label">Candidates</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($total_votes); ?></div>
                    <div class="stat-label">Total Votes Cast</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="color: <?php echo $has_voted ? 'var(--success)' : 'var(--danger)'; ?>; -webkit-text-fill-color: initial;">
                        <?php echo $has_voted ? 'VOTED' : 'PENDING'; ?>
                    </div>
                    <div class="stat-label">Your Status</div>
                </div>
            </div>
        </div>

        <h2 style="margin-bottom: 2rem; font-size: 2rem;">Voter Dashboard</h2>

        <div class="candidates-grid">
            <?php if ($candidates->num_rows > 0): ?>
                <?php while ($row = $candidates->fetch_assoc()): ?>
                    <div class="candidate-card glass-panel">
                        <?php
        $img_src = empty($row['image']) ? '../assets/css/default-user.png' : '../uploads/' . htmlspecialchars($row['image']);
?>
                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="candidate-img" onerror="this.src='https://via.placeholder.com/120?text=No+Image';">
                        
                        <h3 class="candidate-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="candidate-party"><?php echo htmlspecialchars($row['party']); ?></div>
                        
                        <?php if ($has_voted): ?>
                            <div class="vote-stats">
                                <?php echo number_format($row['votes']); ?> Votes
                                <?php if ($total_votes > 0): ?>
                                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); margin-top: 0.5rem;">
                                        <?php echo round(($row['votes'] / $total_votes) * 100, 1); ?>%
                                    </div>
                                <?php
            endif; ?>
                            </div>
                        <?php
        else: ?>
                            <form action="vote.php" method="POST" style="margin-top: 1rem;">
                                <input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn">Vote for <?php echo htmlspecialchars(explode(' ', $row['name'])[0]); ?></button>
                            </form>
                        <?php
        endif; ?>
                    </div>
                <?php
    endwhile; ?>
            <?php
else: ?>
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary);">No candidates available yet.</p>
            <?php
endif; ?>
        </div>
    </div>
</body>
</html>
