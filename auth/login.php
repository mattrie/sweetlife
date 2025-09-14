<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$user = new User();
$result = $user->login($email, $password);

if ($result['success']) {
    $_SESSION['user_id'] = $result['user']['id'];
    $_SESSION['user_name'] = $result['user']['name'];
    $_SESSION['user_email'] = $result['user']['email'];
    $_SESSION['logged_in'] = true;
}

echo json_encode($result);
?>