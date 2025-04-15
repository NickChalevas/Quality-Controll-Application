<?php
// This is a setup script to create or reset the admin user
// Delete this file after using it for security reasons

require_once 'config.php';

// Admin credentials
$username = 'admin';
$password = 'admin'; // Using 'admin' as the password as you mentioned
$name = 'Administrator';
$email = 'admin@example.com';
$role = 'admin';

// Check if admin user exists
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if ($result->num_rows > 0) {
    // Admin exists, update password
    $user = $result->fetch_assoc();
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $user['id']);
    
    if ($stmt->execute()) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<h2 style='color: #2563eb;'>Admin Password Reset</h2>";
        echo "<p>The password for user '<strong>{$username}</strong>' has been reset to '<strong>{$password}</strong>'.</p>";
        echo "<p><strong>Please delete this file immediately for security reasons.</strong></p>";
        echo "<p><a href='login.php' style='display: inline-block; background-color: #2563eb; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
        echo "</div>";
    } else {
        echo "Error resetting password: " . $conn->error;
    }
} else {
    // Admin doesn't exist, create new admin user
    $sql = "INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $hashed_password, $name, $email, $role);
    
    if ($stmt->execute()) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<h2 style='color: #2563eb;'>Admin User Created</h2>";
        echo "<p>A new admin user has been created with the following credentials:</p>";
        echo "<p>Username: <strong>{$username}</strong><br>Password: <strong>{$password}</strong></p>";
        echo "<p><strong>Please delete this file immediately for security reasons.</strong></p>";
        echo "<p><a href='login.php' style='display: inline-block; background-color: #2563eb; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
        echo "</div>";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
}
?>