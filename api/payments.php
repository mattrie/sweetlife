<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../classes/Payment.php';
require_once '../classes/Booking.php';

$payment = new Payment();
$booking = new Booking();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        if (isset($_POST['action']) && $_POST['action'] === 'initialize_payment') {
            $booking_id = $_POST['booking_id'];
            $booking_data = $booking->getBookingById($booking_id);
            
            if (!$booking_data) {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Create payment record
            $payment_data = [
                'booking_id' => $booking_id,
                'amount' => $booking_data['total_amount']
            ];
            
            $payment_result = $payment->createPayment($payment_data);
            
            if ($payment_result['success']) {
                // Initialize Paystack payment
                $paystack_data = [
                    'email' => $booking_data['user_email'],
                    'amount' => $booking_data['total_amount'] * 100, // Convert to kobo
                    'reference' => $payment_result['transaction_reference'],
                    'callback_url' => SITE_URL . '/payment-callback.php',
                    'metadata' => [
                        'booking_id' => $booking_id,
                        'booking_reference' => $booking_data['booking_reference'],
                        'user_name' => $booking_data['user_name']
                    ]
                ];
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($paystack_data),
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
                        "Content-Type: application/json",
                    ],
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                
                $paystack_response = json_decode($response, true);
                
                if ($paystack_response['status']) {
                    echo json_encode([
                        'success' => true,
                        'authorization_url' => $paystack_response['data']['authorization_url'],
                        'reference' => $paystack_response['data']['reference']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Payment initialization failed']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create payment record']);
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'verify_payment') {
            $reference = $_POST['reference'];
            $verification = $payment->verifyPaystackPayment($reference);
            
            if ($verification['status'] && $verification['data']['status'] === 'success') {
                $payment->updatePaymentStatus($reference, 'success', $verification['data']['reference']);
                echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
            } else {
                $payment->updatePaymentStatus($reference, 'failed');
                echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>