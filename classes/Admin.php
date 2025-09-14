<?php
require_once 'config/database.php';

class Admin {
    private $conn;
    private $table = 'admins';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            return [
                'success' => true,
                'admin' => $admin,
                'message' => 'Login successful'
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    public function getDashboardStats() {
        $stats = [];
        
        // Total rooms
        $query = "SELECT COUNT(*) as total_rooms FROM rooms";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_rooms'];
        
        // Available rooms
        $query = "SELECT COUNT(*) as available_rooms FROM rooms WHERE status = 'available'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['available_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['available_rooms'];
        
        // Total bookings
        $query = "SELECT COUNT(*) as total_bookings FROM bookings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_bookings'];
        
        // Active bookings
        $query = "SELECT COUNT(*) as active_bookings FROM bookings 
                  WHERE status IN ('confirmed', 'checked_in') 
                  AND check_out_date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['active_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['active_bookings'];
        
        // Total revenue this month
        $query = "SELECT COALESCE(SUM(b.total_amount), 0) as monthly_revenue 
                  FROM bookings b 
                  JOIN payments p ON b.id = p.booking_id 
                  WHERE p.status = 'success' 
                  AND MONTH(b.created_at) = MONTH(CURDATE()) 
                  AND YEAR(b.created_at) = YEAR(CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['monthly_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['monthly_revenue'];
        
        // Occupancy rate
        $query = "SELECT COUNT(*) as occupied_rooms FROM bookings 
                  WHERE status IN ('confirmed', 'checked_in') 
                  AND CURDATE() BETWEEN check_in_date AND check_out_date";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $occupied = $stmt->fetch(PDO::FETCH_ASSOC)['occupied_rooms'];
        $stats['occupancy_rate'] = $stats['total_rooms'] > 0 ? round(($occupied / $stats['total_rooms']) * 100, 2) : 0;
        
        return $stats;
    }

    public function getRecentBookings($limit = 10) {
        $query = "SELECT b.*, r.room_number, rc.name as category_name, u.name as user_name
                  FROM bookings b
                  LEFT JOIN rooms r ON b.room_id = r.id
                  LEFT JOIN room_categories rc ON r.category_id = rc.id
                  LEFT JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>