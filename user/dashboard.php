<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/eventmanager.php';
require_once __DIR__ . '/../config/ordermanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$eventManager = new EventManager();
$orderManager = new OrderManager();

$user = $auth->getCurrentUser();

// Handle search and filters
$filters = [];
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $filters['type'] = $_GET['type'];
}
if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
    $filters['min_price'] = (float)$_GET['min_price'];
}
if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $filters['max_price'] = (float)$_GET['max_price'];
}
if (isset($_GET['organizer']) && !empty($_GET['organizer'])) {
    $filters['organizer'] = $_GET['organizer'];
}

$events = $eventManager->getEvents($filters);
$userOrders = $orderManager->getOrdersByUser($user['id']);

// Get dashboard statistics
$totalEvents = count($events);
$availableEvents = count(array_filter($events, function($event) {
    return $event['price'] > 0;
}));
$totalOrganizers = count(array_unique(array_column($events, 'organizer_id')));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard - EventHub</title>
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
                    <a href="orders.php">My Orders</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container dashboard">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div>
                <h1>Event Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
            </div>
            <div style="position: relative;">
                <input type="text" id="search-input" placeholder="Search events..." style="padding: 0.8rem 1.5rem; border: 2px solid #e5e7eb; border-radius: 25px; width: 300px;">
                <div id="search-results" class="search-results"></div>
            </div>
        </div>

        <!-- Dashboard Statistics -->
        <div class="dashboard-stats">
            <div class="stat-card blue">
                <div class="stat-value"><?php echo $totalEvents; ?></div>
                <div class="stat-label">Total Events</div>
            </div>
            <div class="stat-card green">
                <div class="stat-value"><?php echo $availableEvents; ?></div>
                <div class="stat-label">Available Now</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-value"><?php echo $totalOrganizers; ?></div>
                <div class="stat-label">Organizers</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-value">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem;">
            <!-- Main Content -->
            <div>
                <h2>Available Events</h2>
                <div class="events-grid">
                    <?php if (empty($events)): ?>
                        <div class="card">
                            <p>No events found matching your criteria.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <div class="event-card-header">
                                    <span style="background: rgba(255,255,255,0.2); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        <?php echo htmlspecialchars(strtoupper($event['type'])); ?>
                                    </span>
                                </div>
                                <div class="event-card-body">
                                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <div class="organizer">by <?php echo htmlspecialchars($event['organizer_name']); ?></div>
                                    <div class="price">₹<?php echo number_format($event['price'], 2); ?></div>
                                    <div class="location">📍 <?php echo htmlspecialchars($event['location']); ?></div>
                                    <button class="btn btn-primary book-event" data-event-id="<?php echo $event['id']; ?>" style="width: 100%;">Book Now</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <h3>Filters</h3>
                <form id="filter-form" method="GET" action="dashboard.php">
                    <div class="filter-group">
                        <label for="event-type">Event Type</label>
                        <select id="event-type" name="type">
                            <option value="">All Types</option>
                            <option value="conference" <?php echo isset($_GET['type']) && $_GET['type'] == 'conference' ? 'selected' : ''; ?>>Conference</option>
                            <option value="festival" <?php echo isset($_GET['type']) && $_GET['type'] == 'festival' ? 'selected' : ''; ?>>Festival</option>
                            <option value="summit" <?php echo isset($_GET['type']) && $_GET['type'] == 'summit' ? 'selected' : ''; ?>>Summit</option>
                            <option value="workshop" <?php echo isset($_GET['type']) && $_GET['type'] == 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="min-price">Min Price (₹)</label>
                        <input type="number" id="min-price" name="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
                    </div>

                    <div class="filter-group">
                        <label for="max-price">Max Price (₹)</label>
                        <input type="number" id="max-price" name="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                    </div>

                    <div class="filter-group">
                        <label for="organizer">Organizer</label>
                        <input type="text" id="organizer" name="organizer" value="<?php echo isset($_GET['organizer']) ? $_GET['organizer'] : ''; ?>">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">Apply Filters</button>
                    <button type="button" id="clear-filters" class="btn btn-outline" style="width: 100%;">Clear Filters</button>
                </form>

                <hr style="margin: 2rem 0;">

                <a href="orders.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 1rem;">View My Orders</a>
                
                <a href="../logout.php" class="btn btn-danger" style="width: 100%;">Logout</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>