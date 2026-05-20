<?php
require '../connection/db.php';

echo "<h2>Announcements Database Fixer</h2>";

// 1. Delete the table if a broken/old version exists
$conn->query("DROP TABLE IF EXISTS announcements");

// 2. Create it perfectly from scratch
$sql = "CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_pinned TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<strong style='color: green;'>SUCCESS: The 'announcements' table has been perfectly created with the 'author' column!</strong><br><br>";
    echo "You can now go back to your announcements page and try sending a message.";
} else {
    echo "<strong style='color: red;'>ERROR: </strong>" . $conn->error;
}

$conn->close();
