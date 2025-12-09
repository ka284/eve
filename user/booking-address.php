<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/eventmanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$eventManager = new EventManager();
$user = $auth->getCurrentUser();

// Check if we're coming from the booking detail page (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'] ?? 0;
    $bookingDate = $_POST['booking_date'] ?? '';
    $bookingTime = $_POST['booking_time'] ?? '';

    if (!$eventId || !$bookingDate || !$bookingTime) {
        header('Location: dashboard.php');
        exit;
    }

    $event = $eventManager->getEventById($eventId);
    if (!$event) {
        header('Location: dashboard.php');
        exit;
    }

    // Store booking data in session
    $_SESSION['booking_data'] = [
        'event_id' => $eventId,
        'booking_date' => $bookingDate,
        'booking_time' => $bookingTime,
        'event' => $event
    ];
} else {
    // If not POST, check if we have booking data in session
    $bookingData = $_SESSION['booking_data'] ?? null;
    if (!$bookingData) {
        header('Location: dashboard.php');
        exit;
    }
    
    $eventId = $bookingData['event_id'];
    $bookingDate = $bookingData['booking_date'];
    $bookingTime = $bookingData['booking_time'];
    $event = $bookingData['event'];
}

// Handle address form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['country'])) {
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $pinCode = $_POST['pin_code'] ?? '';
    $address = $_POST['address'] ?? '';

    if (!$country || !$state || !$city || !$pinCode || !$address) {
        $error = "Please fill in all address fields.";
    } else {
        // Store address data in session
        $_SESSION['booking_data']['address'] = [
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'pin_code' => $pinCode,
            'address' => $address
        ];
        
        // Redirect to payment page
        header('Location: booking-payment.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Confirmation - EventHub</title>
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
                <h1>Confirm Your Address</h1>
                <a href="booking-detail.php?event_id=<?php echo $eventId; ?>" class="btn btn-outline">← Back to Event</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Booking Summary -->
                <div>
                    <div class="card">
                        <h3>Booking Summary</h3>
                        
                        <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                            <p><strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($bookingDate)); ?></p>
                            <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($bookingTime)); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <hr style="margin: 1rem 0;">
                            <p><strong>Total Amount:</strong> <span style="color: #059669; font-size: 1.2rem;">₹<?php echo number_format($event['price'], 2); ?></span></p>
                        </div>
                        
                        <h4>Next Steps</h4>
                        <ol style="margin-left: 1.5rem; margin-top: 1rem;">
                            <li>Confirm your address details</li>
                            <li>Choose your payment method</li>
                            <li>Complete the booking</li>
                            <li>Wait for organizer confirmation</li>
                        </ol>
                    </div>
                </div>

                <!-- Address Form -->
                <div>
                    <div class="card">
                        <h3>Address Details</h3>
                        
                        <form method="POST" action="booking-payment.php">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="India" selected>India</option>
                                    <option value="USA">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" placeholder="Enter your state" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="Enter your city" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="pin_code">PIN Code</label>
                                <input type="text" id="pin_code" name="pin_code" placeholder="Enter PIN code" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Full Address</label>
                                <textarea id="address" name="address" rows="3" placeholder="Enter your complete address" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Continue to Payment →</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>