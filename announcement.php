<?php
// 1. Start session and connect to the database
session_start();
require '../connection/db.php'; // Ensure this file is in the same folder, or adjust the path!

// 2. Set welcome name
$welcomeName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements & Chat - Office of the Prosecutor</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific overrides to ensure the chat looks perfect */
        .chat-container {
            height: 65vh;
            display: flex;
            flex-direction: column;
            background: #f0f2f5;
            border-radius: 0 0 8px 0;
        }

        .chat-box {
            flex-grow: 1;
            overflow-y: auto;
            padding: 25px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message-row {
            display: flex;
            flex-direction: column;
            max-width: 75%;
        }

        .message-row.received {
            align-self: flex-start;
        }

        .message-row.sent {
            align-self: flex-end;
            align-items: flex-end;
        }

        .msg-bubble {
            padding: 12px 18px;
            border-radius: 18px;
            font-size: 0.9rem;
            line-height: 1.4;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .received .msg-bubble {
            background: white;
            color: #333;
            border-top-left-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .sent .msg-bubble {
            background: #002e5d;
            color: white;
            border-top-right-radius: 4px;
        }

        .msg-info {
            font-size: 0.7rem;
            color: #666;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .sent .msg-info {
            color: #888;
        }

        .msg-actions {
            display: flex;
            gap: 8px;
            margin-top: 5px;
            opacity: 0.3;
            transition: 0.2s;
        }

        .message-row:hover .msg-actions {
            opacity: 1;
        }

        .sent .msg-actions {
            justify-content: flex-end;
        }

        .react-btn,
        .pin-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            color: #666;
            transition: 0.2s;
        }

        .react-btn:hover {
            transform: scale(1.2);
        }

        .pin-btn:hover,
        .pin-btn.active {
            color: #c5a059;
        }

        /* Emote Display Bar */
        .reaction-bar {
            display: flex;
            gap: 5px;
            margin-top: -5px;
            z-index: 10;
            padding: 0 10px;
        }

        .reaction-badge {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 2px 6px;
            font-size: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 3px;
        }

        /* The specific input area */
        .chat-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
        }

        .input-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .input-wrapper input {
            flex-grow: 1;
            padding: 12px 20px;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
            background: #f8fafc;
        }

        .btn-send {
            background: #c5a059;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.2s;
        }

        .btn-send:hover {
            background: #002e5d;
        }

        /* Pinned Announcement Specifics */
        .pinned-card {
            position: relative;
        }

        .unpin-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #c5a059;
            font-size: 1.2rem;
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

                    <span>Office of the Prosecutor Management System</span>

                </div>
            </div>
        </div>

        <div class="section-card" style="padding: 0; overflow: hidden;">
            <div class="messenger-layout">

                <div class="pinned-sidebar">
                    <h4 style="color: #002e5d; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                        <i class="fa-solid fa-thumbtack" style="color:#c5a059;"></i> Pinned Announcements
                    </h4>
                    <div class="pinned-list">

                        <div class="pinned-card">
                            <i class="fa-solid fa-thumbtack unpin-badge"></i>
                            <span class="pin-date">May 15, 2026</span>
                            <h4>System Maintenance</h4>
                            <p>The system will be down for maintenance this weekend. Please save your case files.</p>
                            <span class="pin-author">Admin</span>
                        </div>

                    </div>
                </div>

                <div class="chat-main">
                    <div class="chat-header">
                        <span class="status-indicator"></span>
                        <strong>Office General Chat</strong>
                    </div>

                    <div class="chat-box chat-container" id="chatContainer">

                        <div class="message-row received">
                            <div class="msg-info">Prosecutor Santos <small>10:30 AM</small></div>
                            <div class="msg-bubble">
                                Please review the new clearance guidelines posted yesterday.
                            </div>
                            <div class="reaction-bar">
                                <span class="reaction-badge">👍 2</span>
                            </div>
                            <div class="msg-actions">
                                <button class="react-btn"><i class="fa-regular fa-face-smile"></i></button>
                                <button class="pin-btn"><i class="fa-solid fa-thumbtack"></i></button>
                            </div>
                        </div>

                        <div class="message-row sent">
                            <div class="msg-info">You <small>10:35 AM</small></div>
                            <div class="msg-bubble">
                                Noted. I am checking the records right now.
                            </div>
                            <div class="msg-actions">
                                <button class="react-btn"><i class="fa-regular fa-face-smile"></i></button>
                            </div>
                        </div>

                    </div>

                    <div class="chat-input-area">
                        <form method="POST" action="">
                            <div class="input-wrapper">
                                <input type="text" name="message" placeholder="Type an announcement or message..." required>
                                <button type="submit" class="btn-send"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <script src="../script.js"></script>
    <script>
        // Auto-scroll chat to bottom on load
        window.onload = function() {
            var chatBox = document.getElementById("chatContainer");
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        };
    </script>
</body>

</html>