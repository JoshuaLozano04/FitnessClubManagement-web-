<?php
include 'database.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $query = "SELECT trainee_name, checkin_time, checkout_time FROM attendance WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "No record found"]);
    }
}
?>
