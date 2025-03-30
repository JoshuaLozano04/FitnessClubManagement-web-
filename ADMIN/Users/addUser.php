<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $role = trim($_POST['role']);

    // Validate required fields
    if (empty($fullname) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        echo "All fields are required.";
        exit();
    }

    // Validate role
    if ($role !== 'admin' && $role !== 'staff') {
        die("Invalid role selection. Only Admin and Staff can be added.");
    }
    

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email is already registered
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        echo "Email already exists.";
        exit();
    }

    // Insert new Admin or Staff user
    $stmt = $conn->prepare("INSERT INTO users (profile_picture, fullname, email, password, role, status) VALUES ('default.png', ?, ?, ?, ?, 'inactive')");
    $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo "User added successfully!";
    } else {
        echo "Error adding user.";
    }

    $stmt->close();
    $conn->close();
}
?>
