<?php
// 1. Start session and connect to the database
session_start();
require '../connection/db.php'; // Ensure this file is in the same folder, or adjust the path!

// 2. Set welcome name
$welcomeName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <nav class="doj-nav">

        <div class="sidebar-profile">
            <img src="../images/image.png" alt="Prosecutor Logo">
            <div class="sidebar-welcome-name">Welcome, <?php echo htmlspecialchars($welcomeName); ?>!</div>
            <div class="sidebar-system-title">Office of the Prosecutor<br>Management System</div>
            <a href="../logout.php" class="btn-sidebar-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a>
        </div>
        <ul class="nav-links">
            <li><a href="case.php">Case</a></li>
            <li><a href="clearance.php">Clearance</a></li>
            <li><a href="announcement.php">Announcements</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contactus.php">Contact Us</a></li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </nav>

    <main class="content-area">

        <div class="floating-header">
            <div class="logo-section">
                <div class="title-group">
                    <span> Office of the Prosecutor Management System</span>

                </div>
            </div>
        </div>

    </main>

    <script src="../script.js"></script>

</body>

</html>