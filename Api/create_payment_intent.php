<?php
require '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51R7qAeBNSwOEu2mphvxQDvqLNAjVWiFUOpEknPN77cyvHdk8kyfWzz6YsnrbmvBp1vriWaHZThLNhWCMKTbVVAJE00RQ2VYmZG'); // Replace with your actual Stripe secret key

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['amount'])) {
        throw new Exception("Amount not provided");
    }

    $amount = intval($input['amount']);
    $currency = 'usd';

    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => $currency,
        'payment_method_types' => ['card'],
    ]);

    echo json_encode(['clientSecret' => $paymentIntent->client_secret], JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
