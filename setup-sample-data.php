<?php
// Sample data insertion script for testing
// Run this file once to populate the database with sample data

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

echo "Setting up sample data for EventHub...\n";

$db = new Database();
$auth = new Auth();

// Clear existing data (optional)
echo "Clearing existing data...\n";
$db->query("DELETE FROM orders");
$db->query("DELETE FROM events");
$db->query("DELETE FROM organizers");
$db->query("DELETE FROM users");

// Create sample users
echo "Creating sample users...\n";

// Sample user
$userId1 = $auth->register('John Doe', 'john@example.com', 'password123', 'user');
echo "Created user: John Doe (ID: $userId1)\n";

// Sample organizer
$organizerUserId = $auth->register('Event Organizer', 'organizer@example.com', 'password123', 'organizer');
echo "Created organizer: Event Organizer (ID: $organizerUserId)\n";

// Create organizer profile
$organizerId = $db->insert('organizers', [
    'user_id' => $organizerUserId,
    'name' => 'Tech Events Inc.',
    'bio' => 'We organize the best tech conferences and workshops in the industry. Join us for amazing learning experiences!',
    'video_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ'
]);
echo "Created organizer profile (ID: $organizerId)\n";

// Create sample events
echo "Creating sample events...\n";

$events = [
    [
        'organizer_id' => $organizerId,
        'title' => 'Tech Conference 2024',
        'description' => 'Join us for the biggest tech conference of the year featuring industry leaders and innovative technologies.',
        'type' => 'conference',
        'price' => 1000.00,
        'location' => 'Bangalore, India'
    ],
    [
        'organizer_id' => $organizerId,
        'title' => 'Music Festival Summer',
        'description' => 'Experience the best live music performances from top artists across various genres.',
        'type' => 'festival',
        'price' => 1500.00,
        'location' => 'Mumbai, India'
    ],
    [
        'organizer_id' => $organizerId,
        'title' => 'Business Leadership Summit',
        'description' => 'Connect with business leaders and learn strategies for success in today\'s competitive market.',
        'type' => 'summit',
        'price' => 2500.00,
        'location' => 'Delhi, India'
    ],
    [
        'organizer_id' => $organizerId,
        'title' => 'Web Development Workshop',
        'description' => 'Learn modern web development techniques and best practices from industry experts.',
        'type' => 'workshop',
        'price' => 500.00,
        'location' => 'Hyderabad, India'
    ]
];

foreach ($events as $event) {
    $eventId = $db->insert('events', $event);
    echo "Created event: {$event['title']} (ID: $eventId)\n";
}

// Create sample orders
echo "Creating sample orders...\n";

$orderManager = new OrderManager();

// Sample order data
$orders = [
    [
        'user_id' => $userId1,
        'event_id' => 1,
        'country' => 'India',
        'state' => 'Karnataka',
        'city' => 'Bangalore',
        'pin_code' => '560001',
        'address' => '123 Main Street, Bangalore',
        'payment_method' => 'online'
    ],
    [
        'user_id' => $userId1,
        'event_id' => 2,
        'country' => 'India',
        'state' => 'Maharashtra',
        'city' => 'Mumbai',
        'pin_code' => '400001',
        'address' => '456 Park Avenue, Mumbai',
        'payment_method' => 'cod'
    ]
];

foreach ($orders as $order) {
    $orderId = $orderManager->createOrder($order['user_id'], $order['event_id'], $order, $order['payment_method']);
    echo "Created order (ID: $orderId)\n";
}

echo "\nSample data setup complete!\n";
echo "You can now login with:\n";
echo "User: john@example.com / password123\n";
echo "Organizer: organizer@example.com / password123\n";
?>