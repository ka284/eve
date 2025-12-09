<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/ordermanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$orderManager = new OrderManager();
$user = $auth->getCurrentUser();

$orderId = $_GET['order_id'] ?? 0;
if (!$orderId) {
    header('Location: dashboard.php');
    exit;
}

$order = $orderManager->getOrderById($orderId);
if (!$order || $order['user_id'] != $user['id']) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - EventHub</title>
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
                <h1>Booking Confirmation</h1>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>

            <div style="text-align: center; max-width: 600px; margin: 0 auto;">
                <div class="card">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <div style="width: 80px; height: 80px; background: #10b981; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            ✓
                        </div>
                        <h2>Booking Successful!</h2>
                        <p>Your booking has been submitted and is awaiting organizer confirmation.</p>
                    </div>
                    
                    <div style="background: #f9fafb; padding: 2rem; border-radius: 8px; text-align: left; margin-bottom: 2rem;">
                        <h3>Booking Details</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div>
                                <strong>Order ID:</strong><br>
                                #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                            </div>
                            <div>
                                <strong>Event:</strong><br>
                                <?php echo htmlspecialchars($order['event_title']); ?>
                            </div>
                            <div>
                                <strong>Date:</strong><br>
                                <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                            </div>
                            <div>
                                <strong>Amount:</strong><br>
                                ₹<?php echo number_format($order['event_price'], 2); ?>
                            </div>
                            <div>
                                <strong>Payment Method:</strong><br>
                                <?php echo ucfirst($order['payment_method']); ?>
                            </div>
                            <div>
                                <strong>Status:</strong><br>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <hr style="margin: 1.5rem 0;">
                        
                        <h4>What's Next?</h4>
                        <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
                            <li>Wait for organizer confirmation (usually within 24 hours)</li>
                            <li>You'll receive an email notification when confirmed</li>
                            <li>Download your ticket from the "My Orders" section</li>
                            <li>Attend the event and enjoy!</li>
                        </ol>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <a href="orders.php" class="btn btn-primary">View My Orders</a>
                        <a href="dashboard.php" class="btn btn-secondary">Browse More Events</a>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 2rem;">
                    <h3>Need Help?</h3>
                    <p>If you have any questions about your booking, feel free to contact our support team.</p>
                    <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
                        <a href="#" class="btn btn-outline">Contact Support</a>
                        <a href="#" class="btn btn-outline">FAQ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>