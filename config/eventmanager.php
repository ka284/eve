<?php
require_once __DIR__ . '/../config/database.php';

class EventManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createEvent($organizerId, $title, $description, $type, $price, $location) {
        return $this->db->insert('events', [
            'organizer_id' => $organizerId,
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'price' => $price,
            'location' => $location
        ]);
    }
    
    public function getEvents($filters = []) {
        $sql = "SELECT e.*, o.name as organizer_name, o.bio as organizer_bio 
                FROM events e 
                JOIN organizers o ON e.organizer_id = o.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['type'])) {
            $sql .= " AND e.type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND e.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND e.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['organizer'])) {
            $sql .= " AND o.name LIKE ?";
            $params[] = '%' . $filters['organizer'] . '%';
        }
        
        $sql .= " ORDER BY e.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getEventById($eventId) {
        $sql = "SELECT e.*, o.name as organizer_name, o.bio as organizer_bio, o.video_url 
                FROM events e 
                JOIN organizers o ON e.organizer_id = o.id 
                WHERE e.id = ?";
        return $this->db->fetch($sql, [$eventId]);
    }
    
    public function getEventsByOrganizer($organizerId) {
        $sql = "SELECT e.*, COUNT(o.id) as order_count 
                FROM events e 
                LEFT JOIN orders o ON e.id = o.event_id 
                WHERE e.organizer_id = ? 
                GROUP BY e.id 
                ORDER BY e.created_at DESC";
        return $this->db->fetchAll($sql, [$organizerId]);
    }
    
    public function updateEvent($eventId, $data) {
        return $this->db->update('events', $data, 'id = ?', [$eventId]);
    }
    
    public function deleteEvent($eventId) {
        return $this->db->delete('events', 'id = ?', [$eventId]);
    }
}
?>