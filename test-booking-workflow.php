<?php
// Test script to verify the complete booking workflow
session_start();
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/ordermanager.php';
require_once __DIR__ . '/config/eventmanager.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>EventHub Booking Workflow Test</h1>";

// Initialize
$auth = new Auth();
$db = new Database();
$eventManager = new EventManager();
$orderManager = new OrderManager();

echo "<h2>1. Setting up test data</h2>";

// Clean up existing test data
echo "<p>Cleaning up existing test data...</p>";
$db->query("DELETE FROM orders WHERE user_id IN (SELECT id FROM users WHERE email LIKE '%@test.com')");
$db->query("DELETE FROM events WHERE title LIKE 'Test Event%'");
$db->query("DELETE FROM organizers WHERE name LIKE 'Test Organizer%'");
$db->query("DELETE FROM users WHERE email LIKE '%@test.com'");

// Create test user
echo "<p>Creating test user...</p>";
$testUserId = $auth->register('Test User', 'user@test.com', 'password123', 'user');
echo "<p style='color: green;'>✓ Created test user: user@test.com</p>";

// Create test organizer
echo "<p>Creating test organizer...</p>";
$testOrganizerUserId = $auth->register('Test Organizer', 'organizer@test.com', 'password123', 'organizer');
echo "<p style='color: green;'>✓ Created test organizer user: organizer@test.com</p>";

// Create organizer profile
$testOrganizerId = $db->insert('organizers', [
    'user_id' => $testOrganizerUserId,
    'name' => 'Test Organizer Company',
    'bio' => 'We organize test events for testing purposes.',
    'video_url' => ''
]);
echo "<p style='color: green;'>✓ Created test organizer profile (ID: $testOrganizerId)</p>";

// Create test event
echo "<p>Creating test event...</p>";
$testEventId = $eventManager->createEvent($testOrganizerId, 'Test Event 2024', 'This is a test event for workflow testing', 'conference', 500.00, 'Test City');
echo "<p style='color: green;'>✓ Created test event (ID: $testEventId)</p>";

echo "<h2>2. Testing Order Creation</h2>";

// Test order creation
echo "<p>Creating test order...</p>";
$testOrderId = $orderManager->createOrder($testUserId, $testEventId, [
    'country' => 'Test Country',
    'state' => 'Test State',
    'city' => 'Test City',
    'pin_code' => '123456',
    'address' => '123 Test Street'
], 'online');

if ($testOrderId) {
    echo "<p style='color: green;'>✓ Created test order (ID: $testOrderId)</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to create test order</p>";
}

echo "<h2>3. Testing Order Retrieval</h2>";

// Test getOrdersByUser
echo "<p>Testing getOrdersByUser...</p>";
$userOrders = $orderManager->getOrdersByUser($testUserId);
echo "<p>Orders for test user: " . count($userOrders) . "</p>";
if (count($userOrders) > 0) {
    $order = $userOrders[0];
    echo "<div style='margin-left: 20px; background: #f0f0f0; padding: 10px;'>";
    echo "Order ID: {$order['id']}<br>";
    echo "Event: {$order['event_title']}<br>";
    echo "Status: {$order['status']}<br>";
    echo "Confirmation: {$order['organizer_confirmation']}<br>";
    echo "Amount: ₹{$order['event_price']}<br>";
    echo "</div>";
}

// Test getOrdersByOrganizer
echo "<p>Testing getOrdersByOrganizer...</p>";
$organizerOrders = $orderManager->getOrdersByOrganizer($testOrganizerId);
echo "<p>Pending orders for test organizer: " . count($organizerOrders) . "</p>";
if (count($organizerOrders) > 0) {
    $order = $organizerOrders[0];
    echo "<div style='margin-left: 20px; background: #f0f0f0; padding: 10px;'>";
    echo "Order ID: {$order['id']}<br>";
    echo "Event: {$order['event_title']}<br>";
    echo "Customer: {$order['user_name']} ({$order['user_email']})<br>";
    echo "Confirmation: {$order['organizer_confirmation']}<br>";
    echo "Amount: ₹{$order['event_price']}<br>";
    echo "</div>";
}

