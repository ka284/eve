<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/ordermanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$orderManager = new OrderManager();
$user = $auth->getCurrentUser();

// Get booking data from session
$bookingData = $_SESSION['booking_data'] ?? null;
if (!$bookingData) {
    header('Location: dashboard.php');
    exit;
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];
    
    // Create the order
    $orderId = $orderManager->createOrder(
        $user['id'],
        $bookingData['event_id'],
        $bookingData['address'] ?? [],
        $paymentMethod
    );
    
    if ($orderId) {
        // Clear booking data from session
        unset($_SESSION['booking_data']);
        
        // Redirect to confirmation page
        header('Location: booking-confirmation.php?order_id=' . $orderId);
        exit;
    } else {
        $error = "Failed to create order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - EventHub</title>
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
                <h1>Complete Your Payment</h1>
                <a href="booking-address.php" class="btn btn-outline">← Back to Address</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Order Summary -->
                <div>
                    <div class="card">
                        <h3>Order Summary</h3>
                        
                        <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <h4><?php echo htmlspecialchars($bookingData['event']['title']); ?></h4>
                            <p><strong>Organizer:</strong> <?php echo htmlspecialchars($bookingData['event']['organizer_name']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($bookingData['booking_date'])); ?></p>
                            <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($bookingData['booking_time'])); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($bookingData['event']['location']); ?></p>
                            
                            <hr style="margin: 1rem 0;">
                            
                            <h5>Address</h5>
                            <?php if (isset($bookingData['address'])): ?>
                                <p><?php echo htmlspecialchars($bookingData['address']['address']); ?></p>
                                <p><?php echo htmlspecialchars($bookingData['address']['city']) . ', ' . htmlspecialchars($bookingData['address']['state']) . ' - ' . htmlspecialchars($bookingData['address']['pin_code']); ?></p>
                                <p><?php echo htmlspecialchars($bookingData['address']['country']); ?></p>
                            <?php else: ?>
                                <p>Address information not available</p>
                            <?php endif; ?>
                            
                            <hr style="margin: 1rem 0;">
                            
                            <p><strong>Total Amount:</strong> <span style="color: #059669; font-size: 1.5rem; font-weight: bold;">₹<?php echo number_format($bookingData['event']['price'], 2); ?></span></p>
                        </div>
                        
                        <div style="background: #fef3c7; padding: 1rem; border-radius: 8px; border-left: 4px solid #f59e0b;">
                            <h5 style="color: #92400e; margin-bottom: 0.5rem;">Important Note</h5>
                            <p style="color: #92400e; font-size: 0.9rem;">Your booking will be confirmed once the organizer approves it. You'll receive a notification via email.</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Options -->
                <div>
                    <div class="card">
                        <h3>Select Payment Method</h3>
                        
                        <form method="POST" action="booking-payment.php">
                            <div class="form-group">
                                <label>Choose Payment Method</label>
                                <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                                    <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                        <input type="radio" name="payment_method" value="online" style="margin-right: 1rem;" required>
                                        <div>
                                            <strong>Online Payment</strong>
                                            <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Pay via Credit/Debit Card, UPI, Net Banking</p>
                                        </div>
                                    </label>
                                    
                                    <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                        <input type="radio" name="payment_method" value="cod" style="margin-right: 1rem;" required>
                                        <div>
                                            <strong>Cash on Delivery</strong>
                                            <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Pay when you attend the event</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div style="margin-top: 2rem;">
                                <h4>Terms & Conditions</h4>
                                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0; font-size: 0.9rem;">
                                    <ul style="margin-left: 1.5rem;">
                                        <li>Booking is subject to organizer confirmation</li>
                                        <li>Cancellation policy applies as per organizer terms</li>
                                        <li>Please arrive 15 minutes before the event time</li>
                                        <li>Bring valid ID proof for verification</li>
                                    </ul>
                                </div>
                                
                                <label style="display: flex; align-items: center; margin-bottom: 1rem;">
                                    <input type="checkbox" required style="margin-right: 0.5rem;">
                                    <span style="font-size: 0.9rem;">I agree to the terms and conditions</span>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-success" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                                Complete Booking - ₹<?php echo number_format($bookingData['event']['price'], 2); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>