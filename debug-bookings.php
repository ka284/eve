<?php
// Debug script to test booking functionality
session_start();
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/ordermanager.php';
require_once __DIR__ . '/config/eventmanager.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>EventHub Booking Debug</h1>";

// Test database connection
echo "<h2>1. Database Connection</h2>";
try {
    $db = new Database();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check existing data
echo "<h2>2. Existing Data</h2>";

// Check users
$users = $db->fetchAll("SELECT * FROM users");
echo "<p>Users: " . count($users) . "</p>";
foreach ($users as $user) {
    echo "<div style='margin-left: 20px;'>";
    echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}<br>";
    echo "</div>";
}

// Check organizers
$organizers = $db->fetchAll("SELECT * FROM organizers");
echo "<p>Organizers: " . count($organizers) . "</p>";
foreach ($organizers as $organizer) {
    echo "<div style='margin-left: 20px;'>";
    echo "ID: {$organizer['id']}, User ID: {$organizer['user_id']}, Name: {$organizer['name']}<br>";
    echo "</div>";
}

// Check events
$events = $db->fetchAll("SELECT * FROM events");
echo "<p>Events: " . count($events) . "</p>";
foreach ($events as $event) {
    echo "<div style='margin-left: 20px;'>";
    echo "ID: {$event['id']}, Title: {$event['title']}, Organizer ID: {$event['organizer_id']}, Price: {$event['price']}<br>";
    echo "</div>";
}

// Check orders
$orders = $db->fetchAll("SELECT * FROM orders");
echo "<p>Orders: " . count($orders) . "</p>";
foreach ($orders as $order) {
    echo "<div style='margin-left: 20px;'>";
    echo "ID: {$order['id']}, User ID: {$order['user_id']}, Event ID: {$order['event_id']}, Status: {$order['status']}, Confirmation: {$order['organizer_confirmation']}<br>";
    echo "</div>";
}

// Test creating sample data if needed
echo "<h2>3. Creating Sample Data (if needed)</h2>";

if (count($users) < 2) {
    echo "<p style='color: orange;'>Creating sample users...</p>";
    $auth = new Auth();
    
    try {
        // Create sample user
        if (count($users) == 0) {
            $userId = $auth->register('John Doe', 'john@example.com', 'password123', 'user');
            echo "<p style='color: green;'>✓ Created user: John Doe (ID: $userId)</p>";
        }
        
        // Create sample organizer
        $organizerUserId = $auth->register('Event Organizer', 'organizer@example.com', 'password123', 'organizer');
        echo "<p style='color: green;'>✓ Created organizer user: Event Organizer (ID: $organizerUserId)</p>";
        
        // Create organizer profile
        $organizerId = $db->insert('organizers', [
            'user_id' => $organizerUserId,
            'name' => 'Tech Events Inc.',
            'bio' => 'We organize the best tech conferences and workshops.',
            'video_url' => ''
        ]);
        echo "<p style='color: green;'>✓ Created organizer profile (ID: $organizerId)</p>";
        
        // Create sample event
        $eventManager = new EventManager();
        $eventId = $eventManager->createEvent($organizerId, 'Tech Conference 2024', 'Amazing tech conference', 'conference', 1000.00, 'Bangalore, India');
        echo "<p style='color: green;'>✓ Created sample event (ID: $eventId)</p>";
        
        // Test creating a sample order
        $orderManager = new OrderManager();
        $sampleUserId = $userId ?? 1;
        $orderId = $orderManager->createOrder($sampleUserId, $eventId, [
            'country' => 'India',
            'state' => 'Karnataka',
            'city' => 'Bangalore',
            'pin_code' => '560001',
            'address' => '123 Main Street'
        ], 'online');
        
        if ($orderId) {
            echo "<p style='color: green;'>✓ Created sample order (ID: $orderId)</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create sample order</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error creating sample data: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Sample data already exists</p>";
}

// Test order queries
echo "<h2>4. Testing Order Queries</h2>";
try {
    $orderManager = new OrderManager();
    
    // Test getOrdersByUser
    if (count($users) > 0) {
        $userId = $users[0]['id'];
        $userOrders = $orderManager->getOrdersByUser($userId);
        echo "<p>Orders for user $userId: " . count($userOrders) . "</p>";
        foreach ($userOrders as $order) {
            echo "<div style='margin-left: 20px;'>";
            echo "Order #{$order['id']}: {$order['event_title']} - {$order['status']} ({$order['organizer_confirmation']})<br>";
            echo "</div>";
        }
    }
    
    // Test getOrdersByOrganizer
    if (count($organizers) > 0) {
        $organizerId = $organizers[0]['id'];
        $organizerOrders = $orderManager->getOrdersByOrganizer($organizerId);
        echo "<p>Pending orders for organizer $organizerId: " . count($organizerOrders) . "</p>";
        foreach ($organizerOrders as $order) {
            echo "<div style='margin-left: 20px;'>";
            echo "Order #{$order['id']}: {$order['event_title']} by {$order['user_name']} - {$order['organizer_confirmation']}<br>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error testing order queries: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Test Instructions</h2>";
echo "<ol>";
echo "<li>Visit this page to see the current state of the database</li>";
echo "<li>Test the booking process by:</li>";
echo "<ul>";
echo "<li>Login as user: john@example.com / password123</li>";
echo "<li>Browse events and click 'Book Now'</li>";
echo "<li>Complete the 3-step booking process</li>";
echo "<li>Check if the order appears in 'My Orders'</li>";
echo "<li>Login as organizer: organizer@example.com / password123</li>";
echo "<li>Check if the order appears in pending orders</li>";
echo "<li>Test Accept/Reject buttons</li>";
echo "</ul>";
echo "<li>Refresh this debug page to see database changes</li>";
echo "</ol>";

echo "<p><a href='index.php'>Go to Login Page</a></p>";
echo "<p><a href='debug-bookings.php'>Refresh Debug Page</a></p>";
?>