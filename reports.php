<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get date range for reports
$date_from = filter_input(INPUT_GET, 'date_from', FILTER_SANITIZE_STRING) ?: date('Y-m-d', strtotime('-30 days'));
$date_to = filter_input(INPUT_GET, 'date_to', FILTER_SANITIZE_STRING) ?: date('Y-m-d');

// Get report data
$sql = "SELECT 
            p.name as product_name,
            COUNT(*) as total_inspections,
            SUM(CASE WHEN r.status = 'Pass' THEN 1 ELSE 0 END) as passed,
            SUM(CASE WHEN r.status = 'Fail' THEN 1 ELSE 0 END) as failed,
            ROUND((SUM(CASE WHEN r.status = 'Pass' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as pass_rate
        FROM qc_records r
        JOIN products p ON r.product_id = p.id
        WHERE r.inspection_date BETWEEN ? AND ?
        GROUP BY p.id, p.name
        ORDER BY total_inspections DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

// Get overall stats
$sql = "SELECT 
            COUNT(*) as total_inspections,
            SUM(CASE WHEN status = 'Pass' THEN 1 ELSE 0 END) as passed,
            SUM(CASE WHEN status = 'Fail' THEN 1 ELSE 0 END) as failed,
            ROUND((SUM(CASE WHEN status = 'Pass' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as pass_rate
        FROM qc_records
        WHERE inspection_date BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();
$overall = $result->fetch_assoc();

// Get daily trend data
$sql = "SELECT 
            inspection_date,
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Pass' THEN 1 ELSE 0 END) as passed,
            SUM(CASE WHEN status = 'Fail' THEN 1 ELSE 0 END) as failed
        FROM qc_records
        WHERE inspection_date BETWEEN ? AND ?
        GROUP BY inspection_date
        ORDER BY inspection_date";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

$daily_data = [];
while ($row = $result->fetch_assoc()) {
    $daily_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - Reports</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .report-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .report-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .report-date {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .summary-label {
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .pass-rate-good {
            color: var(--success-color);
        }
        
        .pass-rate-warning {
            color: var(--warning-color);
        }
        
        .pass-rate-bad {
            color: var(--danger-color);
        }
        
        .trend-chart {
            height: 300px;
            margin-top: 1.5rem;
            background-color: var(--light-color);
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
        }
    </style>
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
                    <li><a href="reports.php" class="active">Reports</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <h2>Quality Control Reports</h2>
            
            <div class="filter-section">
                <form method="get" action="reports.php" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="date_from">Date From</label>
                            <input type="date" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_to">Date To</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="report-card">
                <div class="report-header">
                    <div class="report-title">Overall Quality Performance</div>
                    <div class="report-date"><?php echo date('M d, Y', strtotime($date_from)); ?> - <?php echo date('M d, Y', strtotime($date_to)); ?></div>
                </div>
                
                <div class="report-summary">
                    <div class="summary-item">
                        <div class="summary-value"><?php echo $overall['total_inspections']; ?></div>
                        <div class="summary-label">Total Inspections</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value"><?php echo $overall['passed']; ?></div>
                        <div class="summary-label">Passed</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value"><?php echo $overall['failed']; ?></div>
                        <div class="summary-label">Failed</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value <?php 
                            if ($overall['pass_rate'] >= 90) echo 'pass-rate-good';
                            elseif ($overall['pass_rate'] >= 70) echo 'pass-rate-warning';
                            else echo 'pass-rate-bad';
                        ?>"><?php echo $overall['pass_rate']; ?>%</div>
                        <div class="summary-label">Pass Rate</div>
                    </div>
                </div>
                
                <h3>Daily Trend</h3>
                <div class="trend-chart">
                    [Chart visualization would be implemented here with a JavaScript library like Chart.js]
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-header">
                    <div class="report-title">Product Quality Performance</div>
                </div>
                
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Total Inspections</th>
                            <th>Passed</th>
                            <th>Failed</th>
                            <th>Pass Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No data available for the selected period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?php echo $report['product_name']; ?></td>
                                    <td><?php echo $report['total_inspections']; ?></td>
                                    <td><?php echo $report['passed']; ?></td>
                                    <td><?php echo $report['failed']; ?></td>
                                    <td class="<?php 
                                        if ($report['pass_rate'] >= 90) echo 'pass';
                                        elseif ($report['pass_rate'] >= 70) echo '';
                                        else echo 'fail';
                                    ?>"><?php echo $report['pass_rate']; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>