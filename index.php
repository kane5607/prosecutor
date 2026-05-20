<?php
session_start();

// Connect to the database
require 'connection/db.php';

// If they are already logged in, send them straight inside
if (!empty($_SESSION['username'])) {
    header("Location: admin/index.php"); // Adjust if your dashboard page is named differently (e.g., admin/case.php)
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Search the database for this user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Check if the password matches
            if ($password === $row['password']) {

                // SUCCESS! Set the session variables
                $_SESSION['username'] = $row['username'];

                // Save the role if it exists so the rest of your system knows who is logged in
                if (isset($row['role'])) {
                    $_SESSION['role'] = $row['role'];
                }

                // Redirect them into the system
                header("Location: admin/index.php");
                exit();
            } else {
                $error_message = "Incorrect password. Please try again.";
            }
        } else {
            $error_message = "We couldn't find an account with that username.";
        }
        $stmt->close();
    } else {
        $error_message = "Database connection error.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Office of the Prosecutor</title>
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for the eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            border-top: 5px solid #002e5d;
            text-align: center;
            box-sizing: border-box;
        }

        .login-card img {
            width: 100px;
            margin-bottom: 20px;
        }

        h2 {
            color: #002e5d;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Password container styles for the eye icon */
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container input {
            padding-right: 40px;
            /* Make room for the icon */
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #666;
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #002e5d;
        }

        .forgot-password {
            display: block;
            text-align: right;
            font-size: 0.85rem;
            color: #c5a059;
            text-decoration: none;
            margin-top: 5px;
            font-weight: bold;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #b08d4a;
        }

        .btn-login {
            width: 100%;
            background-color: #c5a059;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            transition: background-color 0.2s;
        }

        .btn-login:hover {
            background-color: #b08d4a;
        }

        .error-box {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #ef4444;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <img src="images/image.png" alt="Prosecutor Seal">
        <h2>Login</h2>

        <?php if ($error_message): ?>
            <div class="error-box"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Enter username">
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                    <i class="fa-solid fa-eye toggle-password" id="toggleIcon" onclick="togglePasswordVisibility()"></i>
                </div>
                <!-- Forgot Password Link -->
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" name="login_btn" class="btn-login">Log In</button>
        </form>
    </div>

    <!-- JavaScript to toggle the password view -->
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var toggleIcon = document.getElementById("toggleIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>


</html>