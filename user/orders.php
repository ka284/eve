<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/ordermanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$orderManager = new OrderManager();
$user = $auth->getCurrentUser();

$orders = $orderManager->getOrdersByUser($user['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EventHub</title>
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

    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <h1>My Orders</h1>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>

            <div class="card">
                <?php if (empty($orders)): ?>
                    <div style="text-align: center; padding: 3rem;">
                        <h3>No Orders Found</h3>
                        <p>You haven't made any bookings yet. Start exploring events and book your first one!</p>
                        <a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Events</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Event Name</th>
                                    <th>Organizer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Confirmation</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr data-order-row="<?php echo $order['id']; ?>">
                                        <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($order['event_title']); ?></td>
                                        <td><?php echo htmlspecialchars($order['organizer_name']); ?></td>
                                        <td>₹<?php echo number_format($order['event_price'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['organizer_confirmation']; ?>">
                                                <?php echo ucfirst($order['organizer_confirmation']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                        <td><?php echo ucfirst($order['payment_method'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Order Statistics -->
            <div class="dashboard-stats" style="margin-top: 2rem;">
                <div class="stat-card blue">
                    <div class="stat-value"><?php echo count($orders); ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-value"><?php echo count(array_filter($orders, function($order) { return $order['status'] === 'confirmed'; })); ?></div>
                    <div class="stat-label">Confirmed</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-value"><?php echo count(array_filter($orders, function($order) { return $order['organizer_confirmation'] === 'pending'; })); ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-value">₹<?php echo number_format(array_sum(array_column($orders, 'event_price')), 2); ?></div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>