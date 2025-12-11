<?php
// ------------------------------------------------------------------
// 1. CONFIGURATION AND SESSION START
// ------------------------------------------------------------------
session_start();

// !!! REPLACE THESE CREDENTIALS !!!
$servername = "localhost";
$dbUsername = "root"; 
$dbPassword = ""; 
$dbname = "print_management_db";
$target_dir = "uploads/";

// Get User ID from session
if (!isset($_SESSION['id'])) {
    die("ERROR: Not logged in.");
}
$loggedInId = $_SESSION['id'];

// Check form submission
if (!isset($_POST["uploadSubmit"])) {
    die("ERROR: Form not submitted.");
}

// ------------------------------------------------------------------
// 2. INPUT DATA AND CONNECTION
// ------------------------------------------------------------------
$pageCount = $_POST['pageCount']; // Get page count
$file = $_FILES['printFile'];
$originalFileName = basename($file["name"]);
$fileTmpName = $file["tmp_name"];

// Connect to Database
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("DB CONNECTION FAILED: " . $conn->connect_error);
}

// ------------------------------------------------------------------
// 3. CHECK PAGE BALANCE (Requires 'page_balance' column in 'users' table)
// ------------------------------------------------------------------
$stmt = $conn->prepare("SELECT pages FROM users WHERE id = ?");
$stmt->bind_param("i", $loggedInId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || $user['pages'] < $pageCount) {
    die("ERROR: Not enough pages or user not found.");
}

// ------------------------------------------------------------------
// 4. FILE MOVEMENT AND DEDUCTION
// ------------------------------------------------------------------

// Prepare unique file path
$fileExt = pathinfo($originalFileName, PATHINFO_EXTENSION);
$newFileName = uniqid('upload_', true) . "." . $fileExt;
$target_file = $target_dir . $newFileName;

// Move the file
if (!move_uploaded_file($fileTmpName, $target_file)) {
    die("ERROR: File save failed. Check uploads/ folder permissions.");
}

// Deduct pages
$newBalance = $user['page_balance'] - $pageCount;
$stmt = $conn->prepare("UPDATE users SET pages = ? WHERE id = ?");
$stmt->bind_param("ii", $newBalance, $loggedInId);

if (!$stmt->execute()) {
    // File saved, but DB update failed. This is a critical error.
    die("ERROR: Could not deduct pages from user balance."); 
}
$stmt->close();


// ------------------------------------------------------------------
// 5. INSERT UPLOAD RECORD
// ------------------------------------------------------------------
$sql = "INSERT INTO print_uploads (id, file_name, stored_path, page_count) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $loggedInId, $originalFileName, $target_file, $pageCount);

if ($stmt->execute()) {
    echo "SUCCESS: File uploaded and pages deducted. New balance: $newBalance.";
} else {
    echo "ERROR: File saved, pages deducted, but print record failed.";
}

$stmt->close();
$conn->close();
?>