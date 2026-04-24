<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

$admin_username = $_SESSION['admin_username'];

// Get statistics
$total_voters = $conn->query("SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'] ?? 0;
$voted_users = $conn->query("SELECT COUNT(id) as total FROM users WHERE has_voted = 1")->fetch_assoc()['total'] ?? 0;
$total_candidates = $conn->query("SELECT COUNT(id) as total FROM candidates")->fetch_assoc()['total'] ?? 0;
$total_votes = $conn->query("SELECT SUM(votes) as total FROM candidates")->fetch_assoc()['total'] ?? 0;

// Get candidates list
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
        <div class="logo">ONLINE VOTING SYSTEM <span style="font-size:0.8rem; color:var(--danger);">ADMIN</span></div>
        <div class="nav-links">
            <span style="margin-right: 1.5rem; color: var(--text-secondary);">Logged in as <?php echo htmlspecialchars($admin_username); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?>"><?php echo htmlspecialchars($msg); ?></div>
        <?php
endif; ?>

        <div class="dashboard-header">
            <h2>Election Overview</h2>
            <a href="add_candidate.php" class="btn" style="width: auto;">+ Add Candidate</a>
        </div>

        <div class="analytics-panel glass-panel">
            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($total_voters); ?></div>
                    <div class="stat-label">Registered Voters</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($voted_users); ?></div>
                    <div class="stat-label">Votes Cast</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($total_candidates); ?></div>
                    <div class="stat-label">Candidates</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value" style="background: linear-gradient(to right, #10b981, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo $total_voters > 0 ? round(($voted_users / $total_voters) * 100, 1) : 0; ?>%
                    </div>
                    <div class="stat-label">Turnout</div>
                </div>
            </div>
        </div>

        <div class="glass-panel" style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Candidates Manage List</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Party</th>
                            <th>Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($candidates->num_rows > 0): ?>
                            <?php while ($row = $candidates->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php $img_src = empty($row['image']) ? '../assets/css/default-user.png' : '../uploads/' . htmlspecialchars($row['image']); ?>
                                        <img src="<?php echo $img_src; ?>" alt="Candidate" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid var(--primary-color);">
                                    </td>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                                    <td>
                                        <span style="background: rgba(99, 102, 241, 0.2); padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: bold;">
                                            <?php echo number_format($row['votes']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_candidate.php?id=<?php echo $row['id']; ?>" class="btn" style="padding: 0.5rem 1rem; width: auto; font-size: 0.875rem; margin-right: 0.5rem; background: linear-gradient(135deg, #10b981, #047857);">Edit</a>
                                        <form action="delete_candidate.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; width: auto; font-size: 0.875rem;" onclick="return confirm('Delete this candidate forever? This cannot be undone.');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
    endwhile; ?>
                        <?php
else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No candidates found. Click "Add Candidate" to begin.</td>
                            </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>