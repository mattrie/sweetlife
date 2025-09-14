<?php
require_once 'config/database.php';

class Payment {
    private $conn;
    private $table = 'payments';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function createPayment($data) {
        $transaction_reference = $this->generateTransactionReference();
        
        $query = "INSERT INTO " . $this->table . " 
                  (booking_id, transaction_reference, amount, paystack_reference) 
                  VALUES (:booking_id, :transaction_reference, :amount, :paystack_reference)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':booking_id', $data['booking_id']);
        $stmt->bindParam(':transaction_reference', $transaction_reference);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':paystack_reference', $data['paystack_reference'] ?? null);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'payment_id' => $this->conn->lastInsertId(),
                'transaction_reference' => $transaction_reference
            ];
        }
        
        return ['success' => false];
    }

    public function updatePaymentStatus($transaction_reference, $status, $paystack_reference = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, paystack_reference = :paystack_reference, 
                      payment_date = CURRENT_TIMESTAMP 
                  WHERE transaction_reference = :transaction_reference";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':paystack_reference', $paystack_reference);
        $stmt->bindParam(':transaction_reference', $transaction_reference);
        
        if ($stmt->execute()) {
            // Update booking payment status
            $this->updateBookingPaymentStatus($transaction_reference, $status);
            return true;
        }
        
        return false;
    }

    private function updateBookingPaymentStatus($transaction_reference, $status) {
        $query = "UPDATE bookings b 
                  JOIN payments p ON b.id = p.booking_id 
                  SET b.payment_status = :payment_status,
                      b.status = CASE WHEN :status = 'success' THEN 'confirmed' ELSE b.status END
                  WHERE p.transaction_reference = :transaction_reference";
        
        $stmt = $this->conn->prepare($query);
        $payment_status = $status === 'success' ? 'paid' : 'pending';
        $stmt->bindParam(':payment_status', $payment_status);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':transaction_reference', $transaction_reference);
        
        return $stmt->execute();
    }

    public function getPaymentByReference($reference) {
        $query = "SELECT p.*, b.booking_reference, b.total_amount as booking_amount
                  FROM " . $this->table . " p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  WHERE p.transaction_reference = :reference";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyPaystackPayment($reference) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
                "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            return ['success' => false, 'message' => 'cURL Error: ' . $err];
        }
        
        return json_decode($response, true);
    }

    private function generateTransactionReference() {
        return 'TXN' . date('YmdHis') . mt_rand(1000, 9999);
    }

    public function getAllPayments() {
        $query = "SELECT p.*, b.booking_reference, u.name as user_name, u.email as user_email
                  FROM " . $this->table . " p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  LEFT JOIN users u ON b.user_id = u.id
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>