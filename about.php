<?php
session_start();
require '../connection/db.php';

// ==========================================
// 1. AUTO-SETUP DATABASE TABLE
// ==========================================
@$conn->query("CREATE TABLE IF NOT EXISTS about_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    head_name TEXT,
    mission_text TEXT,
    ops_title TEXT,
    ops_text TEXT,
    card_title TEXT,
    card_text TEXT
)");

// Insert default values if the table is empty
$check = $conn->query("SELECT * FROM about_info WHERE id=1");
if ($check && $check->num_rows == 0) {
    $default_mission = "Our mission is to uphold the rule of law through the fair and efficient administration of justice, ensuring that every citizen of Pagadian City is served with integrity and transparency.";
    $default_ops = "The Administrative Department serves as the backbone of our office, managing case records, clearance processing, and personnel workflows to ensure seamless legal operations.";
    $default_card = "Responsible for the coordination of prosecutors, aides, and secretarial staff within the system.";

    $conn->query("INSERT INTO about_info (id, head_name, mission_text, ops_title, ops_text, card_title, card_text) 
                  VALUES (1, 'Atty. Juan S. Dela Cruz', '$default_mission', 'Administrative Operations', '$default_ops', 'Office Administration', '$default_card')");
}

$welcomeName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin');

// ==========================================
// 2. PROCESS FORM SUBMISSION (UNLOCKED)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_about'])) {
    $head_name = htmlspecialchars($_POST['head_name']);
    $mission_text = nl2br(htmlspecialchars($_POST['mission_text']));
    $ops_title = htmlspecialchars($_POST['ops_title']);
    $ops_text = nl2br(htmlspecialchars($_POST['ops_text']));
    $card_title = htmlspecialchars($_POST['card_title']);
    $card_text = nl2br(htmlspecialchars($_POST['card_text']));

    $stmt = $conn->prepare("UPDATE about_info SET head_name=?, mission_text=?, ops_title=?, ops_text=?, card_title=?, card_text=? WHERE id=1");
    if ($stmt) {
        $stmt->bind_param("ssssss", $head_name, $mission_text, $ops_title, $ops_text, $card_title, $card_text);
        $stmt->execute();
        $stmt->close();
        header("Location: about.php");
        exit();
    } else {
        die("Database Error: " . $conn->error);
    }
}

