<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../classes/Booking.php';
require_once '../classes/Room.php';
require_once '../classes/User.php';

$booking = new Booking();
$room = new Room();
$user = new User();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        if (isset($_POST['action']) && $_POST['action'] === 'create_booking') {
            // Validate required fields
            $required_fields = ['room_id', 'check_in_date', 'check_out_date', 'adults', 'user_name', 'user_email', 'user_phone'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
                    exit;
                }
            }
            
            // Check room availability
            if (!$room->checkAvailability($_POST['room_id'], $_POST['check_in_date'], $_POST['check_out_date'])) {
                echo json_encode(['success' => false, 'message' => 'Room not available for selected dates']);
                exit;
            }
            
            // Register or get user
            $user_data = [
                'name' => $_POST['user_name'],
                'email' => $_POST['user_email'],
                'phone' => $_POST['user_phone'],
                'password' => 'temp_password_' . time() // Temporary password
            ];
            
            $user_result = $user->register($user_data);
            if (!$user_result['success'] && $user->emailExists($_POST['user_email'])) {
                // User exists, get user ID
                $existing_user = $user->getUserByEmail($_POST['user_email']);
                $user_id = $existing_user['id'];
            } else {
                $user_id = $user_result['user_id'];
            }
            
            // Calculate total amount
            $cost_calculation = $booking->calculateTotalAmount($_POST['room_id'], $_POST['check_in_date'], $_POST['check_out_date']);
            
            if (!$cost_calculation) {
                echo json_encode(['success' => false, 'message' => 'Error calculating booking cost']);
                exit;
            }
            
            // Create booking
            $booking_data = [
                'room_id' => $_POST['room_id'],
                'user_id' => $user_id,
                'check_in_date' => $_POST['check_in_date'],
                'check_out_date' => $_POST['check_out_date'],
                'adults' => $_POST['adults'],
                'children' => $_POST['children'] ?? 0,
                'total_nights' => $cost_calculation['nights'],
                'total_amount' => $cost_calculation['total_amount'],
                'special_requests' => $_POST['special_requests'] ?? ''
            ];
            
            $booking_result = $booking->createBooking($booking_data);
            
            if ($booking_result['success']) {
                echo json_encode([
                    'success' => true,
                    'booking_id' => $booking_result['booking_id'],
                    'booking_reference' => $booking_result['booking_reference'],
                    'total_amount' => $cost_calculation['total_amount'],
                    'total_nights' => $cost_calculation['nights']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
            }
        }
        break;
        
    case 'GET':
        if (isset($_GET['user_id'])) {
            $result = $booking->getUserBookings($_GET['user_id']);
            foreach ($result as &$b) {
                $b['images'] = json_decode($b['images'], true);
            }
            echo json_encode($result);
        } elseif (isset($_GET['booking_reference'])) {
            $result = $booking->getBookingByReference($_GET['booking_reference']);
            if ($result) {
                $result['images'] = json_decode($result['images'], true);
            }
            echo json_encode($result);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>