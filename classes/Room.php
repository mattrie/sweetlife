<?php
require_once 'config/database.php';

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAllRooms($type = null) {
        $query = "SELECT r.*, rc.name as category_name 
                  FROM " . $this->table . " r 
                  LEFT JOIN room_categories rc ON r.category_id = rc.id 
                  WHERE r.status = 'available'";
        
        if ($type) {
            $query .= " AND r.type = :type";
        }
        
        $query .= " ORDER BY r.price_per_night ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoomById($id) {
        $query = "SELECT r.*, rc.name as category_name 
                  FROM " . $this->table . " r 
                  LEFT JOIN room_categories rc ON r.category_id = rc.id 
                  WHERE r.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkAvailability($room_id, $check_in, $check_out) {
        $query = "SELECT COUNT(*) as count FROM bookings 
                  WHERE room_id = :room_id 
                  AND status NOT IN ('cancelled') 
                  AND (
                      (check_in_date <= :check_in AND check_out_date > :check_in) OR
                      (check_in_date < :check_out AND check_out_date >= :check_out) OR
                      (check_in_date >= :check_in AND check_out_date <= :check_out)
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':check_in', $check_in);
        $stmt->bindParam(':check_out', $check_out);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }

    public function getUnavailableDates($room_id, $months = 3) {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+{$months} months"));
        
        $query = "SELECT check_in_date, check_out_date FROM bookings 
                  WHERE room_id = :room_id 
                  AND status NOT IN ('cancelled') 
                  AND check_out_date >= :start_date 
                  AND check_in_date <= :end_date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRoom($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (room_number, category_id, type, price_per_night, max_adults, max_children, description, amenities, images) 
                  VALUES (:room_number, :category_id, :type, :price_per_night, :max_adults, :max_children, :description, :amenities, :images)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':room_number', $data['room_number']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':price_per_night', $data['price_per_night']);
        $stmt->bindParam(':max_adults', $data['max_adults']);
        $stmt->bindParam(':max_children', $data['max_children']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':amenities', $data['amenities']);
        $stmt->bindParam(':images', $data['images']);
        
        return $stmt->execute();
    }

    public function updateRoom($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET room_number = :room_number, category_id = :category_id, type = :type, 
                      price_per_night = :price_per_night, max_adults = :max_adults, 
                      max_children = :max_children, description = :description, 
                      amenities = :amenities, images = :images, updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':room_number', $data['room_number']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':price_per_night', $data['price_per_night']);
        $stmt->bindParam(':max_adults', $data['max_adults']);
        $stmt->bindParam(':max_children', $data['max_children']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':amenities', $data['amenities']);
        $stmt->bindParam(':images', $data['images']);
        
        return $stmt->execute();
    }

    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getRoomCategories() {
        $query = "SELECT * FROM room_categories ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>