<?php
require '../connection/db.php';

echo "<h2>Feedback Database Fixer</h2>";

// 1. Delete the table if a broken/old version exists
$conn->query("DROP TABLE IF EXISTS feedback");

// 2. Create it perfectly from scratch
$sql = "CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_type VARCHAR(50),
    feedback_date DATE,
    sex VARCHAR(10),
    age INT,
    services TEXT,
    cc1 INT,
    cc2 INT,
    sqd0 INT, sqd1 INT, sqd2 INT, sqd3 INT, sqd4 INT, 
    sqd5 INT, sqd6 INT, sqd7 INT, sqd8 INT,
    suggestions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<strong style='color: green;'>SUCCESS: The 'feedback' table has been perfectly created with the 'services' column!</strong><br><br>";
    echo "You can now go back to your feedback page and try submitting the form again.";
} else {
    echo "<strong style='color: red;'>ERROR: </strong>" . $conn->error;
}

$conn->close();
