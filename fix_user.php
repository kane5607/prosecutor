<?php
require '../connection/db.php';

echo "<h2>Users Table Updater</h2>";

// List of all the new columns we need for the settings page
$queries = [
    "ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT 'default-avatar.png'",
    "ALTER TABLE users ADD COLUMN first_name VARCHAR(100)",
    "ALTER TABLE users ADD COLUMN middle_name VARCHAR(100)",
    "ALTER TABLE users ADD COLUMN last_name VARCHAR(100)",
    "ALTER TABLE users ADD COLUMN address TEXT",
    "ALTER TABLE users ADD COLUMN age INT",
    "ALTER TABLE users ADD COLUMN dob DATE",
    "ALTER TABLE users ADD COLUMN gender VARCHAR(20)",
    "ALTER TABLE users ADD COLUMN contact_no VARCHAR(50)",
    "ALTER TABLE users ADD COLUMN email VARCHAR(100)"
];

// Run each query one by one
foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "<p style='color: green;'><strong>ADDED:</strong> Successfully added a new column.</p>";
    } else {
        echo "<p style='color: #64748b;'><em>SKIPPED:</em> Column likely already exists (" . $conn->error . ")</p>";
    }
}

echo "<h3 style='color: #002e5d;'>✅ Done! Go back to your Settings page and try saving again.</h3>";
$conn->close();
