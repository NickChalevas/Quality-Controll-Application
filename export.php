<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in and is admin or manager
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if user has appropriate role
$sql = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin' && $user['role'] !== 'manager') {
    header('Location: index.php');
    exit;
}

// Process export request
if (isset($_POST['export']) && $_POST['export'] === 'records') {
    // Get filter parameters
    $filters = [
        'product_id' => filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT),
        'status' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING),
        'date_from' => filter_input(INPUT_POST, 'date_from', FILTER_SANITIZE_STRING),
        'date_to' => filter_input(INPUT_POST, 'date_to', FILTER_SANITIZE_STRING)
    ];
    
    // Get records based on filters
    $records = getAllRecords($conn, $filters);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="qc_records_export_' . date('Y-m-d') . '.csv"');
    
    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    
    // Output the column headings
    fputcsv($output, ['ID', 'Product', 'Batch Number', 'Inspection Date', 'Inspector', 'Status', 'Notes', 'Created At']);
    
    // Output each row of the data
    foreach ($records as $record) {
        fputcsv($output, [
            $record['id'],
            $record['product_name'],
            $record['batch_number'],
            $record['inspection_date'],
            $record['inspector_name'],
            $record['status'],
            $record['notes'],
            $record['created_at']
        ]);
    }
    
    // Close the file pointer
    fclose($output);
    exit;
}

// Get products for filter dropdown
$products = getProducts($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - Export Data</title>
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
                    <?php if ($user['role'] === 'admin'): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <h2>Export Data</h2>
            
            <div class="export-section">
                <h3>Export Quality Control Records</h3>
                <p>Export QC records as a CSV file. You can filter the data before exporting.</p>
                
                <form method="post" action="export.php" class="form">
                    <input type="hidden" name="export" value="records">
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="product_id">Product</label>
                            <select id="product_id" name="product_id">
                                <option value="">All Products</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>">
                                        <?php echo $product['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="">All</option>
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="date_from">Date From</label>
                            <input type="date" id="date_from" name="date_from">
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_to">Date To</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Export to CSV</button>
                    </div>
                </form>
            </div>
            
            <?php if ($user['role'] === 'admin'): ?>
            <div class="export-section">
                <h3>Export System Data</h3>
                <p>Export system data for backup purposes.</p>
                
                <div class="export-options">
                    <a href="export.php?type=users" class="btn">Export Users</a>
                    <a href="export.php?type=products" class="btn">Export Products</a>
                    <a href="export.php?type=all" class="btn">Export All Data</a>
                </div>
            </div>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>