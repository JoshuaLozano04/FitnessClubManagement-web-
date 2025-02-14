<?php
session_start();
include 'database.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query the database for the provided email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    // Check if the email exists
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            if ($user['role'] == 'admin') {
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit();
            } else {
                $error_msg = "You do not have permission to access this page";
            }
        } else {
            $error_msg = "Invalid email or password";
        }
    } else {
        $error_msg = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="loginStyle.css">
</head>
<body>

    <div class="login-container">
        <div class="login-form">
            <div class="logo">
                <img src="img/01.png" alt="Admin Logo">
            </div>
            <h2>Login</h2>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" required placeholder="Enter your Email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="login-btn">Login</button>
                <?php if (isset($error_msg)) { echo "<p class='error-msg'>$error_msg</p>"; } ?>
            </form>
        </div>
    </div>

</body>
</html>
