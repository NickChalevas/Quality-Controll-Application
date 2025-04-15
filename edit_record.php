<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$success_message = '';
$error_message = '';

// Get record ID from URL
$record_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$record_id) {
    header('Location: records.php');
    exit;
}

// Get record details
$sql = "SELECT * FROM qc_records WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: records.php');
    exit;
}

$record = $result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $batch_number = filter_input(INPUT_POST, 'batch_number', FILTER_SANITIZE_STRING);
    $inspection_date = filter_input(INPUT_POST, 'inspection_date', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (!$product_id || empty($batch_number) || empty($inspection_date) || empty($status)) {
        $error_message = 'Please fill in all required fields';
    } else {
        // Update record in database
        $sql = "UPDATE qc_records 
                SET product_id = ?, batch_number = ?, inspection_date = ?, status = ?, notes = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $product_id, $batch_number, $inspection_date, $status, $notes, $record_id);
        
        if ($stmt->execute()) {
            $success_message = 'Quality control record updated successfully';
            
            // Refresh record data
            $sql = "SELECT * FROM qc_records WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $record_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $record = $result->fetch_assoc();
        } else {
            $error_message = 'Error updating record: ' . $conn->error;
        }
    }
}

// Get products for dropdown
$products = getProducts($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - Edit Record</title>
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
                <h2>Edit Quality Control Record #<?php echo $record_id; ?></h2>
                <div class="record-actions">
                    <a href="view_record.php?id=<?php echo $record_id; ?>" class="btn">View Record</a>
                    <a href="records.php" class="btn">Back to Records</a>
                </div>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="post" action="edit_record.php?id=<?php echo $record_id; ?>" class="form">
                <div class="form-group">
                    <label for="product_id">Product *</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">Select Product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo ($record['product_id'] == $product['id']) ? 'selected' : ''; ?>>
                                <?php echo $product['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="batch_number">Batch Number *</label>
                    <input type="text" id="batch_number" name="batch_number" value="<?php echo $record['batch_number']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="inspection_date">Inspection Date *</label>
                    <input type="date" id="inspection_date" name="inspection_date" value="<?php echo $record['inspection_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="status" value="Pass" <?php echo ($record['status'] === 'Pass') ? 'checked' : ''; ?> required> Pass
                        </label>
                        <label>
                            <input type="radio" name="status" value="Fail" <?php echo ($record['status'] === 'Fail') ? 'checked' : ''; ?>> Fail
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="4"><?php echo $record['notes']; ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="view_record.php?id=<?php echo $record_id; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>