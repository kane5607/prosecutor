<?php
session_start();
require '../connection/db.php';

// ==========================================
// 1. SETUP & AUTHENTICATION
// ==========================================
$welcomeName = 'Admin';
if (!empty($_SESSION['username'])) {
    $welcomeName = $_SESSION['username'];
} elseif (!empty($_SESSION['user_name'])) {
    $welcomeName = $_SESSION['user_name'];
}
$isAdmin = (strtolower($welcomeName) === 'admin' || strtolower($welcomeName) === 'prosecutor' || strtolower($welcomeName) === 'desk management');

// ==========================================
// 2. FETCH FEEDBACK DATA
// ==========================================
$all_feedback = [];
// Assuming your table is named 'feedback' based on your previous insert query
$res = $conn->query("SELECT * FROM feedback ORDER BY feedback_date DESC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $all_feedback[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Feedback Management - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific layout tweaks for the Feedback Table */
        .feedback-layout {
            width: 100%;
        }

        .user-row {
            cursor: pointer;
            transition: 0.2s;
        }

        .user-row.selected td {
            background-color: #fdfbf7 !important;
            border-left: 4px solid #c5a059 !important;
        }

        /* Modal Content Widening for Survey Answers */
        .modal-content.wide {
            max-width: 850px;
        }

        /* Survey Result Styling */
        .survey-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-top: 15px;
        }

        .survey-item {
            background: #f4f4f4;
            padding: 12px 15px;
            border-radius: 4px;
            border-left: 3px solid #002e5d;
        }

        .survey-question {
            font-weight: bold;
            color: #333;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .survey-answer {
            color: #002e5d;
            font-weight: 800;
            font-size: 0.95rem;
        }

        .suggestions-box {
            background: #fff8e1;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #c5a059;
            margin-top: 15px;
            font-style: italic;
        }
    </style>
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
            <li><a href="feedback.php" class="active">Feedback</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contactus.php">Contact Us</a></li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </nav>

    <main class="content-area">

        <div class="floating-header">
            <div class="logo-section">
                <div class="title-group">
                    <span>Office of the Prosecutor Management System</span>
                </div>
            </div>
        </div>

        <div class="feedback-layout">

            <div class="main-table-column">
                <div class="search-bar-wrapper">
                    <select class="search-filter">
                        <option>Date</option>
                        <option>Client Type</option>
                    </select>
                    <input type="text" class="search-input" placeholder="Search feedback records...">
                </div>

                <div class="section-card">
                    <div class="card-header">Client Satisfaction Feedback Records</div>

                    <div class="table-container">
                        <table class="case-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client Type</th>
                                    <th>Demographics (Sex/Age)</th>
                                    <th>Services Availed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($all_feedback)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center; padding: 20px; color:#666;">No feedback records found.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($all_feedback as $f):
                                    $fData = htmlspecialchars(json_encode($f), ENT_QUOTES, 'UTF-8');
                                ?>
                                    <tr class="user-row" onclick="selectRow(this, <?php echo $fData; ?>)">
                                        <td style="font-weight: bold; color: #002e5d;"><?php echo date('M d, Y', strtotime($f['feedback_date'])); ?></td>
                                        <td><span class="type-badge"><?php echo htmlspecialchars($f['client_type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($f['sex']); ?> / <?php echo htmlspecialchars($f['age']); ?> yrs</td>
                                        <td style="font-size: 0.8rem; color:#555;"><?php echo htmlspecialchars($f['services']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bottom-actions">
                        <button class="btn-action btn-view" onclick="viewDetails()">View Feedback Details</button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div id="viewModal" class="modal">
        <div class="modal-content wide">
            <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            <div id="feedbackContent"></div>
        </div>
    </div>

    <script src="../script.js"></script>
    <script>
        let selectedFeedbackData = null;

        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            let viewModal = document.getElementById('viewModal');
            if (event.target == viewModal) {
                viewModal.style.display = "none";
            }
        }

        function selectRow(row, data) {
            document.querySelectorAll('.user-row').forEach(r => {
                r.style.backgroundColor = '';
                r.style.borderLeft = '';
            });
            row.style.backgroundColor = '#fdfbf7';
            row.style.borderLeft = '4px solid #c5a059';
            selectedFeedbackData = data;
        }

        // Helper to translate SQD 1-5 ratings into readable text
        function getRatingText(val) {
            switch (String(val)) {
                case '1':
                    return 'Strongly Disagree 😡';
                case '2':
                    return 'Disagree 🙁';
                case '3':
                    return 'Neutral 😐';
                case '4':
                    return 'Agree 🙂';
                case '5':
                    return 'Strongly Agree 😄';
                default:
                    return 'No Answer';
            }
        }

        // Helper to translate CC answers
        function getCC1Text(val) {
            switch (String(val)) {
                case '1':
                    return "I know what it is and saw the DOJ's CC.";
                case '2':
                    return "I know what it is but did not see the CC.";
                case '3':
                    return "Learned about it only during this transaction.";
                case '4':
                    return "I do not know what it is.";
                default:
                    return 'No Answer';
            }
        }

        function getCC2Text(val) {
            switch (String(val)) {
                case '1':
                    return "Easy to see";
                case '2':
                    return "Somewhat easy";
                case '3':
                    return "Difficult to see";
                case '4':
                    return "Not visible / N/A";
                default:
                    return 'No Answer';
            }
        }

        function viewDetails() {
            if (!selectedFeedbackData) return alert("Select a feedback row from the table first!");
            const f = selectedFeedbackData;

            document.getElementById('feedbackContent').innerHTML = `
                <div class="form-header-official"><h3>Feedback Details</h3></div>
                
                <div class="gray-bg" style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div><strong>Date:</strong><br>${f.feedback_date}</div>
                    <div><strong>Client Type:</strong><br>${f.client_type}</div>
                    <div><strong>Demographics:</strong><br>${f.sex}, ${f.age} years old</div>
                </div>

                <div style="margin-bottom: 20px;">
                    <strong style="color:#002e5d;">Services Availed:</strong><br>
                    <span style="background: #e3f2fd; padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; display: inline-block; margin-top: 5px;">
                        ${f.services || 'None indicated'}
                    </span>
                </div>

                <h4 style="color: #002e5d; border-bottom: 2px solid #ccc; padding-bottom: 5px;">Citizen's Charter (CC)</h4>
                <div class="survey-grid">
                    <div class="survey-item">
                        <div class="survey-question">CC1: Awareness of Citizen's Charter?</div>
                        <div class="survey-answer">${getCC1Text(f.cc1)}</div>
                    </div>
                    <div class="survey-item">
                        <div class="survey-question">CC2: Visibility of the Charter?</div>
                        <div class="survey-answer">${getCC2Text(f.cc2)}</div>
                    </div>
                </div>

                <h4 style="color: #002e5d; border-bottom: 2px solid #ccc; padding-bottom: 5px; margin-top: 25px;">Service Quality Dimensions (SQD)</h4>
                <div class="survey-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="survey-item"><div class="survey-question">SQD0: Satisfied with service</div><div class="survey-answer">${getRatingText(f.sqd0)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD1: Reasonable amount of time</div><div class="survey-answer">${getRatingText(f.sqd1)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD2: Office followed requirements</div><div class="survey-answer">${getRatingText(f.sqd2)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD3: Steps were easy and simple</div><div class="survey-answer">${getRatingText(f.sqd3)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD4: Easily found info on website</div><div class="survey-answer">${getRatingText(f.sqd4)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD5: Paid reasonable fees</div><div class="survey-answer">${getRatingText(f.sqd5)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD6: Fair to everyone</div><div class="survey-answer">${getRatingText(f.sqd6)}</div></div>
                    <div class="survey-item"><div class="survey-question">SQD7: Treated courteously</div><div class="survey-answer">${getRatingText(f.sqd7)}</div></div>
                    <div class="survey-item" style="grid-column: span 2;"><div class="survey-question">SQD8: Got what I needed / Denial explained</div><div class="survey-answer">${getRatingText(f.sqd8)}</div></div>
                </div>

                <h4 style="color: #002e5d; border-bottom: 2px solid #ccc; padding-bottom: 5px; margin-top: 25px;">Suggestions / Comments</h4>
                <div class="suggestions-box">
                    "${f.suggestions ? f.suggestions : 'No suggestions provided by the client.'}"
                </div>
            `;
            openModal('viewModal');
        }
    </script>
</body>

</html>`