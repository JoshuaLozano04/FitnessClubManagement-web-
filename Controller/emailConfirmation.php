<?php
include 'database.php'; 

function getEmailConfirmation($email, $code) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM email_confirmation WHERE email = ? AND code = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM email_confirmation WHERE email = ? AND code = ?");
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();

        return array("status" => "success", "message" => "Email and code match found and confirmed.", "email" => $email, "code" => $code);
    } else {
        return array("status" => "error", "message" => "No match found for the provided email and code.", "email" => $email, "code" => $code);
    }
}

function deleteExpiredEmailConfirmations() {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM email_confirmation WHERE expired <= NOW()");
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);

    $email = isset($data['email']) ? $data['email'] : '';
    $code = isset($data['code']) ? $data['code'] : '';

    if (!empty($email) && !empty($code)) {
        $response = getEmailConfirmation($email, $code);
    } else {
        $response = array("status" => "error", "message" => "Email and code are required.", "email" => $email, "code" => $code);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();
?>