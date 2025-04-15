<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: login.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <header>
                <h1>Quality Control Tracker</h1>
                <nav>
                    <ul>
                        <li><a href="index.php">Dashboard</a></li>
                        <li><a href="add_record.php">Add QC Record</a></li>
                        <li><a href="reports.php">Reports</a></li>
                        <li><a href="?logout=1">Logout</a></li>
                    </ul>
                </nav>
            </header>
            
            <main>
                <h2>Dashboard</h2>
                
                <div class="stats-container">
                    <div class="stat-box">
                        <h3>Total Records</h3>
                        <p class="stat-number"><?php echo countTotalRecords($conn); ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Passed QC</h3>
                        <p class="stat-number"><?php echo countPassedRecords($conn); ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Failed QC</h3>
                        <p class="stat-number"><?php echo countFailedRecords($conn); ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Pass Rate</h3>
                        <p class="stat-number"><?php echo calculatePassRate($conn); ?>%</p>
                    </div>
                </div>
                
                <h3>Recent Quality Control Records</h3>
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Batch #</th>
                            <th>Date</th>
                            <th>Inspector</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $records = getRecentRecords($conn, 10);
                        foreach ($records as $record) {
                            echo "<tr>";
                            echo "<td>{$record['id']}</td>";
                            echo "<td>{$record['product_name']}</td>";
                            echo "<td>{$record['batch_number']}</td>";
                            echo "<td>{$record['inspection_date']}</td>";
                            echo "<td>{$record['inspector_name']}</td>";
                            echo "<td class='" . ($record['status'] == 'Pass' ? 'pass' : 'fail') . "'>{$record['status']}</td>";
                            echo "<td><a href='view_record.php?id={$record['id']}'>View</a> | <a href='edit_record.php?id={$record['id']}'>Edit</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                
                <a href="records.php" class="view-all">View All Records</a>
            </main>
        <?php else: ?>
            <div class="login-prompt">
                <h2>Please log in to access the Quality Control Tracker</h2>
                <a href="login.php" class="btn">Login</a>
            </div>
        <?php endif; ?>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>