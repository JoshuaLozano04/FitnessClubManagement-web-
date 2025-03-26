<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('your_secret_key'); // Replace with your Stripe secret key

header('Content-Type: application/json');

try {
    $amount = $_POST['amount']; // Get amount from the Android app
    $currency = 'usd';

    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount * 100, // Convert to cents
        'currency' => $currency,
        'payment_method_types' => ['card'],
    ]);

    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
