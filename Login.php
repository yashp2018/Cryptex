<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";       // your database username
$password = "";           // your database password
$database = "cryptex";    // your database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive form data
$username_email = $_POST['username_email'];
$password_input = $_POST['password'];

// Search for user
$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username_email, $username_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password_input, $user['password_hash'])) {
        // Check if KYC is verified
        if ($user['kyc_verified']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            echo "Login successful! Welcome, " . htmlspecialchars($user['username']) . ".";
            // Redirect to dashboard (optional)
            // header("Location: dashboard.php");
        } else {
            echo "Your KYC is not verified. Please complete KYC verification.";
        }
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "No user found with that username or email.";
}

$stmt->close();
$conn->close();
?>
