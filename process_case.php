<?php
// 1. Start the session and connect to the database
session_start();
require '../connection/db.php'; // Ensure this file is in the same folder, or adjust the path!

// 2. Check if the form was actually submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ---> AUTO-FIX DATABASE COLUMNS <---
    // This ensures the q1, q2, q3 columns exist in your database so you don't get an error!
    @$conn->query("ALTER TABLE cases ADD COLUMN q1 VARCHAR(10) DEFAULT 'N/A'");
    @$conn->query("ALTER TABLE cases ADD COLUMN q2 VARCHAR(10) DEFAULT 'N/A'");
    @$conn->query("ALTER TABLE cases ADD COLUMN q3 VARCHAR(10) DEFAULT 'N/A'");
    // -----------------------------------

    // 3. Grab all the data from the form
    $date_received = $_POST['date_received'] ?? null;
    $time_received = $_POST['time_received'] ?? null;
    $receiving_staff = $_POST['receiving_staff'] ?? null;
    $nps_docket_no = $_POST['nps_docket_no'] ?? null;
    $assigned_to = $_POST['assigned_to'] ?? null;
    $date_assigned = $_POST['date_assigned'] ?? null;
    $complainants = $_POST['complainant_details'] ?? null;
    $respondents = $_POST['respondent_details'] ?? null;
    $offense = $_POST['offense_details'] ?? null;

    // If datetime-local is empty, set it to NULL for the database
    $commission_dt = !empty($_POST['commission_dt']) ? $_POST['commission_dt'] : null;
    $commission_place = $_POST['commission_place'] ?? null;

    // Grab the Yes/No answers
    $q1 = $_POST['q1'] ?? 'N/A';
    $q2 = $_POST['q2'] ?? 'N/A';
    $q3 = $_POST['q3'] ?? 'N/A';

    // Every new case automatically starts as 'Pending Review'
    $status = 'Pending Review';

    // 4. Prepare the SQL Statement (Updated to use q1, q2, q3)
    $stmt = $conn->prepare("INSERT INTO cases (
        nps_docket_no, date_received, time_received, receiving_staff, 
        assigned_to, date_assigned, complainants, respondents, 
        offense, commission_dt, commission_place, 
        q1, q2, q3, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Database Error: " . $conn->error);
    }

    // 5. Bind the actual variables to those ? marks
    $stmt->bind_param(
        "sssssssssssssss",
        $nps_docket_no,
        $date_received,
        $time_received,
        $receiving_staff,
        $assigned_to,
        $date_assigned,
        $complainants,
        $respondents,
        $offense,
        $commission_dt,
        $commission_place,
        $q1,
        $q2,
        $q3,
        $status
    );

    // 6. Execute the query and check if it worked
    if ($stmt->execute()) {
        // Success! Redirect the user back to the Case page
        $_SESSION['success_msg'] = "New case successfully filed!";
        header("Location: case.php");
        exit();
    } else {
        // Uh oh, something broke (like a duplicate Docket Number)
        $_SESSION['error_msg'] = "Error filing case: " . $conn->error;
        header("Location: case.php");
        exit();
    }

    // Clean up connections
    $stmt->close();
    $conn->close();
} else {
    // If someone tries to type "process_case.php" directly into the URL bar, kick them back to the case page.
    header("Location: case.php");
    exit();
}
