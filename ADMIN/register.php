<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='login.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Email is already registered.'); window.location.href='login.php';</script>";
        exit;
    }

    if ($role !== 'admin' && $role !== 'staff') {
        echo "<script>alert('Invalid role selection. Only Admin and Staff can be added.'); window.location.href='login.php';</script>";
        exit;
    }
    
    // Insert new user
    $query = "INSERT INTO users (fullname, email, password, role) VALUES ('$fullname', '$email', '$hashed_password', '$role')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Registration failed. Please try again.'); window.location.href='login.php';</script>";
    }
}
?>
