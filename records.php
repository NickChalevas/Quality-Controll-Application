<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get filter parameters
$filters = [
    'product_id' => filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT),
    'status' => filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING),
    'date_from' => filter_input(INPUT_GET, 'date_from', FILTER_SANITIZE_STRING),
    'date_to' => filter_input(INPUT_GET, 'date_to', FILTER_SANITIZE_STRING)
];

// Get records based on filters
$records = getAllRecords($conn, $filters);

// Get products for filter dropdown
$products = getProducts($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - Records</title>
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
                    <li><a href="records.php" class="active">Records</a></li>
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <h2>Quality Control Records</h2>
            
            <div class="filter-section">
                <h3>Filter Records</h3>
                <form method="get" action="records.php" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="product_id">Product</label>
                            <select id="product_id" name="product_id">
                                <option value="">All Products</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" <?php echo ($filters['product_id'] == $product['id']) ? 'selected' : ''; ?>>
                                        <?php echo $product['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="">All</option>
                                <option value="Pass" <?php echo ($filters['status'] === 'Pass') ? 'selected' : ''; ?>>Pass</option>
                                <option value="Fail" <?php echo ($filters['status'] === 'Fail') ? 'selected' : ''; ?>>Fail</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="date_from">Date From</label>
                            <input type="date" id="date_from" name="date_from" value="<?php echo $filters['date_from']; ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_to">Date To</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo $filters['date_to']; ?>">
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="records.php" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="records-container">
                <?php if (empty($records)): ?>
                    <p class="no-records">No records found matching your criteria.</p>
                <?php else: ?>
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
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo $record['id']; ?></td>
                                    <td><?php echo $record['product_name']; ?></td>
                                    <td><?php echo $record['batch_number']; ?></td>
                                    <td><?php echo $record['inspection_date']; ?></td>
                                    <td><?php echo $record['inspector_name']; ?></td>
                                    <td class="<?php echo strtolower($record['status']); ?>"><?php echo $record['status']; ?></td>
                                    <td>
                                        <a href="view_record.php?id=<?php echo $record['id']; ?>" class="action-link">View</a> | 
                                        <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="action-link">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>