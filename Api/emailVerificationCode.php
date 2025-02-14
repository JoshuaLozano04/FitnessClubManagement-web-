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
        $stmt = $conn->prepare("INSERT INTO email_confirmation (email, code) VALUES (?, ?)");
        if ($stmt === false) {
            respond("Failed to prepare statement: " . $conn->error, 500);
        }

        $stmt->bind_param("si", $email, $code);

        if ($stmt->execute()) {
            respond("Confirmation code sent and stored successfully.");
        } else {
            respond("Error executing statement: " . $stmt->error, 500);
        }

        $stmt->close();
    } else {
        respond($emailResult, 500);
    }

    $conn->close();
} else {
    respond("Invalid request method", 405);
}
?>