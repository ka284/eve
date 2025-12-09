<?php
session_start();
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p>You're now logged in to EventHub. Discover amazing events and create unforgettable experiences.</p>
        </div>
        
        <div class="card">
            <h3>What would you like to do today?</h3>
            <div class="features" style="margin-top: 2rem;">
                <div class="feature" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3>📅 Discover Events</h3>
                    <p>Browse through hundreds of exciting events</p>
                </div>
                <div class="feature" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <h3>🎫 Book Tickets</h3>
                    <p>Reserve your spot at upcoming events</p>
                </div>
                <div class="feature" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                    <h3>⭐ Manage Orders</h3>
                    <p>Track your bookings and order status</p>
                </div>
            </div>
            
            <div style="margin-top: 3rem; text-align: left;">
                <a href="dashboard.php" class="btn btn-primary" style="display: inline-block;">Continue to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>