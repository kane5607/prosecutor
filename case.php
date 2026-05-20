<?php
// 1. Start session and connect to the database
session_start();
require '../connection/db.php';

// 2. AUTO-SETUP DATABASE TABLE FOR EVIDENCE
@$conn->query("CREATE TABLE IF NOT EXISTS case_evidence (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nps_docket_no VARCHAR(100),
    file_name VARCHAR(255),
    original_name VARCHAR(255),
    file_type VARCHAR(50),
    uploaded_by VARCHAR(100),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 3. Set welcome name & Role Check
$welcomeName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Prosecutor');
$role = strtolower($welcomeName);
$isAdmin = ($role === 'admin' || $role === 'prosecutor' || $role === 'desk management');

// 4. Fetch ALL details from the cases table
$sql = "SELECT * FROM cases ORDER BY created_at DESC";
$result = $conn->query($sql);

// 5. Fetch Evidence Data to pass to JS
$evidenceData = [];
$ev_res = $conn->query("SELECT * FROM case_evidence ORDER BY uploaded_at DESC");
if ($ev_res) {
    while ($row = $ev_res->fetch_assoc()) {
        $evidenceData[$row['nps_docket_no']][] = $row;
    }
}
$evidenceJSON = json_encode($evidenceData);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Management - Office of the Prosecutor</title>
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
            <li><a href="case.php" class="active">Case</a></li>
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
                    <span>Office of the Prosecutor Management System</span>
                </div>
            </div>
        </div>

        <div class="search-bar-wrapper">
            <select id="searchFilter" class="search-filter">
                <option value="0">NPS Docket No.</option>
                <option value="1">Complainant/s</option>
                <option value="2">Respondent/s</option>
                <option value="3">Offense/s Committed</option>
            </select>
            <input type="text" id="searchInput" class="search-input" onkeyup="filterTable()" placeholder="Search records...">
        </div>

        <div class="section-card">
            <div class="card-header">
                <strong>👥 Case Records</strong>
            </div>

            <div class="table-container">
                <table class="case-table" id="caseTable">
                    <thead>
                        <tr>
                            <th>NPS Docket No.</th>
                            <th>Complainant/s</th>
                            <th>Respondent/s</th>
                            <th>Offense/s Committed</th>
                            <th>Date & Time of Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {

                                $statusClass = '';
                                if (stripos($row['status'], 'Pending') !== false) {
                                    $statusClass = 'pending';
                                } elseif (stripos($row['status'], 'Ongoing') !== false) {
                                    $statusClass = 'ongoing';
                                } elseif (stripos($row['status'], 'Resolved') !== false) {
                                    $statusClass = 'resolved';
                                }

                                $formattedDate = "N/A";
                                if (!empty($row['commission_dt'])) {
                                    $formattedDate = date("M j, Y | h:i A", strtotime($row['commission_dt']));
                                }

                                $jsDocket = htmlspecialchars($row['nps_docket_no'], ENT_QUOTES);
                                $jsComp = htmlspecialchars($row['complainants'], ENT_QUOTES);
                                $jsResp = htmlspecialchars($row['respondents'], ENT_QUOTES);
                                $jsOffense = htmlspecialchars($row['offense'], ENT_QUOTES);
                                $jsStatus = htmlspecialchars($row['status'], ENT_QUOTES);
                                $jsPlace = htmlspecialchars($row['commission_place'] ?? 'N/A', ENT_QUOTES);

                                $jsQ1 = htmlspecialchars($row['q1'] ?? 'N/A', ENT_QUOTES);
                                $jsQ2 = htmlspecialchars($row['q2'] ?? 'N/A', ENT_QUOTES);
                                $jsQ3 = htmlspecialchars($row['q3'] ?? 'N/A', ENT_QUOTES);

                                echo "<tr onclick='selectCase(this, \"$jsDocket\", \"$jsComp\", \"$jsResp\", \"$jsOffense\", \"$jsStatus\", \"$formattedDate\", \"$jsPlace\", \"$jsQ1\", \"$jsQ2\", \"$jsQ3\")'>";
                                echo "<td><strong>" . $jsDocket . "</strong></td>";
                                echo "<td>" . $jsComp . "</td>";
                                echo "<td>" . $jsResp . "</td>";
                                echo "<td><span class='type-badge'>" . $jsOffense . "</span></td>";
                                echo "<td>" . $formattedDate . "</td>";
                                echo "<td><span class='status-pill " . $statusClass . "'>" . $jsStatus . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; padding: 20px;'>No case records found in the database.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="bottom-actions">
                <button class="btn btn-view" onclick="openSelectedCaseModal()">View Case Details</button>
            </div>
        </div>
    </main>

    <div id="viewCaseModal" class="modal">
        <div class="modal-content wide">
            <span class="close-btn" onclick="document.getElementById('viewCaseModal').style.display='none'">&times;</span>

            <div class="form-header-official" style="text-align: center; margin-bottom: 25px;">
                <p>Republic of the Philippines</p>
                <p>Department of Justice</p>
                <p><strong>NATIONAL PROSECUTION SERVICE</strong></p>
                <br>
                <h3 style="color: #002e5d;">INVESTIGATION DATA RECORD</h3>
            </div>

            <div class="data-form">
                <div class="form-section-title" style="font-weight: bold; color: #002e5d; margin-bottom: 10px; border-bottom: 2px solid #ccc; padding-bottom: 5px;">To be accomplished by the Office:</div>
                <div class="form-grid gray-bg" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f4f4f4; padding: 15px; border-radius: 6px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label>NPS DOCKET NO:</label>
                        <div class="mock-input" id="view-docket" style="background-color: #e2e8f0; font-weight: 800; color: #002e5d; font-size: 1.1rem; text-align: center;"></div>
                    </div>
                    <div class="form-group">
                        <label>CURRENT CASE STATUS:</label>
                        <div class="mock-input" id="view-status" style="font-weight: bold; text-align: center;"></div>
                    </div>
                </div>

                <div class="form-section-title" style="font-weight: bold; color: #002e5d; margin-bottom: 10px; border-bottom: 2px solid #ccc; padding-bottom: 5px;">Party Details:</div>
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label>COMPLAINANT/S: Name, Sex, Age & Address</label>
                        <div class="mock-input" id="view-comp" style="min-height: 80px;"></div>
                    </div>
                    <div class="form-group">
                        <label>RESPONDENT/S: Name, Sex, Age & Address</label>
                        <div class="mock-input" id="view-resp" style="min-height: 80px;"></div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>OFFENSE/S COMMITTED / LAW/s VIOLATED:</label>
                    <div class="mock-input" id="view-offense" style="font-weight: bold; color: #b58105;"></div>
                </div>

                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label>DATE & TIME OF COMMISSION:</label>
                        <div class="mock-input" id="view-date"></div>
                    </div>
                    <div class="form-group">
                        <label>PLACE OF COMMISSION:</label>
                        <div class="mock-input" id="view-place"></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px; border-top: 2px solid #ccc; padding-top: 20px;">

                    <div>
                        <div class="form-section-title" style="font-weight: bold; color: #002e5d; margin-bottom: 10px;"><i class="fa-solid fa-list-check"></i> Case Tracking Info:</div>
                        <div style="background: #f8fafc; padding: 15px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <div style="margin-bottom: 10px;">
                                <span style="font-weight: bold; color: #333; font-size: 0.85rem;">1. Similar complaint filed before?</span><br>
                                <span class="q-badge" id="view-q1"></span>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <span style="font-weight: bold; color: #333; font-size: 0.85rem;">2. Is this a counter-charge?</span><br>
                                <span class="q-badge" id="view-q2"></span>
                            </div>
                            <div>
                                <span style="font-weight: bold; color: #333; font-size: 0.85rem;">3. Related to another case here?</span><br>
                                <span class="q-badge" id="view-q3"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="form-section-title" style="font-weight: bold; color: #002e5d; margin-bottom: 10px;"><i class="fa-solid fa-folder-open"></i> Case Evidence & Files:</div>

                        <div style="background: #f8fafc; padding: 15px; border: 1px solid #cbd5e1; border-radius: 6px; height: 100%;">

                            <div id="evidence-display" class="evidence-list">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-footer" style="display: flex; justify-content: flex-end; border-top:none;">
                    <button class="btn btn-view" onclick="document.getElementById('viewCaseModal').style.display='none'" style="background-color: #64748b; color: white;">Close Record</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../script.js"></script>

    <script>
        // Load Evidence Data from PHP to JS
        const allEvidence = <?php echo $evidenceJSON; ?>;

        function filterTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let filterIndex = document.getElementById("searchFilter").value;
            let table = document.getElementById("caseTable");
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

        let selectedCaseData = null;

        function selectCase(rowElement, docket, comp, resp, offense, status, date, place, q1, q2, q3) {
            let rows = document.querySelectorAll('.case-table tbody tr');
            rows.forEach(r => r.classList.remove('selected-row'));

            rowElement.classList.add('selected-row');
            selectedCaseData = {
                docket,
                comp,
                resp,
                offense,
                status,
                date,
                place,
                q1,
                q2,
                q3
            };
        }

        function getFileIcon(ext) {
            if (['jpg', 'jpeg', 'png'].includes(ext)) return '<i class="fa-solid fa-image ev-icon"></i>';
            if (['mp4', 'mov', 'avi'].includes(ext)) return '<i class="fa-solid fa-video ev-icon"></i>';
            if (ext === 'pdf') return '<i class="fa-solid fa-file-pdf ev-icon"></i>';
            return '<i class="fa-solid fa-file ev-icon"></i>';
        }

        function openSelectedCaseModal() {
            if (!selectedCaseData) {
                alert("Please click on a case row in the table first.");
                return;
            }

            // Map data to the mock form inputs
            document.getElementById('view-docket').innerText = selectedCaseData.docket;
            document.getElementById('view-status').innerText = selectedCaseData.status;
            document.getElementById('view-comp').innerText = selectedCaseData.comp;
            document.getElementById('view-resp').innerText = selectedCaseData.resp;
            document.getElementById('view-offense').innerText = selectedCaseData.offense;
            document.getElementById('view-date').innerText = selectedCaseData.date;
            document.getElementById('view-place').innerText = selectedCaseData.place;

            // Map the Q1, Q2, Q3 data
            document.getElementById('view-q1').innerText = selectedCaseData.q1;
            document.getElementById('view-q2').innerText = selectedCaseData.q2;
            document.getElementById('view-q3').innerText = selectedCaseData.q3;

            // Load Evidence Files into the Right Column
            let evBox = document.getElementById('evidence-display');
            evBox.innerHTML = ''; // clear old files

            let caseFiles = allEvidence[selectedCaseData.docket];

            if (caseFiles && caseFiles.length > 0) {
                caseFiles.forEach(file => {
                    let icon = getFileIcon(file.file_type);
                    // Build the row for the file
                    evBox.innerHTML += `
                        <div class="evidence-item">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65%;">
                                ${icon} ${file.original_name}
                            </span>
                            <a href="../uploads/evidence/${file.file_name}" target="_blank" class="btn-view-file">Open</a>
                        </div>
                    `;
                });
            } else {
                evBox.innerHTML = '<div style="padding: 10px; text-align: center; color: #999; font-style: italic; font-size: 0.85rem;">No files attached to this case.</div>';
            }

            // Apply coloring to the status box
            let statusBox = document.getElementById('view-status');
            if (selectedCaseData.status.toLowerCase().includes("pending")) {
                statusBox.style.color = "#b58105";
            } else if (selectedCaseData.status.toLowerCase().includes("resolved")) {
                statusBox.style.color = "#198754";
            } else {
                statusBox.style.color = "#1976d2";
            }

            document.getElementById('viewCaseModal').style.display = 'block';
        }

        // --- MODAL CLOSING ---
        window.onclick = function(event) {
            var viewModal = document.getElementById('viewCaseModal');
            if (event.target == viewModal) {
                viewModal.style.display = "none";
            }
        }
    </script>
</body>

</html>