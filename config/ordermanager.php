<?php
require_once __DIR__ . '/../config/database.php';

class OrderManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createOrder($userId, $eventId, $addressData = [], $paymentMethod = 'cod') {
        $orderData = [
            'user_id' => $userId,
            'event_id' => $eventId,
            'status' => 'pending',
            'payment_status' => 'pending',
            'organizer_confirmation' => 'pending',
            'payment_method' => $paymentMethod
        ];
        
        if (!empty($addressData)) {
            $orderData = array_merge($orderData, [
                'country' => $addressData['country'] ?? null,
                'state' => $addressData['state'] ?? null,
                'city' => $addressData['city'] ?? null,
                'pin_code' => $addressData['pin_code'] ?? null,
                'address' => $addressData['address'] ?? null
            ]);
        }
        
        return $this->db->insert('orders', $orderData);
    }
    
    public function getOrdersByUser($userId) {
        $sql = "SELECT o.*, e.title as event_title, e.price as event_price, 
                       o2.name as organizer_name 
                FROM orders o 
                JOIN events e ON o.event_id = e.id 
                JOIN organizers o2 ON e.organizer_id = o2.id 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    public function getOrdersByOrganizer($organizerId) {
        $sql = "SELECT o.*, e.title as event_title, e.price as event_price, 
                       u.name as user_name, u.email as user_email 
                FROM orders o 
                JOIN events e ON o.event_id = e.id 
                JOIN users u ON o.user_id = u.id 
                JOIN organizers o2 ON e.organizer_id = o2.id 
                WHERE o2.id = ? AND o.organizer_confirmation = 'pending'
                ORDER BY o.created_at DESC";
        return $this->db->fetchAll($sql, [$organizerId]);
    }
    
    public function updateOrderStatus($orderId, $status, $confirmationStatus = null) {
        $data = ['status' => $status];
        
        if ($confirmationStatus) {
            $data['organizer_confirmation'] = $confirmationStatus;
        }
        
        return $this->db->update('orders', $data, 'id = ?', [$orderId]);
    }
    
    public function getOrderById($orderId) {
        $sql = "SELECT o.*, e.title as event_title, e.price as event_price, 
                       e.location as event_location, e.type as event_type,
                       o2.name as organizer_name, o2.bio as organizer_bio,
                       u.name as user_name, u.email as user_email 
                FROM orders o 
                JOIN events e ON o.event_id = e.id 
                JOIN organizers o2 ON e.organizer_id = o2.id 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = ?";
        return $this->db->fetch($sql, [$orderId]);
    }
}
?>