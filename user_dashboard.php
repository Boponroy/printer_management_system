<?php

session_start();
require_once 'loginConfig.php';

if (isset($_SESSION['email'])) {
    $user_email = $_SESSION['email'];
} else {header("Location: index.php");
}

$pages_count = 0;
$sql = "SELECT pages FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email); 
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pages_count = $row['pages'];
}

$stmt->close();
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_dashboard.css">
</head>
<body>
    <nav>
        <a href="upload_print.php">Upload Print Job</a>
        <a href="">View Active Job</a>
        <a href="">Print History</a>
        <button onclick="window.location.href='logout.php'">Log Out</button>
    </nav>

    <div class="container">
        <div class="box">
            <h1>Welcome <span><?= $_SESSION['name']; ?></span></h1>
            <div class="page">
                <p>Total page remaining</p>
                <div class="page-box">
                    <span><?= $pages_count; ?></span>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>