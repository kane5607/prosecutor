<?php
// 1. Start session and connect to the database
session_start();
require '../connection/db.php';

// HELPER FUNCTION: To handle multiple file uploads cleanly
function uploadSpecificFile(string $inputName)
{
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES[$inputName]['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $target_file)) {
            return $file_name;
        }
    }
    return NULL;
}

// 2. PROCESS NEW APPLICATION & FILE UPLOADS
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_clearance'])) {
    $name = $_POST['applicant_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $contact = $_POST['contact_no'] ?? '';
    $type = $_POST['clearance_type'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $status = 'Not Complete';

    // Upload each file specifically
    $file_valid = uploadSpecificFile('file_valid');
    $file_brgy = uploadSpecificFile('file_brgy');
    $file_police = uploadSpecificFile('file_police');
    $file_photo = uploadSpecificFile('file_photo');

    $stmt = $conn->prepare("INSERT INTO clearance_applications (applicant_name, address, dob, contact_no, clearance_type, purpose, status, file_valid_id, file_brgy, file_police, file_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssssssss", $name, $address, $dob, $contact, $type, $purpose, $status, $file_valid, $file_brgy, $file_police, $file_photo);
        $stmt->execute();
        $stmt->close();
        header("Location: clearance.php");
        exit();
    } else {
        die("Database error: " . $conn->error);
    }
}

// 4. FETCH ALL CLEARANCES
$sql = "SELECT * FROM clearance_applications ORDER BY created_at DESC";
$result = $conn->query($sql);

