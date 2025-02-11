<?php
include "database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'pumpitjonathan66@gmail.com'; // SMTP username
        $mail->Password = 'trfu rtge spnl owuo'; // SMTP password (app-specific password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('your_email@gmail.com', 'Pumping Iron Gym');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function deleteExpiredEmailConfirmations() {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM email_confirmation WHERE expired <= NOW()");
    $stmt->execute();
}

function insertEmailConfirmation($email, $code) {
    global $conn;
    deleteExpiredEmailConfirmations();

    $expirationDate = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $stmt = $conn->prepare("INSERT INTO email_confirmation (email, code, expired) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $code, $expirationDate);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return array("status" => "success", "message" => "Email confirmation inserted successfully.");
    } else {
        return array("status" => "error", "message" => "Failed to insert email confirmation.");
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {
    $input = json_decode(file_get_contents('php://input'), true);
} elseif ($contentType === "application/x-www-form-urlencoded" || $contentType === "multipart/form-data") {
    $input = $_POST;
} else {
    parse_str(file_get_contents('php://input'), $input);
}

function respond($message, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(["message" => $message]);
    exit();
}

if ($method === "POST") {
    if (!isset($input['email'])) {
        respond("Email is required", 400);
    }

    $email = $input['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond("Invalid email format", 400);
    }

    $code = rand(1000, 9999);

    $subject = 'Your Confirmation Code';
    $body = "Your confirmation code is: $code";

    $emailResult = sendEmail($email, $subject, $body);

    if ($emailResult === true) {
        $response = insertEmailConfirmation($email, $code);
        if ($response['status'] === "success") {
            respond("Confirmation code sent and stored successfully.");
        } else {
            respond($response['message'], 500);
        }
    } else {
        respond($emailResult, 500);
    }

    $conn->close();
} else {
    respond("Invalid request method", 405);
}
?>