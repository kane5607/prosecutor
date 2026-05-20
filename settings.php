<?php
session_start();
require '../connection/db.php';

/* =========================================================
   1. DATABASE REPAIR & DATA HANDLING
   ========================================================= */
@$conn->query("ALTER TABLE users CHANGE user_name username VARCHAR(100)");
$columns = [
    "profile_pic VARCHAR(255) DEFAULT 'default-avatar.png'",
    "first_name VARCHAR(100)",
    "middle_name VARCHAR(100)",
    "last_name VARCHAR(100)",
    "address TEXT",
    "age INT",
    "dob DATE",
    "gender VARCHAR(20)",
    "contact_no VARCHAR(50)",
    "email VARCHAR(100)",
    "last_activity DATETIME NULL",
    "account_status VARCHAR(20) DEFAULT 'Active'"
];
foreach ($columns as $col) {
    @$conn->query("ALTER TABLE users ADD COLUMN $col");
}

// Setup System Settings Table & New Clearance Columns
@$conn->query("CREATE TABLE IF NOT EXISTS system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    system_name VARCHAR(255) DEFAULT 'Office of the Prosecutor Management System',
    maintenance_mode TINYINT(1) DEFAULT 0,
    timezone VARCHAR(100) DEFAULT 'Asia/Manila'
)");
@$conn->query("ALTER TABLE system_settings ADD COLUMN clearance_export_format VARCHAR(50) DEFAULT 'PDF'");
@$conn->query("ALTER TABLE system_settings ADD COLUMN clearance_signatory VARCHAR(100) DEFAULT 'Head Prosecutor'");
@$conn->query("ALTER TABLE system_settings ADD COLUMN clearance_qr_verification TINYINT(1) DEFAULT 1");

$checkSys = $conn->query("SELECT * FROM system_settings WHERE id=1");
if ($checkSys && $checkSys->num_rows == 0) {
    $conn->query("INSERT INTO system_settings (id) VALUES (1)");
}

$welcomeName = 'Admin';
if (!empty($_SESSION['username'])) {
    $welcomeName = $_SESSION['username'];
} elseif (!empty($_SESSION['user_name'])) {
    $welcomeName = $_SESSION['user_name'];
}

// =========================================================
// 2. PROCESS FORM SUBMISSIONS
// =========================================================

// Create account logic
if (isset($_POST['create_account'])) {
    $new_u = trim($_POST['new_username']);
    $new_p = $_POST['new_password'];
    $new_r = $_POST['new_role'] ?? 'Staff';
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $addr  = $_POST['address'];
    $age   = !empty($_POST['age']) ? $_POST['age'] : 0;
    $dob   = $_POST['dob'];
    $gen   = $_POST['gender'];
    $cont  = $_POST['contact_no'];
    $email = $_POST['email'];

    $ins = $conn->prepare("INSERT INTO users (username, password, role, first_name, middle_name, last_name, address, age, dob, gender, contact_no, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins->bind_param("sssssssissss", $new_u, $new_p, $new_r, $fname, $mname, $lname, $addr, $age, $dob, $gen, $cont, $email);
    $ins->execute();
    $ins->close();
    header("Location: settings.php");
    exit();
}

// Toggle User Status (Deactivate/Activate)
if (isset($_POST['toggle_status'])) {
    $target_user = $_POST['target_user'];
    $new_status = $_POST['new_status'];
    $upd = $conn->prepare("UPDATE users SET account_status=? WHERE username=?");
    $upd->bind_param("ss", $new_status, $target_user);
    $upd->execute();
    $upd->close();
    header("Location: settings.php");
    exit();
}

// Update System Settings
if (isset($_POST['update_system'])) {
    $sys_name = htmlspecialchars($_POST['system_name']);
    $timezone = htmlspecialchars($_POST['timezone']);
    $maint = isset($_POST['maintenance_mode']) ? 1 : 0;

    // New Clearance Settings
    $format = htmlspecialchars($_POST['clearance_export_format']);
    $sig = htmlspecialchars($_POST['clearance_signatory']);
    $qr = isset($_POST['clearance_qr_verification']) ? 1 : 0;

    $updSys = $conn->prepare("UPDATE system_settings SET system_name=?, timezone=?, maintenance_mode=?, clearance_export_format=?, clearance_signatory=?, clearance_qr_verification=? WHERE id=1");
    $updSys->bind_param("ssissi", $sys_name, $timezone, $maint, $format, $sig, $qr);
    $updSys->execute();
    $updSys->close();
    header("Location: settings.php");
    exit();
}

// =========================================================
// 3. FETCH DATA FOR UI
// =========================================================
$all_users = [];
$res = $conn->query("SELECT * FROM users ORDER BY role ASC, username ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $all_users[] = $row;
    }
}