// ==========================================
// 3. FETCH ABOUT INFO FROM DATABASE
// ==========================================
$sql = "SELECT * FROM about_info WHERE id=1";
$result = $conn->query($sql);
$about = [];
if ($result && $result->num_rows > 0) {
    $about = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Edit Button Styles */
        .btn-edit-top {
            background-color: #c5a059;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .btn-edit-top:hover {
            background-color: #a38243;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 46, 93, 0.6);
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            margin: 40px auto;
            padding: 30px;
            width: 90%;
            max-width: 700px;
            border-radius: 6px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #c5a059;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .form-input-styled {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-bottom: 15px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            color: #002e5d;
            margin-bottom: 5px;
            font-size: 0.85rem;
        }

        .btn-file {
            background-color: #002e5d;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
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
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="nav-links">
            <li><a href="case.php" class="<?php echo ($current_page == 'case.php') ? 'active' : ''; ?>">Case</a></li>
            <li><a href="clearance.php" class="<?php echo ($current_page == 'clearance.php') ? 'active' : ''; ?>">Clearance</a></li>
            <li><a href="announcement.php" class="<?php echo ($current_page == 'announcements.php') ? 'active' : ''; ?>">Announcements</a></li>
            <li><a href="feedback.php" class="<?php echo ($current_page == 'feedback.php') ? 'active' : ''; ?>">Feedback</a></li>
            <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a></li>
            <li><a href="contactus.php" class="<?php echo ($current_page == 'contactus.php') ? 'active' : ''; ?>">Contact Us</a></li>
            <li><a href="settings.php" class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">Settings</a></li>
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

        <div class="section-card" style="border-radius: 8px;">

            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="color: #002e5d; margin: 0;">About the Office</h2>
                    <p style="color: #666; margin: 5px 0 0 0;">Learn about the leadership and administration of the City Prosecution Office.</p>
                </div>

                <button class="btn-edit-top" onclick="openEditModal()">Edit Info</button>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

            <div class="about-section" style="margin-top: 30px;">
                <div class="leader-profile" style="background: #f8f9fa; padding: 30px; border-radius: 8px; border-left: 5px solid #c5a059;">
                    <div class="profile-details">
                        <span class="role-badge" style="background: #002e5d; color: white; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase;">Head Prosecutor</span>
                        <h3 style="color: #002e5d; margin: 15px 0 10px 0; font-size: 1.5rem;"><?php echo htmlspecialchars_decode($about['head_name'] ?? ''); ?></h3>
                        <p class="mission-text" style="font-style: italic; color: #555; line-height: 1.6;">
                            "<?php echo htmlspecialchars_decode($about['mission_text'] ?? ''); ?>"
                        </p>
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

            <div class="admin-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div class="admin-info">
                    <h4 style="color: #002e5d; margin-bottom: 15px; font-size: 1.2rem;"><?php echo htmlspecialchars_decode($about['ops_title'] ?? ''); ?></h4>
                    <p style="color: #555; line-height: 1.6;"><?php echo htmlspecialchars_decode($about['ops_text'] ?? ''); ?></p>
                </div>
                <div class="admin-card" style="background: white; border: 1px solid #e2e8f0; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); text-align: center;">
                    <div class="admin-icon" style="font-size: 2.5rem; margin-bottom: 15px;">⚖️</div>
                    <h5 style="color: #002e5d; font-size: 1.1rem; margin-bottom: 10px;"><?php echo htmlspecialchars_decode($about['card_title'] ?? ''); ?></h5>
                    <p style="color: #666; font-size: 0.9rem; line-height: 1.5;"><?php echo htmlspecialchars_decode($about['card_text'] ?? ''); ?></p>
                </div>
            </div>

        </div>
    </main>

    <div id="editAboutModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <div class="form-header-official" style="text-align:center; border-bottom: 2px solid #ccc; margin-bottom: 20px; padding-bottom:10px;">
                <h3 style="color:#002e5d;">Edit About Us Content</h3>
            </div>

            <form method="POST" action="about.php" class="data-form">
                <?php
                // Helper to clean up <br> tags for the textareas
                function strip_br(string $string)
                {
                    return str_replace(array('<br>', '<br/>', '<br />'), '', $string);
                }
                ?>

                <h4 style="color: #c5a059; margin-bottom: 10px;">Head Prosecutor Section</h4>
                <div class="form-group">
                    <label>Head Prosecutor Name:</label>
                    <input type="text" name="head_name" value="<?php echo htmlspecialchars_decode($about['head_name'] ?? ''); ?>" required class="form-input-styled">
                </div>
                <div class="form-group">
                    <label>Mission Statement / Quote:</label>
                    <textarea name="mission_text" rows="3" required class="form-input-styled"><?php echo strip_br($about['mission_text'] ?? ''); ?></textarea>
                </div>

                <hr style="border-top: 1px solid #eee; margin: 20px 0;">

                <h4 style="color: #c5a059; margin-bottom: 10px;">Operations Details</h4>
                <div class="form-group">
                    <label>Operations Title:</label>
                    <input type="text" name="ops_title" value="<?php echo htmlspecialchars_decode($about['ops_title'] ?? ''); ?>" required class="form-input-styled">
                </div>
                <div class="form-group">
                    <label>Operations Description:</label>
                    <textarea name="ops_text" rows="3" required class="form-input-styled"><?php echo strip_br($about['ops_text'] ?? ''); ?></textarea>
                </div>

                <hr style="border-top: 1px solid #eee; margin: 20px 0;">

                <h4 style="color: #c5a059; margin-bottom: 10px;">Admin Card Feature</h4>
                <div class="form-group">
                    <label>Card Title:</label>
                    <input type="text" name="card_title" value="<?php echo htmlspecialchars_decode($about['card_title'] ?? ''); ?>" required class="form-input-styled">
                </div>
                <div class="form-group">
                    <label>Card Description:</label>
                    <textarea name="card_text" rows="2" required class="form-input-styled"><?php echo strip_br($about['card_text'] ?? ''); ?></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" name="update_about" class="btn-file">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editAboutModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editAboutModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            let modal = document.getElementById('editAboutModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>