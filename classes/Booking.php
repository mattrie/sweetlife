<?php
require_once 'config/database.php';

class Booking {
    private $conn;
    private $table = 'bookings';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function createBooking($data) {
        $booking_reference = $this->generateBookingReference();
        
        $query = "INSERT INTO " . $this->table . " 
                  (booking_reference, room_id, user_id, check_in_date, check_out_date, 
                   adults, children, total_nights, total_amount, special_requests) 
                  VALUES (:booking_reference, :room_id, :user_id, :check_in_date, :check_out_date, 
                          :adults, :children, :total_nights, :total_amount, :special_requests)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':booking_reference', $booking_reference);
        $stmt->bindParam(':room_id', $data['room_id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':check_in_date', $data['check_in_date']);
        $stmt->bindParam(':check_out_date', $data['check_out_date']);
        $stmt->bindParam(':adults', $data['adults']);
        $stmt->bindParam(':children', $data['children']);
        $stmt->bindParam(':total_nights', $data['total_nights']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':special_requests', $data['special_requests']);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'booking_id' => $this->conn->lastInsertId(),
                'booking_reference' => $booking_reference
            ];
        }
        
        return ['success' => false];
    }

    public function getBookingById($id) {
        $query = "SELECT b.*, r.room_number, r.type as room_type, rc.name as category_name, 
                         u.name as user_name, u.email as user_email, u.phone as user_phone
                  FROM " . $this->table . " b
                  LEFT JOIN rooms r ON b.room_id = r.id
                  LEFT JOIN room_categories rc ON r.category_id = rc.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE b.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBookingByReference($reference) {
        $query = "SELECT b.*, r.room_number, r.type as room_type, rc.name as category_name, 
                         u.name as user_name, u.email as user_email, u.phone as user_phone
                  FROM " . $this->table . " b
                  LEFT JOIN rooms r ON b.room_id = r.id
                  LEFT JOIN room_categories rc ON r.category_id = rc.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE b.booking_reference = :reference";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserBookings($user_id) {
        $query = "SELECT b.*, r.room_number, r.type as room_type, rc.name as category_name,
                         r.images, p.status as payment_status
                  FROM " . $this->table . " b
                  LEFT JOIN rooms r ON b.room_id = r.id
                  LEFT JOIN room_categories rc ON r.category_id = rc.id
                  LEFT JOIN payments p ON b.id = p.booking_id
                  WHERE b.user_id = :user_id
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBookings($status = null) {
        $query = "SELECT b.*, r.room_number, r.type as room_type, rc.name as category_name,
                         u.name as user_name, u.email as user_email, u.phone as user_phone,
                         p.status as payment_status
                  FROM " . $this->table . " b
                  LEFT JOIN rooms r ON b.room_id = r.id
                  LEFT JOIN room_categories rc ON r.category_id = rc.id
                  LEFT JOIN users u ON b.user_id = u.id
                  LEFT JOIN payments p ON b.id = p.booking_id";
        
        if ($status) {
            $query .= " WHERE b.status = :status";
        }
        
        $query .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBookingStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function generateBookingReference() {
        return 'SLH' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotalAmount($room_id, $check_in, $check_out) {
        // Get room price
        $query = "SELECT price_per_night FROM rooms WHERE id = :room_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            return false;
        }
        
        // Calculate nights
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;
        
        return [
            'nights' => $nights,
            'price_per_night' => $room['price_per_night'],
            'total_amount' => $nights * $room['price_per_night']
        ];
    }
}
?>