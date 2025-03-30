<?php
session_start();
include 'database.php';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if the email exists
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            if ($user['role'] == 'admin' || $user['role'] == 'staff') { 
                // ✅ Store User ID in Session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                error_log("Login successful. User ID: " . $_SESSION["user_id"]); // Debugging

                header("Location: index.php");
                exit();
            } else {
                $error_msg = "You do not have permission to access this page.";
            }
        } else {
            $error_msg = "Invalid email or password.";
        }
    } else {
        $error_msg = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pump It Jonathan</title>
    <link rel="stylesheet" href="loginStyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"/>
</head>
<body>

    <div class="container" id="container">
        <div class="form-container register-container">
            <form method="POST" action="register.php">
                <h1>Register Here</h1>
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <div class="password-container">
                    <div class="input-wrapper">
                        <input type="password" name="password" id="register-password" placeholder="Password" required>
                        <i class="ri-eye-off-fill togglePassword" data-target="register-password"></i>
                    </div>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required>
                        <i class="ri-eye-off-fill togglePassword" data-target="confirm-password"></i>
                    </div>
                </div>
                <div class="role-container">
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <button type="submit" name="register">Register</button>
            </form>
        </div>


        <div class="form-container login-container">
            <form method="POST" action="login.php">
                <h1>Login Here</h1>
                <input type="email" name="email" placeholder="Email" required>
                <div class="password-container">
                    <div class="input-wrapper">
                        <input type="password" name="password" id="login-password" placeholder="Password" required>
                        <i class="ri-eye-off-fill togglePassword" data-target="login-password"></i>
                    </div>
                </div>
                <div class="content">
                    <div class="checkbox">
                        <input type="checkbox" id="remember-me">
                        <label for="remember-me">Remember Me</label>
                    </div>
                    <div class="pass-link">
                        <a href="#">Forgot password?</a>
                    </div>
                </div>
                <button type="submit" name="login">Login</button>
                <?php if (!empty($error_msg)) { echo "<p class='error-msg'>$error_msg</p>"; } ?>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1 class="title">Hello,<br>friends!</h1>
                    <p>If you have an account, login here <br>and have fun.</p>
                    <button class="ghost" id="login">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1 class="title">Start your <br>journey now!</h1>
                    <p>If you don't have an account yet, join us and start your journey.</p>
                    <button class="ghost" id="register">Register</button>
                </div>
            </div>
        </div>
        
    </div>
    <script src="login.js"></script>
</body>
</html>
