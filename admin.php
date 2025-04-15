<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if user is admin
$sql = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Get system stats
$stats = [
    'total_users' => 0,
    'total_products' => 0,
    'total_records' => 0,
    'pass_rate' => 0
];

// Count users
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$stats['total_users'] = $row['count'];

// Count products
$sql = "SELECT COUNT(*) as count FROM products";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$stats['total_products'] = $row['count'];

// Count records
$sql = "SELECT COUNT(*) as count FROM qc_records";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$stats['total_records'] = $row['count'];

// Calculate pass rate
$sql = "SELECT 
            ROUND((SUM(CASE WHEN status = 'Pass' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as pass_rate
        FROM qc_records";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$stats['pass_rate'] = $row['pass_rate'] ?? 0;

// Get recent activity
$sql = "SELECT 
            'qc_record' as type,
            r.id,
            p.name as product_name,
            r.batch_number,
            r.status,
            u.name as user_name,
            r.created_at
        FROM qc_records r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.inspector_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 10";
$result = $conn->query($sql);

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QC Tracker - Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .admin-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        
        .admin-card-title {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        
        .admin-card-value {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .admin-card-link {
            margin-top: auto;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .admin-section {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        
        .admin-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .admin-section-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-content {
            margin-bottom: 0.25rem;
        }
        
        .activity-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-badge.active {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .status-badge.inactive {
            background-color: #fee2e2;
            color: #dc2626;
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
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="admin.php" class="active">Admin</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <h2>Admin Dashboard</h2>
            
            <div class="admin-cards">
                <div class="admin-card">
                    <div class="admin-card-title">Total Users</div>
                    <div class="admin-card-value"><?php echo $stats['total_users']; ?></div>
                    <a href="user_management.php" class="admin-card-link">Manage Users →</a>
                </div>
                
                <div class="admin-card">
                    <div class="admin-card-title">Total Products</div>
                    <div class="admin-card-value"><?php echo $stats['total_products']; ?></div>
                    <a href="product_management.php" class="admin-card-link">Manage Products →</a>
                </div>
                
                <div class="admin-card">
                    <div class="admin-card-title">Total QC Records</div>
                    <div class="admin-card-value"><?php echo $stats['total_records']; ?></div>
                    <a href="records.php" class="admin-card-link">View Records →</a>
                </div>
                
                <div class="admin-card">
                    <div class="admin-card-title">Overall Pass Rate</div>
                    <div class="admin-card-value"><?php echo $stats['pass_rate']; ?>%</div>
                    <a href="reports.php" class="admin-card-link">View Reports →</a>
                </div>
            </div>
            
            <div class="admin-sections">
                <div class="admin-section">
                    <div class="admin-section-header">
                        <div class="admin-section-title">Admin Tools</div>
                    </div>
                    
                    <div class="admin-tools">
                        <a href="user_management.php" class="btn btn-primary" style="margin-bottom: 0.5rem; width: 100%;">User Management</a>
                        <a href="product_management.php" class="btn btn-primary" style="margin-bottom: 0.5rem; width: 100%;">Product Management</a>
                        <a href="export.php" class="btn btn-primary" style="margin-bottom: 0.5rem; width: 100%;">Export Data</a>
                        <a href="system_settings.php" class="btn btn-primary" style="width: 100%;">System Settings</a>
                    </div>
                </div>
                
                <div class="admin-section">
                    <div class="admin-section-header">
                        <div class="admin-section-title">Recent Activity</div>
                        <a href="activity_log.php" class="btn">View All</a>
                    </div>
                    
                    <ul class="activity-list">
                        <?php if (empty($activities)): ?>
                            <li class="activity-item">No recent activity.</li>
                        <?php else: ?>
                            <?php foreach ($activities as $activity): ?>
                                <li class="activity-item">
                                    <div class="activity-content">
                                        <strong><?php echo $activity['user_name']; ?></strong> 
                                        added a new QC record for 
                                        <strong><?php echo $activity['product_name']; ?></strong> 
                                        (Batch: <?php echo $activity['batch_number']; ?>) 
                                        with status 
                                        <span class="status-badge <?php echo strtolower($activity['status']); ?>">
                                            <?php echo $activity['status']; ?>
                                        </span>
                                    </div>
                                    <div class="activity-meta">
                                        <?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Quality Control Tracker</p>
        </footer>
    </div>
</body>
</html>