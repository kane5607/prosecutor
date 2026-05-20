<?php
session_start();
require '../connection/db.php';

// ==========================================
// 1. AUTO-SETUP DATABASE TABLE
// ==========================================
@$conn->query("CREATE TABLE IF NOT EXISTS contact_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    address TEXT,
    phone TEXT,
    email TEXT,
    hours TEXT,
    hours_note TEXT
)");

$check = $conn->query("SELECT * FROM contact_info WHERE id=1");
if ($check && $check->num_rows == 0) {
    $conn->query("INSERT INTO contact_info (id, address, phone, email, hours, hours_note) VALUES (1, 'Hall of Justice Building,<br>Pagadian City, Zamboanga del Sur,<br>Philippines, 7016', 'Main: (062) 214-XXXX<br>Hotline: +63 9XX XXX XXXX', 'ocp.pagadian@doj.gov.ph<br>info.prosecutor@gmail.com', 'Monday - Friday: 8:00 AM - 5:00 PM', 'Closed on Weekends and Public Holidays')");
}

$welcomeName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin');

// ==========================================
// 2. PROCESS FORM SUBMISSION (UNLOCKED)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_contact'])) {
    $address = nl2br(htmlspecialchars($_POST['address']));
    $phone = nl2br(htmlspecialchars($_POST['phone']));
    $email = nl2br(htmlspecialchars($_POST['email']));
    $hours = htmlspecialchars($_POST['hours']);
    $hours_note = htmlspecialchars($_POST['hours_note']);

    $stmt = $conn->prepare("UPDATE contact_info SET address=?, phone=?, email=?, hours=?, hours_note=? WHERE id=1");
    if ($stmt) {
        $stmt->bind_param("sssss", $address, $phone, $email, $hours, $hours_note);
        $stmt->execute();
        $stmt->close();
        header("Location: contactus.php");
        exit();
    } else {
        die("Database Error: " . $conn->error);
    }
}

// ==========================================
// 3. FETCH CONTACT INFO FROM DATABASE
// ==========================================
$sql = "SELECT * FROM contact_info WHERE id=1";
$result = $conn->query($sql);

$contact = [
    'address' => "Hall of Justice Building,<br>Pagadian City, Zamboanga del Sur,<br>Philippines, 7016",
    'phone' => "Main: (062) 214-XXXX<br>Hotline: +63 9XX XXX XXXX",
    'email' => "ocp.pagadian@doj.gov.ph<br>info.prosecutor@gmail.com",
    'hours' => "Monday - Friday: 8:00 AM - 5:00 PM",
    'hours_note' => "Closed on Weekends and Public Holidays"
];

if ($result && $result->num_rows > 0) {
    $contact = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Keep page-specific modal styles here */
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
            max-width: 600px;
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
            <li><a href="announcement.php" class="<?php echo ($current_page == 'announcement.php') ? 'active' : ''; ?>">Announcements</a></li>
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
                    <span>Office of the Prosecutor</span>
                    <span>Management System</span>
                </div>
            </div>
        </div>

        <div class="section-card" style="border-radius: 8px;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="color: #002e5d; margin: 0;"><i class="fa-solid fa-address-book" style="color: #c5a059;"></i> Office Contact Information</h2>
                    <p style="margin: 5px 0 0 0;">Official contact details for the City Prosecution Office of Pagadian City.</p>
                </div>

                <button class="btn-edit-top" onclick="openEditModal()">Edit Info</button>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 20px;">
                <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; background: #f8fafc;">
                    <i class="fa-solid fa-location-dot" style="font-size: 2rem; color: #002e5d; margin-bottom: 15px;"></i>
                    <h4 style="color: #002e5d; margin-bottom: 10px;">Our Office</h4>
                    <p style="color: #555; line-height: 1.5;"><?php echo $contact['address']; ?></p>
                </div>

                <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; background: #f8fafc;">
                    <i class="fa-solid fa-phone" style="font-size: 2rem; color: #002e5d; margin-bottom: 15px;"></i>
                    <h4 style="color: #002e5d; margin-bottom: 10px;">Phone & Hotline</h4>
                    <p style="color: #555; line-height: 1.5;"><?php echo $contact['phone']; ?></p>
                </div>

                <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; background: #f8fafc;">
                    <i class="fa-solid fa-envelope" style="font-size: 2rem; color: #002e5d; margin-bottom: 15px;"></i>
                    <h4 style="color: #002e5d; margin-bottom: 10px;">Email Address</h4>
                    <p style="color: #555; line-height: 1.5;"><?php echo $contact['email']; ?></p>
                </div>
            </div>

            <div style="margin-top: 40px; padding: 20px; background: #002e5d; color: white; border-radius: 8px; text-align: center;">
                <h4 style="color: #c5a059; margin-bottom: 10px;">Business Hours</h4>
                <p><?php echo htmlspecialchars_decode($contact['hours']); ?></p>
                <p style="font-size: 0.85rem; margin-top: 5px; opacity: 0.8;"><?php echo htmlspecialchars_decode($contact['hours_note']); ?></p>
            </div>
        </div>
    </main>

    <div id="editContactModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <div class="form-header-official" style="text-align:center; border-bottom: 2px solid #ccc; margin-bottom: 20px; padding-bottom:10px;">
                <h3 style="color:#002e5d;">Edit Contact Information</h3>
            </div>

            <form method="POST" action="contactus.php" class="data-form">
                <?php
                function strip_br(string $string)
                {
                    return str_replace(array('<br>', '<br/>', '<br />'), '', $string);
                }
                ?>
                <div class="form-group">
                    <label>Our Office Address (Press Enter for new line):</label>
                    <textarea name="address" rows="3" required class="form-input-styled"><?php echo strip_br($contact['address']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Phone & Hotline (Press Enter for new line):</label>
                    <textarea name="phone" rows="2" required class="form-input-styled"><?php echo strip_br($contact['phone']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Email Address (Press Enter for new line):</label>
                    <textarea name="email" rows="2" required class="form-input-styled"><?php echo strip_br($contact['email']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Business Hours Main Text:</label>
                    <input type="text" name="hours" value="<?php echo strip_br($contact['hours']); ?>" required class="form-input-styled">
                </div>
                <div class="form-group">
                    <label>Business Hours Subtext (Note):</label>
                    <input type="text" name="hours_note" value="<?php echo strip_br($contact['hours_note']); ?>" required class="form-input-styled">
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                    <button type="submit" name="update_contact" class="btn-file">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editContactModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editContactModal').style.display = 'none';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('editContactModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <script src="../script.js"></script>
</body>

</html>