<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../classes/Room.php';

$room = new Room();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Get single room
            $result = $room->getRoomById($_GET['id']);
            if ($result) {
                $result['images'] = json_decode($result['images'], true);
            }
        } elseif (isset($_GET['type'])) {
            // Get rooms by type
            $result = $room->getAllRooms($_GET['type']);
            foreach ($result as &$r) {
                $r['images'] = json_decode($r['images'], true);
            }
        } else {
            // Get all rooms
            $result = $room->getAllRooms();
            foreach ($result as &$r) {
                $r['images'] = json_decode($r['images'], true);
            }
        }
        echo json_encode($result);
        break;
        
    case 'POST':
        // Check availability
        if (isset($_POST['action']) && $_POST['action'] === 'check_availability') {
            $room_id = $_POST['room_id'];
            $check_in = $_POST['check_in'];
            $check_out = $_POST['check_out'];
            
            $available = $room->checkAvailability($room_id, $check_in, $check_out);
            $unavailable_dates = $room->getUnavailableDates($room_id);
            
            echo json_encode([
                'available' => $available,
                'unavailable_dates' => $unavailable_dates
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>