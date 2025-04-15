<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get record ID from URL
$record_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$record_id) {
    header('Location: records.php');
    exit;
}

// Get record details
$sql = "SELECT r.*, p.name as product_name, u.name as inspector_name 
        FROM qc_records r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.inspector_id = u.id
        WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: records.php');
    exit;
}

$record = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - View Record</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Quality Control Tracker</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="add_record.php">Add QC Record</a></li>
                    <li><a href="records.php">Records</a></li>
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <div class="record-header">
                <h2>Quality Control Record #<?php echo $record['id']; ?></h2>
                <div class="record-actions">
                    <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="btn btn-secondary">Edit Record</a>
                    <a href="records.php" class="btn">Back to Records</a>
                </div>
            </div>
            
            <div class="record-details">
                <div class="record-status <?php echo strtolower($record['status']); ?>">
                    <?php echo $record['status']; ?>
                </div>
                
                <div class="detail-section">
                    <h3>Product Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Product:</div>
                        <div class="detail-value"><?php echo $record['product_name']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Batch Number:</div>
                        <div class="detail-value"><?php echo $record['batch_number']; ?></div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>Inspection Details</h3>
                    <div class="detail-row">
                        <div class="detail-label">Inspection Date:</div>
                        <div class="detail-value"><?php echo $record['inspection_date']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Inspector:</div>
                        <div class="detail-value"><?php echo $record['inspector_name']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value status-<?php echo strtolower($record['status']); ?>"><?php echo $record['status']; ?></div>
                    </div>
                </div>
                
                <?php if (!empty($record['notes'])): ?>
                <div class="detail-section">
                    <h3>Notes</h3>
                    <div class="detail-notes">
                        <?php echo nl2br(htmlspecialchars($record['notes'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="detail-section">
                    <h3>Record Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Created:</div>
                        <div class="detail-value"><?php echo $record['created_at']; ?></div>
                    </div>
                    <?php if ($record['updated_at'] !== $record['created_at']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Last Updated:</div>
                        <div class="detail-value"><?php echo $record['updated_at']; ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>