// Default Fallback
$sysConfig = [
    'system_name' => 'Office of the Prosecutor',
    'maintenance_mode' => 0,
    'timezone' => 'Asia/Manila',
    'clearance_export_format' => 'PDF',
    'clearance_signatory' => 'Head Prosecutor',
    'clearance_qr_verification' => 1
];
$sysRes = $conn->query("SELECT * FROM system_settings WHERE id=1");
if ($sysRes && $sysRes->num_rows > 0) {
    $sysConfig = $sysRes->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Settings - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .settings-layout {
            display: block;
            /* Takes full width now */
            width: 100%;
        }

        /* Tabs Styling */
        .settings-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            color: #64748b;
            cursor: pointer;
            border-radius: 4px;
            transition: 0.2s;
        }

        .tab-btn:hover {
            background: #f1f5f9;
            color: #002e5d;
        }

        .tab-btn.active {
            background: #002e5d;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* User Table Styles */
        .user-row {
            cursor: pointer;
            transition: 0.2s;
        }

        .user-row.selected td {
            background-color: #fdfbf7 !important;
            border-left: 4px solid #c5a059 !important;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .modal-content.wide {
            max-width: 800px;
        }

        /* System Settings Grid */
        .sys-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8fafc;
        }

        .sys-card h4 {
            color: #002e5d;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #16a34a;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
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
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contactus.php">Contact Us</a></li>
            <li><a href="settings.php" class="active">Settings</a></li>
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

        <div class="settings-layout">

            <div class="main-table-column">

                <div class="settings-tabs">
                    <button class="tab-btn active" onclick="switchTab('userTab', this)"><i class="fa-solid fa-users-cog"></i> User Settings</button>
                    <button class="tab-btn" onclick="switchTab('systemTab', this)"><i class="fa-solid fa-server"></i> System Settings</button>
                </div>

                <div id="userTab" class="tab-content active">
                    <div class="search-bar-wrapper">
                        <select class="search-filter">
                            <option>Username</option>
                        </select>
                        <input type="text" class="search-input" placeholder="Search user records...">
                    </div>

                    <div class="section-card">
                        <div class="card-header">System User Records</div>

                        <div class="table-container">
                            <table class="case-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Account Role</th>
                                        <th style="text-align:center;">Account Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_users as $u):
                                        $uData = htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8');
                                        $isInactive = ($u['account_status'] === 'Deactivated');
                                    ?>
                                        <tr class="user-row" onclick="selectRow(this, <?php echo $uData; ?>)">
                                            <td style="font-weight: bold; color: #002e5d;">@<?php echo htmlspecialchars($u['username']); ?></td>
                                            <td style="<?php echo $isInactive ? 'text-decoration: line-through; color: #999;' : ''; ?>">
                                                <?php echo trim($u['first_name'] . ' ' . $u['last_name']) ?: 'Incomplete'; ?>
                                            </td>
                                            <td><span class="type-badge"><?php echo htmlspecialchars($u['role']); ?></span></td>
                                            <td style="text-align:center;">
                                                <?php if ($isInactive): ?>
                                                    <span class="status-pill status-inactive">Deactivated</span>
                                                <?php else: ?>
                                                    <span class="status-pill resolved">Active</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="bottom-actions" style="margin-top: 25px;">
                            <button class="btn-action" onclick="openModal('regModal')">Create New User</button>
                            <button class="btn-action btn-view" onclick="viewDetails()">Manage Selected User</button>
                        </div>
                    </div>
                </div>

                <div id="systemTab" class="tab-content">
                    <div class="section-card">
                        <div class="card-header">Global System Configuration</div>

                        <form method="POST" action="">
                            <div class="sys-card">
                                <h4><i class="fa-solid fa-sliders"></i> General Preferences</h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Official System Name</label>
                                        <input type="text" name="system_name" value="<?php echo htmlspecialchars($sysConfig['system_name']); ?>" class="form-input-styled" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Default Timezone</label>
                                        <select name="timezone" class="form-input-styled">
                                            <option value="Asia/Manila" <?php echo ($sysConfig['timezone'] == 'Asia/Manila') ? 'selected' : ''; ?>>Asia/Manila (PHT)</option>
                                            <option value="UTC" <?php echo ($sysConfig['timezone'] == 'UTC') ? 'selected' : ''; ?>>UTC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="sys-card">
                                <h4><i class="fa-solid fa-file-signature"></i> Clearance Release Preferences</h4>
                                <div class="form-grid" style="margin-bottom: 15px;">
                                    <div class="form-group">
                                        <label>Default Export Format</label>
                                        <select name="clearance_export_format" class="form-input-styled">
                                            <option value="PDF" <?php echo ($sysConfig['clearance_export_format'] == 'PDF') ? 'selected' : ''; ?>>Secure PDF (Recommended)</option>
                                            <option value="Print" <?php echo ($sysConfig['clearance_export_format'] == 'Print') ? 'selected' : ''; ?>>Direct to Printer</option>
                                            <option value="DOCX" <?php echo ($sysConfig['clearance_export_format'] == 'DOCX') ? 'selected' : ''; ?>>Word Document (Editable)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Authorized Signatory</label>
                                        <input type="text" name="clearance_signatory" value="<?php echo htmlspecialchars($sysConfig['clearance_signatory']); ?>" class="form-input-styled" placeholder="e.g. Atty. Juan Dela Cruz">
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px dashed #ccc;">
                                    <div>
                                        <strong style="color: #333; display: block; margin-bottom: 3px;">Enable QR Code Verification</strong>
                                        <span style="font-size: 0.85rem; color: #666;">Automatically attach a scannable QR code to released clearances to prevent forgery.</span>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="clearance_qr_verification" <?php echo ($sysConfig['clearance_qr_verification'] == 1) ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="sys-card">
                                <h4><i class="fa-solid fa-shield-halved"></i> Security & Access</h4>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong style="color: #333; display: block; margin-bottom: 5px;">Maintenance Mode</strong>
                                        <span style="font-size: 0.85rem; color: #666;">Locks out all non-admin users. Use this when updating the system.</span>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="maintenance_mode" <?php echo ($sysConfig['maintenance_mode'] == 1) ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" name="update_system" class="btn-action">Save System Configuration</button>
                            </div>
                        </form>

                        <div class="sys-card" style="margin-top: 20px; border-left: 4px solid #002e5d;">
                            <h4><i class="fa-solid fa-database"></i> Database Management</h4>
                            <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Generate a secure SQL dump of all system data, cases, and user records.</p>
                            <button onclick="alert('Database backup initiated. Downloading SQL file...');" class="btn-action" style="background: #002e5d;"><i class="fa-solid fa-download"></i> Backup Database</button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <div id="regModal" class="modal">
        <div class="modal-content wide">
            <span class="close-btn" onclick="closeModal('regModal')">&times;</span>
            <div class="form-header-official">
                <h3>Register New User</h3>
            </div>
            <form method="POST">
                <div class="form-section-title">Credentials</div>
                <div class="form-grid-3">
                    <div class="form-group"><input type="text" name="new_username" required placeholder="Username" class="form-input-styled"></div>
                    <div class="form-group"><input type="password" name="new_password" required placeholder="Password" class="form-input-styled"></div>
                    <div class="form-group">
                        <select name="new_role" class="form-input-styled">
                            <option value="Aide">Aide</option>
                            <option value="Secretary">Secretary</option>
                            <option value="Prosecutor">Prosecutor</option>
                            <option value="Desk Management">Desk Management</option>
                        </select>
                    </div>
                </div>

                <div class="form-section-title">Personal Info</div>
                <div class="form-grid-3">
                    <div class="form-group"><input type="text" name="first_name" placeholder="First Name" class="form-input-styled"></div>
                    <div class="form-group"><input type="text" name="middle_name" placeholder="Middle Name" class="form-input-styled"></div>
                    <div class="form-group"><input type="text" name="last_name" placeholder="Last Name" class="form-input-styled"></div>
                </div>
                <div class="form-grid-3">
                    <div class="form-group"><input type="number" name="age" placeholder="Age" class="form-input-styled"></div>
                    <div class="form-group"><input type="date" name="dob" class="form-input-styled"></div>
                    <div class="form-group">
                        <select name="gender" class="form-input-styled">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group"><input type="text" name="address" placeholder="Address" class="form-input-styled"></div>

                <div class="form-grid">
                    <div class="form-group"><input type="text" name="contact_no" placeholder="Contact Number" class="form-input-styled"></div>
                    <div class="form-group"><input type="email" name="email" placeholder="Email Address" class="form-input-styled"></div>
                </div>

                <div class="form-footer">
                    <button type="submit" name="create_account" class="btn-action" style="width:100%;">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal">
        <div class="modal-content wide">
            <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            <div id="profileContent"></div>
        </div>
    </div>

    <script>
        // Tab Switching Logic
        function switchTab(tabId, btnElement) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            btnElement.classList.add('active');
        }

        let selectedUserData = null;

        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        window.onclick = function(event) {
            let modals = document.querySelectorAll('.modal');
            modals.forEach(m => {
                if (event.target == m) {
                    m.style.display = "none";
                }
            });
        }

        function selectRow(row, data) {
            document.querySelectorAll('.user-row').forEach(r => {
                r.style.backgroundColor = '';
                r.style.borderLeft = '';
            });
            row.style.backgroundColor = '#fdfbf7';
            row.style.borderLeft = '4px solid #c5a059';
            selectedUserData = data;
        }

        function viewDetails() {
            if (!selectedUserData) return alert("Select a user from the table first!");
            const u = selectedUserData;
            const pic = u.profile_pic ? u.profile_pic : 'default-avatar.png';

            const isInactive = (u.account_status === 'Deactivated');
            const statusColor = isInactive ? '#991b1b' : '#16a34a';
            const statusText = isInactive ? 'Deactivated' : 'Active';

            const actionBtnText = isInactive ? 'Re-Activate Account' : 'Deactivate Account';
            const actionBtnColor = isInactive ? '#16a34a' : '#d9534f';
            const nextStatus = isInactive ? 'Active' : 'Deactivated';

            document.getElementById('profileContent').innerHTML = `
                <div class="form-header-official"><h3>User Management</h3></div>
                
                <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 20px;">
                    <span style="font-weight:bold; font-size:1.1rem;">Current Status: <span style="color: ${statusColor};">${statusText}</span></span>
                    
                    <form method="POST" action="" style="margin:0;">
                        <input type="hidden" name="target_user" value="${u.username}">
                        <input type="hidden" name="new_status" value="${nextStatus}">
                        <button type="submit" name="toggle_status" class="btn-action" style="background-color: ${actionBtnColor}; padding: 8px 15px; font-size: 0.85rem;">
                            <i class="fa-solid fa-power-off"></i> ${actionBtnText}
                        </button>
                    </form>
                </div>

                <div style="display:grid; grid-template-columns: 150px 1fr; gap: 30px;">
                    <img src="../images/${pic}" style="width: 150px; height: 150px; border-radius: 8px; border: 3px solid #002e5d; object-fit: cover; opacity: ${isInactive ? '0.5' : '1'};">
                    <div class="gray-bg" style="${isInactive ? 'opacity: 0.7;' : ''}">
                        <div class="form-grid">
                            <div><strong>Full Name:</strong><br>${u.first_name || ''} ${u.middle_name || ''} ${u.last_name || ''}</div>
                            <div><strong>Role:</strong><br>${u.role}</div>
                            <div><strong>Username:</strong><br>@${u.username}</div>
                            <div><strong>Gender:</strong><br>${u.gender || 'N/A'}</div>
                            <div><strong>Age:</strong><br>${u.age || 'N/A'} yrs</div>
                            <div><strong>Contact:</strong><br>${u.contact_no || 'N/A'}</div>
                            <div><strong>Email:</strong><br>${u.email || 'N/A'}</div>
                        </div>
                        <div style="margin-top:15px; border-top:1px solid #ccc; padding-top:10px;">
                            <strong>Address:</strong><br>${u.address || 'N/A'}
                        </div>
                    </div>
                </div>
            `;
            openModal('viewModal');
        }
    </script>
</body>

</html>