echo "<h2>4. Testing Order Status Updates</h2>";

// Test order status update
echo "<p>Testing order status update (Accept)...</p>";
$updateResult = $orderManager->updateOrderStatus($testOrderId, 'confirmed', 'confirmed');
if ($updateResult > 0) {
    echo "<p style='color: green;'>✓ Order status updated successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to update order status</p>";
}

// Verify the update
echo "<p>Verifying order status update...</p>";
$updatedOrder = $orderManager->getOrderById($testOrderId);
if ($updatedOrder) {
    echo "<div style='margin-left: 20px; background: #f0f0f0; padding: 10px;'>";
    echo "Order ID: {$updatedOrder['id']}<br>";
    echo "Status: {$updatedOrder['status']}<br>";
    echo "Confirmation: {$updatedOrder['organizer_confirmation']}<br>";
    echo "</div>";
    
    if ($updatedOrder['status'] === 'confirmed' && $updatedOrder['organizer_confirmation'] === 'confirmed') {
        echo "<p style='color: green;'>✓ Order status update verified</p>";
    } else {
        echo "<p style='color: red;'>✗ Order status update not verified</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to retrieve updated order</p>";
}

echo "<h2>5. Testing Database State</h2>";

// Show final database state
echo "<p>Final database state:</p>";
$allOrders = $db->fetchAll("SELECT * FROM orders");
echo "<p>Total orders in database: " . count($allOrders) . "</p>";

foreach ($allOrders as $order) {
    echo "<div style='margin-left: 20px; background: #e8f5e8; padding: 10px; margin-bottom: 5px;'>";
    echo "Order #{$order['id']}: User {$order['user_id']} → Event {$order['event_id']} → Status: {$order['status']} / {$order['organizer_confirmation']}";
    echo "</div>";
}

echo "<h2>6. Test Summary</h2>";

// Test summary
$tests = [
    'User Creation' => $testUserId > 0,
    'Organizer Creation' => $testOrganizerId > 0,
    'Event Creation' => $testEventId > 0,
    'Order Creation' => $testOrderId > 0,
    'User Order Retrieval' => count($userOrders) > 0,
    'Organizer Order Retrieval' => count($organizerOrders) > 0,
    'Order Status Update' => $updateResult > 0,
    'Order Verification' => $updatedOrder && $updatedOrder['status'] === 'confirmed'
];

$passedTests = 0;
$totalTests = count($tests);

foreach ($tests as $testName => $passed) {
    if ($passed) {
        echo "<p style='color: green;'>✓ $testName: PASSED</p>";
        $passedTests++;
    } else {
        echo "<p style='color: red;'>✗ $testName: FAILED</p>";
    }
}

echo "<h3>Overall Result: $passedTests/$totalTests tests passed</h3>";
if ($passedTests === $totalTests) {
    echo "<p style='color: green; font-weight: bold;'>🎉 All tests passed! The booking workflow should work correctly.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>⚠️  Some tests failed. Please check the implementation.</p>";
}

echo "<h2>7. Manual Test Instructions</h2>";
echo "<p>To test the booking workflow manually:</p>";
echo "<ol>";
echo "<li>Go to the login page: <a href='index.php'>Login</a></li>";
echo "<li>Login as test user: user@test.com / password123</li>";
echo "<li>You should see 'Test Event 2024' in the events list</li>";
echo "<li>Click 'Book Now' on the test event</li>";
echo "<li>Fill in the booking details and proceed through the 3-step process</li>";
echo "<li>After completing the booking, check 'My Orders' to see the new order</li>";
echo "<li>Logout and login as test organizer: organizer@test.com / password123</li>";
echo "<li>You should see the pending order in the organizer dashboard</li>";
echo "<li>Click 'Accept' or 'Reject' to test the order management</li>";
echo "<li>Verify that the order status updates correctly</li>";
echo "</ol>";

echo "<p><a href='test-booking-workflow.php'>Run Test Again</a> | <a href='index.php'>Go to Login Page</a></p>";
?>