$welcomeName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Management - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Clearance-Specific Styles */
        .req-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 8px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        .req-row span.req-name,
        .req-row label {
            font-weight: 500;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Make disabled checkboxes look standard but unclickable */
        .req-row input[type="checkbox"]:disabled {
            cursor: not-allowed;
            opacity: 0.8;
        }

        .req-row input[type="file"] {
            max-width: 250px;
            font-size: 0.8rem;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #viewModal,
            #viewModal * {
                visibility: visible;
            }

            #viewModal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
            }

            .modal-content {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <nav class="doj-nav no-print">

        <div class="sidebar-profile">
            <img src="../images/image.png" alt="Prosecutor Logo">
            <div class="sidebar-welcome-name">Welcome, <?php echo htmlspecialchars($welcomeName); ?>!</div>
            <div class="sidebar-system-title">Office of the Prosecutor<br>Management System</div>
            <a href="../logout.php" class="btn-sidebar-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a>
        </div>
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="nav-links">
            <li><a href="case.php" class="<?php echo ($current_page == 'case.php') ? 'active' : ''; ?>">Case</a></li>
            <li><a href="clearance.php" class="<?php echo ($current_page == 'clearance.php') ? 'active' : ''; ?>">Clearance</a></li>
            <li><a href="announcement.php" class="<?php echo ($current_page == 'announcement.php') ? 'active' : ''; ?>">Announcements</a></li>
            <li><a href="feedback.php" class="<?php echo ($current_page == 'feedback.php') ? 'active' : ''; ?>">Feedback</a></li>
            <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a></li>
            <li><a href="contactus.php" class="<?php echo ($current_page == 'contactus.php') ? 'active' : ''; ?>">Contact Us</a></li>
            <li><a href="settings.php" class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">Settings</a></li>
        </ul>
    </nav>

    <main class="content-area">

        <div class="floating-header no-print">
            <div class="logo-section">
                <div class="title-group">
                    <span>Office of the Prosecutor Management System</span>
                </div>
            </div>
        </div>

        <div class="search-bar-wrapper no-print">
            <select id="clearanceFilter" class="search-filter">
                <option value="0">Applicant Name</option>
                <option value="1">Address</option>
                <option value="2">Date of Birth</option>
                <option value="3">Contact Number</option>
                <option value="4">Type of Clearance</option>
            </select>
            <input type="text" id="clearanceSearch" class="search-input" onkeyup="filterClearance()" placeholder="Search applicants...">
        </div>

        <div class="section-card no-print">
            <div class="card-header">
                <strong>📝 Clearance Application Tracking</strong>
            </div>

            <div class="table-container">
                <table class="case-table" id="clearanceTable">
                    <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>Address</th>
                            <th>Date of Birth</th>
                            <th>Contact Number</th>
                            <th>Type of Clearance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {

                                $statusClass = 'pending';
                                if ($row['status'] == 'Pending') $statusClass = 'ongoing';
                                if ($row['status'] == 'Released') $statusClass = 'resolved';

                                $id = $row['id'];
                                $jsName = htmlspecialchars($row['applicant_name'], ENT_QUOTES);
                                $jsAddress = htmlspecialchars($row['address'], ENT_QUOTES);
                                $jsDob = htmlspecialchars($row['dob'], ENT_QUOTES);
                                $jsType = htmlspecialchars($row['clearance_type'], ENT_QUOTES);
                                $jsReqs = htmlspecialchars($row['requirements_checked'] ?? '', ENT_QUOTES);

                                $jsFileValid = htmlspecialchars($row['file_valid_id'] ?? '', ENT_QUOTES);
                                $jsFileBrgy = htmlspecialchars($row['file_brgy'] ?? '', ENT_QUOTES);
                                $jsFilePolice = htmlspecialchars($row['file_police'] ?? '', ENT_QUOTES);
                                $jsFilePhoto = htmlspecialchars($row['file_photo'] ?? '', ENT_QUOTES);

                                $age = 'N/A';
                                if (!empty($row['dob'])) {
                                    $birthDate = new DateTime($row['dob']);
                                    $today = new DateTime('today');
                                    $age = $birthDate->diff($today)->y;
                                }

                                echo "<tr onclick='selectClearance(this, $id, \"$jsName\", \"$age\", \"$jsAddress\", \"$jsType\", \"$jsReqs\", \"$jsFileValid\", \"$jsFileBrgy\", \"$jsFilePolice\", \"$jsFilePhoto\")'>";
                                echo "<td><strong>" . $jsName . "</strong></td>";
                                echo "<td>" . $jsAddress . "</td>";
                                echo "<td>" . $jsDob . "</td>";
                                echo "<td>" . htmlspecialchars($row['contact_no']) . "</td>";
                                echo "<td><span class='type-badge'>" . $jsType . "</span></td>";
                                echo "<td><span class='status-pill " . $statusClass . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; padding: 20px;'>No clearance applications found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="bottom-actions">
                <button class="btn btn-view" onclick="openSelectedClearanceModal()">View Clearance Tracker</button>
            </div>
        </div>
    </main>

    <div id="viewModal" class="modal no-print">
        <div class="modal-content detail-card">
            <span class="close-btn" onclick="closeViewModal()">&times;</span>
            <div class="form-header-official">
                <h3>Applicant Profile Tracker</h3>
            </div>

            <div class="form-grid gray-bg">
                <p><strong>Name:</strong> <span id="view-name"></span></p>
                <p><strong>Age:</strong> <span id="view-age"></span></p>
                <p><strong>Address:</strong> <span id="view-address"></span></p>
                <p><strong>Type:</strong> <span id="view-type"></span></p>
            </div>

            <div class="requirements-section" style="margin-top: 20px;">
                <h4 style="color:#002e5d; margin-bottom: 10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px;">Documentary Requirements Log</h4>

                <div class="question-list">
                    <div class="req-row">
                        <label><input type="checkbox" value="Valid ID" class="req-check" disabled> Valid ID (Government Issued)</label>
                        <span id="link-valid"></span>
                    </div>
                    <div class="req-row">
                        <label><input type="checkbox" value="Barangay Clearance" class="req-check" disabled> Barangay Clearance</label>
                        <span id="link-brgy"></span>
                    </div>
                    <div class="req-row">
                        <label><input type="checkbox" value="Police Clearance" class="req-check" disabled> Police Clearance</label>
                        <span id="link-police"></span>
                    </div>
                    <div class="req-row">
                        <label><input type="checkbox" value="2x2 Photo" class="req-check" disabled> Recent 2x2 Photo</label>
                        <span id="link-photo"></span>
                    </div>
                </div>
            </div>

            <div class="form-footer" style="display: flex; justify-content: flex-end; align-items: center; margin-top: 20px;">
                <p style="margin-bottom: 0;">Status: <strong id="modal-status" style="color: #002e5d;">Pending Review</strong></p>
            </div>
        </div>
    </div>

    <script src="../script.js"></script>
    <script>
        function filterClearance() {
            let input = document.getElementById("clearanceSearch").value.toLowerCase();
            let filterIndex = document.getElementById("clearanceFilter").value;
            let table = document.getElementById("clearanceTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName("td")[filterIndex];
                if (td) {
                    let textValue = td.textContent || td.innerText;
                    if (textValue.toLowerCase().indexOf(input) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        let selectedClearanceData = null;

        function selectClearance(rowElement, id, name, age, address, type, reqs, fValid, fBrgy, fPolice, fPhoto) {
            let rows = document.querySelectorAll('.case-table tbody tr');
            rows.forEach(r => r.classList.remove('selected-row'));
            rowElement.classList.add('selected-row');

            selectedClearanceData = {
                id,
                name,
                age,
                address,
                type,
                reqs,
                fValid,
                fBrgy,
                fPolice,
                fPhoto
            };
        }

        function openSelectedClearanceModal() {
            if (!selectedClearanceData) {
                alert("Please click on an applicant row in the table first.");
                return;
            }
            viewApplicant(
                selectedClearanceData.id,
                selectedClearanceData.name,
                selectedClearanceData.age,
                selectedClearanceData.address,
                selectedClearanceData.type,
                selectedClearanceData.reqs,
                selectedClearanceData.fValid,
                selectedClearanceData.fBrgy,
                selectedClearanceData.fPolice,
                selectedClearanceData.fPhoto
            );
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function generateFileLink(filename) {
            if (filename && filename.trim() !== '') {
                return `<a href="../uploads/${filename}" target="_blank" style="background-color: #f1f5f9; padding: 4px 12px; border-radius: 4px; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-block;">📄 View File</a>`;
            }
            return "<span style='color: #94a3b8; font-style: italic; font-size: 0.85rem;'>No File Attached</span>";
        }

        function viewApplicant(id, name, age, address, type, savedReqs, fValid, fBrgy, fPolice, fPhoto) {
            document.getElementById('view-name').innerText = name;
            document.getElementById('view-age').innerText = age;
            document.getElementById('view-address').innerText = address;
            document.getElementById('view-type').innerText = type;

            document.getElementById('link-valid').innerHTML = generateFileLink(fValid);
            document.getElementById('link-brgy').innerHTML = generateFileLink(fBrgy);
            document.getElementById('link-police').innerHTML = generateFileLink(fPolice);
            document.getElementById('link-photo').innerHTML = generateFileLink(fPhoto);

            let checkboxes = document.querySelectorAll('.req-check');
            let checkedCount = 0;

            checkboxes.forEach(cb => {
                if (savedReqs && savedReqs.includes(cb.value)) {
                    cb.checked = true;
                    checkedCount++;
                } else {
                    cb.checked = false;
                }
            });

            // Update status text based on how many are checked
            let statusText = document.getElementById('modal-status');
            if (checkedCount === 4) {
                statusText.innerText = "Requirements Complete";
                statusText.style.color = "#16a34a";
            } else if (checkedCount > 0) {
                statusText.innerText = "Partially Complete";
                statusText.style.color = "#c5a059";
            } else {
                statusText.innerText = "Not Complete";
                statusText.style.color = "#d9534f";
            }

            document.getElementById('viewModal').style.display = 'block';
        }

        window.onclick = function(event) {
            let viewModal = document.getElementById('viewModal');
            if (event.target == viewModal) {
                viewModal.style.display = "none";
            }
        }
    </script>
</body>

</html>