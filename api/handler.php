<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/ordermanager.php';

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    $auth = new Auth();
    $auth->logout();
}

// Handle AJAX order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['order_id'])) {
    header('Content-Type: application/json');
    
    $auth = new Auth();
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    $orderManager = new OrderManager();
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'accept') {
        $result = $orderManager->updateOrderStatus($orderId, 'confirmed', 'confirmed');
        echo json_encode(['success' => $result > 0, 'message' => 'Order accepted successfully']);
    } elseif ($action === 'reject') {
        $result = $orderManager->updateOrderStatus($orderId, 'cancelled', 'cancelled');
        echo json_encode(['success' => $result > 0, 'message' => 'Order rejected successfully']);
    }
    exit;
}
?>