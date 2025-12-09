<?php
// Test script to verify API functionality
session_start();
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/ordermanager.php';
require_once __DIR__ . '/config/eventmanager.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>EventHub API Test</h1>";

// Test database connection
echo "<h2>Database Connection</h2>";
try {
    $db = new Database();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test creating sample data if needed
echo "<h2>Sample Data</h2>";
$auth = new Auth();
$orderManager = new OrderManager();
$eventManager = new EventManager();

// Check if users exist
$users = $db->fetchAll("SELECT * FROM users");
echo "<p>Users in database: " . count($users) . "</p>";

if (count($users) == 0) {
    echo "<p style='color: orange;'>Creating sample users...</p>";
    try {
        $userId1 = $auth->register('John Doe', 'john@example.com', 'password123', 'user');
        $organizerUserId = $auth->register('Event Organizer', 'organizer@example.com', 'password123', 'organizer');
        echo "<p style='color: green;'>✓ Created sample users</p>";
        
        // Create organizer profile
        $organizerId = $db->insert('organizers', [
            'user_id' => $organizerUserId,
            'name' => 'Tech Events Inc.',
            'bio' => 'We organize the best tech conferences and workshops in the industry.',
            'video_url' => ''
        ]);
        echo "<p style='color: green;'>✓ Created organizer profile</p>";
        
        // Create sample event
        $eventId = $eventManager->createEvent($organizerId, 'Tech Conference 2024', 'Amazing tech conference', 'conference', 1000.00, 'Bangalore, India');
        echo "<p style='color: green;'>✓ Created sample event</p>";
        
        // Create sample order
        $orderId = $orderManager->createOrder($userId1, $eventId, [
            'country' => 'India',
            'state' => 'Karnataka',
            'city' => 'Bangalore',
            'pin_code' => '560001',
            'address' => '123 Main Street'
        ], 'online');
        echo "<p style='color: green;'>✓ Created sample order</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Failed to create sample data: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Sample data already exists</p>";
}

// Test order queries
echo "<h2>Order Queries</h2>";
try {
    $pendingOrders = $orderManager->getOrdersByOrganizer(1);
    echo "<p>Pending orders for organizer 1: " . count($pendingOrders) . "</p>";
    
    if (count($pendingOrders) > 0) {
        echo "<ul>";
        foreach ($pendingOrders as $order) {
            echo "<li>Order #{$order['id']}: {$order['event_title']} - {$order['user_name']} ({$order['organizer_confirmation']})</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Order query failed: " . $e->getMessage() . "</p>";
}

// Test API endpoint
echo "<h2>API Endpoint Test</h2>";
echo "<p>To test the API endpoint:</p>";
echo "<ol>";
echo "<li>Login as organizer: organizer@example.com / password123</li>";
echo "<li>Go to organizer dashboard</li>";
echo "<li>You should see pending orders with Accept/Reject buttons</li>";
echo "<li>Click Accept or Reject to test the API</li>";
echo "</ol>";

echo "<h2>Test Credentials</h2>";
echo "<p><strong>User:</strong> john@example.com / password123</p>";
echo "<p><strong>Organizer:</strong> organizer@example.com / password123</p>";

echo "<p><a href='index.php'>Go to Login Page</a></p>";
?>