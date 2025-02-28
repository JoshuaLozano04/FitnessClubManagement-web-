
<?php
$host = "localhost";  // Change if needed
$user = "root";       // Change if needed
$pass = "";           // Change if needed
$dbname = "fitnessclubmanagement_db"; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Welcome to the user account page</p>

</body>
</html>