<?php
// This is a temporary script to reset the admin password
// Delete this file after using it for security reasons

require_once 'config.php';

// Set new password
$username = 'admin';
$new_password = 'password';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the password in the database
$sql = "UPDATE users SET password = ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $username);

if ($stmt->execute()) {
    echo "Password for user '{$username}' has been reset to '{$new_password}'.<br>";
    echo "Please delete this file immediately for security reasons.";
} else {
    echo "Error resetting password: " . $conn->error;
}
?>