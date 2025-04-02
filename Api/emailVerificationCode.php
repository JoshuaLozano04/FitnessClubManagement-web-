<?php
include "database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $body, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pumpitjonathan66@gmail.com';
        $mail->Password = 'trfu rtge spnl owuo'; // Use an App Password instead of your main password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Priority
        $mail->Priority = 1;
        $mail->addCustomHeader("X-Priority: 1");
        $mail->addCustomHeader("X-MSMail-Priority: High");
        $mail->addCustomHeader("Importance: High");

        // Sender Info
        $mail->setFrom('pumpitjonathan66@gmail.com', 'Pumping Iron Gym');
        $mail->addAddress($to);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "Your Confirmation Code is $code";
        $mail->Body = "
            <p><strong>Your confirmation code:</strong> <span style='font-size:18px; color:red;'>$code</span></p>
            <p><strong>Tip:</strong> Move this email to your 'Primary' inbox to avoid missing important updates.</p>
            <br>
            <p>Best regards,</p>
            <p>Pumping Iron Gym Team</p>
        ";

        // Include a plain-text version (helps avoid spam filters)
        $mail->AltBody = "Your confirmation code is: $code. Please use this to complete your registration.";

        // Add a reply-to address (helps with legitimacy)
        $mail->addReplyTo('pumpitjonathan66@gmail.com', 'Pumping Iron Gym');

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

    $emailResult = sendEmail($email, $subject, $body, $code );

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