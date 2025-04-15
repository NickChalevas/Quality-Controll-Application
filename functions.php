<?php
// Count total QC records
function countTotalRecords($conn) {
    $sql = "SELECT COUNT(*) as total FROM qc_records";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Count passed QC records
function countPassedRecords($conn) {
    $sql = "SELECT COUNT(*) as passed FROM qc_records WHERE status = 'Pass'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['passed'];
}

// Count failed QC records
function countFailedRecords($conn) {
    $sql = "SELECT COUNT(*) as failed FROM qc_records WHERE status = 'Fail'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['failed'];
}

// Calculate pass rate
function calculatePassRate($conn) {
    $total = countTotalRecords($conn);
    if ($total == 0) return 0;
    
    $passed = countPassedRecords($conn);
    return round(($passed / $total) * 100, 1);
}

// Get recent QC records
function getRecentRecords($conn, $limit = 10) {
    $sql = "SELECT r.*, p.name as product_name, u.name as inspector_name 
            FROM qc_records r
            JOIN products p ON r.product_id = p.id
            JOIN users u ON r.inspector_id = u.id
            ORDER BY r.inspection_date DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    return $records;
}

// Get all QC records with optional filtering
function getAllRecords($conn, $filters = []) {
    $sql = "SELECT r.*, p.name as product_name, u.name as inspector_name 
            FROM qc_records r
            JOIN products p ON r.product_id = p.id
            JOIN users u ON r.inspector_id = u.id
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (!empty($filters['product_id'])) {
        $sql .= " AND r.product_id = ?";
        $params[] = $filters['product_id'];
        $types .= "i";
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND r.status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }
    
    if (!empty($filters['date_from'])) {
        $sql .= " AND r.inspection_date >= ?";
        $params[] = $filters['date_from'];
        $types .= "s";
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND r.inspection_date <= ?";
        $params[] = $filters['date_to'];
        $types .= "s";
    }
    
    $sql .= " ORDER BY r.inspection_date DESC";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    return $records;
}

// Get products for dropdown
function getProducts($conn) {
    $sql = "SELECT id, name FROM products ORDER BY name";
    $result = $conn->query($sql);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

// Authenticate user
function authenticateUser($conn, $username, $password) {
    $sql = "SELECT id, name, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'name' => $user['name']
            ];
        }
    }
    
    return false;
}