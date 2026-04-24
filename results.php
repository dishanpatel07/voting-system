<?php
session_start();
require_once "includes/db.php";

$total_votes = $conn->query("SELECT SUM(votes) as total FROM candidates")->fetch_assoc()['total'] ?? 0;
$candidates = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");

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
    <nav class="navbar glass-panel container" style="margin-top: 1rem;">
        <div class="logo">ONLINE VOTING SYSTEM <span style="font-size:0.8rem; color:var(--success);">LIVE RESULTS</span></div>
        <div class="nav-links">
            <a href="index.php">Back to Home</a>
        </div>
    </nav>

    <div class="container">
        <h2 style="text-align: center; margin-bottom: 2rem; font-size: 2.5rem;">Live Election Standings</h2>
        
        <div style="text-align: center; margin-bottom: 3rem;">
            <div style="font-size: 1.2rem; color: var(--text-secondary);">Total Votes Cast</div>
            <div style="font-size: 3rem; font-weight: 800; color: var(--success);"><?php echo number_format($total_votes); ?></div>
        </div>

        <div class="candidates-grid" style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">
            <?php if ($candidates->num_rows > 0): ?>
                <?php
    $rank = 1;
    while ($row = $candidates->fetch_assoc()):
        $percentage = $total_votes > 0 ? round(($row['votes'] / $total_votes) * 100, 1) : 0;
?>
                    <div class="glass-panel" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.5rem;">
                        <div style="font-size: 2rem; font-weight: 800; color: var(--text-secondary); width: 40px; text-align: center;">
                            #<?php echo $rank++; ?>
                        </div>
                        
                        <?php $img_src = empty($row['image']) ? 'assets/css/default-user.png' : 'uploads/' . htmlspecialchars($row['image']); ?>
                        <img src="<?php echo $img_src; ?>" alt="Candidate" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-color);">
                        
                        <div style="flex-grow: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                <div>
                                    <h3 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($row['name']); ?></h3>
                                    <div style="color: var(--text-secondary); font-size: 0.875rem;"><?php echo htmlspecialchars($row['party']); ?></div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 800; font-size: 1.25rem; color: var(--secondary-color);"><?php echo number_format($row['votes']); ?> Votes</div>
                                    <div style="font-weight: 600; font-size: 1rem;"><?php echo $percentage; ?>%</div>
                                </div>
                            </div>
                            
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?php echo $percentage; ?>%;"></div>
                            </div>
                        </div>
                    </div>
                <?php
    endwhile; ?>
            <?php
else: ?>
                <div style="text-align: center; color: var(--text-secondary); padding: 3rem;">No candidates available yet.</div>
            <?php
endif; ?>
        </div>
    </div>
</body>
</html>
