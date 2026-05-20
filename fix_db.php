<?php
require '../connection/db.php';

echo "<h2>Database Diagnostic Tool</h2>";

// 1. Check which database we are ACTUALLY connected to
$result = $conn->query("SELECT DATABASE()");
$row = $result->fetch_row();
$actual_db = $row[0];
echo "<strong>PHP is currently connected to database:</strong> <span style='color:blue; font-size:1.2rem;'>" . $actual_db . "</span><br><br>";

// 2. Force add the missing columns to THIS exact database
$sql = "ALTER TABLE cases 
        ADD COLUMN IF NOT EXISTS time_received TIME AFTER date_received,
        ADD COLUMN IF NOT EXISTS receiving_staff VARCHAR(100) AFTER time_received,
        ADD COLUMN IF NOT EXISTS assigned_to VARCHAR(100) AFTER receiving_staff,
        ADD COLUMN IF NOT EXISTS date_assigned DATE AFTER assigned_to,
        ADD COLUMN IF NOT EXISTS q1_similar_complaint VARCHAR(10) AFTER commission_place,
        ADD COLUMN IF NOT EXISTS q2_counter_charge VARCHAR(10) AFTER q1_similar_complaint,
        ADD COLUMN IF NOT EXISTS q3_related_case VARCHAR(10) AFTER q2_counter_charge";

if ($conn->query($sql) === TRUE) {
    echo "<strong style='color: green;'>SUCCESS: All missing columns (including receiving_staff) have been successfully added to the '" . $actual_db . "' database!</strong><br><br>";
    echo "You can now go back to your case form and submit it.";
} else {
    echo "<strong style='color: red;'>ERROR: </strong>" . $conn->error;
}

$conn->close();
