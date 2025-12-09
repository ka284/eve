<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/eventmanager.php';
require_once __DIR__ . '/../config/ordermanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('organizer');

$eventManager = new EventManager();
$orderManager = new OrderManager();

$user = $auth->getCurrentUser();

// Get organizer data
$db = new Database();
$organizer = $db->fetch("SELECT * FROM organizers WHERE user_id = ?", [$user['id']]);

// Handle organizer profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $videoUrl = $_POST['video_url'] ?? '';
    
    if (!empty($name)) {
        $db->update('organizers', [
            'name' => $name,
            'bio' => $bio,
            'video_url' => $videoUrl
        ], 'user_id = ?', [$user['id']]);
        
        $success = "Profile updated successfully!";
        $organizer = $db->fetch("SELECT * FROM organizers WHERE user_id = ?", [$user['id']]);
    }
}

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? '';
    $price = $_POST['price'] ?? 0;
    $location = $_POST['location'] ?? '';
    
    if (!empty($title) && !empty($type) && !empty($location)) {
        $eventId = $eventManager->createEvent($organizer['id'], $title, $description, $type, $price, $location);
        if ($eventId) {
            $event_success = "Event created successfully!";
        }
    }
}

// Get organizer events and pending orders
$events = $eventManager->getEventsByOrganizer($organizer['id']);
$pendingOrders = $orderManager->getOrdersByOrganizer($organizer['id']);

// Calculate statistics
$totalEvents = count($events);
$totalValue = array_sum(array_column($events, 'price')) ; // Estimated total value
$pendingOrderCount = count($pendingOrders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="nav">
                <a href="dashboard.php" class="logo">EventHub</a>
                <div class="nav-links">
                    <a href="profile.php">Profile</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <div>
                    <h1>Organizer Dashboard</h1>
                    <p>Welcome, <?php echo htmlspecialchars($organizer['name']); ?>!</p>
                </div>
                <div>
                    <a href="profile.php" class="btn btn-outline">Edit Profile</a>
                    <button class="btn btn-primary" onclick="document.getElementById('createEventModal').style.display='block'">Create New Event</button>
                </div>
            </div>

            <!-- Dashboard Statistics -->
            <div class="dashboard-stats">
                <div class="stat-card blue">
                    <div class="stat-value"><?php echo $totalEvents; ?></div>
                    <div class="stat-label">Total Events</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-value">₹<?php echo number_format($totalValue, 2); ?></div>
                    <div class="stat-label">Total Value</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-value"><?php echo $pendingOrderCount; ?></div>
                    <div class="stat-label">Pending Orders</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
            </div>

            <!-- Tabs for Events and Orders -->
            <div style="margin-bottom: 2rem;">
                <div class="login-tabs">
                    <div class="login-tab active" data-tab="events-tab">Events</div>
                    <div class="login-tab" data-tab="orders-tab">Registration Stats</div>
                </div>
            </div>

            <!-- Events Tab Content -->
            <div id="events-tab" class="login-tab-content active">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                    <!-- Your Events -->
                    <div>
                        <div class="card">
                            <h3>Your Events</h3>
                            
                            <?php if (isset($event_success)): ?>
                                <div class="alert alert-success"><?php echo $event_success; ?></div>
                            <?php endif; ?>
                            
                            <?php if (empty($events)): ?>
                                <div style="text-align: center; padding: 2rem;">
                                    <h4>No Events Yet</h4>
                                    <p>Create your first event to start managing bookings.</p>
                                    <button class="btn btn-primary" onclick="document.getElementById('createEventModal').style.display='block'">Create Event</button>
                                </div>
                            <?php else: ?>
                                <div class="events-grid">
                                    <?php foreach ($events as $event): ?>
                                        <div class="event-card">
                                            <div class="event-card-header">
                                                <span style="background: rgba(255,255,255,0.2); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                                    <?php echo htmlspecialchars(strtoupper($event['type'])); ?>
                                                </span>
                                            </div>
                                            <div class="event-card-body">
                                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                                <div class="price">₹<?php echo number_format($event['price'], 2); ?></div>
                                                <div class="location">📍 <?php echo htmlspecialchars($event['location']); ?></div>
                                                <p style="font-size: 0.9rem; color: #6b7280; margin-bottom: 1rem;">
                                                    <?php echo ($event['order_count'] ?? 0); ?> bookings
                                                </p>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <button class="btn btn-outline" style="flex: 1; font-size: 0.9rem;">Edit</button>
                                                    <button class="btn btn-danger" style="flex: 1; font-size: 0.9rem;">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pending Orders -->
                    <div>
                        <div class="card">
                            <h3>Pending Orders</h3>
                            
                            <?php if (empty($pendingOrders)): ?>
                                <div style="text-align: center; padding: 2rem;">
                                    <h4>No Pending Orders</h4>
                                    <p>All orders have been processed.</p>
                                </div>
                            <?php else: ?>
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <?php foreach ($pendingOrders as $order): ?>
                                        <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #f59e0b;" data-order-row="<?php echo $order['id']; ?>">
                                            <h5><?php echo htmlspecialchars($order['event_title']); ?></h5>
                                            <p style="margin: 0.5rem 0; font-size: 0.9rem;">
                                                <strong>Customer:</strong> <?php echo htmlspecialchars($order['user_name']); ?><br>
                                                <strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?><br>
                                                <strong>Amount:</strong> ₹<?php echo number_format($order['event_price'], 2); ?><br>
                                                <strong>Date:</strong> <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                            </p>
                                            <div class="order-actions">
                                                <button class="btn btn-success accept-order" data-order-id="<?php echo $order['id']; ?>" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Accept</button>
                                                <button class="btn btn-danger reject-order" data-order-id="<?php echo $order['id']; ?>" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Reject</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Tab Content -->
            <div id="orders-tab" class="login-tab-content">
                <div class="card">
                    <h3>Registration Statistics</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                            <h4 style="color: #059669; font-size: 2rem;"><?php echo array_sum(array_column($events, 'order_count')); ?></h4>
                            <p>Total Registrations</p>
                        </div>
                        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                            <h4 style="color: #dc2626; font-size: 2rem;"><?php echo $pendingOrderCount; ?></h4>
                            <p>Pending Approvals</p>
                        </div>
                        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                            <h4 style="color: #7c3aed; font-size: 2rem;">₹<?php echo number_format($totalValue, 2); ?></h4>
                            <p>Potential Revenue</p>
                        </div>
                        <div style="text-align: center; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                            <h4 style="color: #ea580c; font-size: 2rem;"><?php echo $totalEvents > 0 ? round(array_sum(array_column($events, 'order_count')) / $totalEvents, 1) : 0; ?></h4>
                            <p>Avg. Bookings/Event</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div id="createEventModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>Create New Event</h3>
                <button onclick="document.getElementById('createEventModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
            </div>
            
            <form method="POST" action="dashboard.php">
                <div class="form-group">
                    <label for="event-title">Event Title</label>
                    <input type="text" id="event-title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="event-type">Event Type</label>
                    <select id="event-type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="conference">Conference</option>
                        <option value="festival">Festival</option>
                        <option value="summit">Summit</option>
                        <option value="workshop">Workshop</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="event-price">Price (₹)</label>
                    <input type="number" id="event-price" name="price" min="0" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="event-location">Location</label>
                    <input type="text" id="event-location" name="location" required>
                </div>
                
                <div class="form-group">
                    <label for="event-description">Description</label>
                    <textarea id="event-description" name="description" rows="3"></textarea>
                </div>
                
                <button type="submit" name="create_event" class="btn btn-primary" style="width: 100%;">Create Event</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>