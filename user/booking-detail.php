<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/eventmanager.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$eventManager = new EventManager();
$user = $auth->getCurrentUser();

$eventId = $_GET['event_id'] ?? 0;
if (!$eventId) {
    header('Location: dashboard.php');
    exit;
}

$event = $eventManager->getEventById($eventId);
if (!$event) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - EventHub</title>
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
                <h1>Event Details</h1>
                <a href="dashboard.php" class="btn btn-outline">← Back to Events</a>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- Event Details -->
                <div>
                    <div class="card">
                        <div class="event-card-header">
                            <h2><?php echo htmlspecialchars($event['title']); ?></h2>
                            <span style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 25px; font-weight: 600;">
                                <?php echo htmlspecialchars(strtoupper($event['type'])); ?>
                            </span>
                        </div>
                        
                        <div style="padding: 2rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                                <div>
                                    <h4>Organizer</h4>
                                    <p><strong><?php echo htmlspecialchars($event['organizer_name']); ?></strong></p>
                                    <?php if (!empty($event['organizer_bio'])): ?>
                                        <p><?php echo htmlspecialchars($event['organizer_bio']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4>Event Info</h4>
                                    <p><strong>Price:</strong> ₹<?php echo number_format($event['price'], 2); ?></p>
                                    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                                    <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($event['type'])); ?></p>
                                </div>
                            </div>
                            
                            <?php if (!empty($event['description'])): ?>
                                <div style="margin-bottom: 2rem;">
                                    <h4>Description</h4>
                                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($event['video_url'])): ?>
                                <div style="margin-bottom: 2rem;">
                                    <h4>Organizer Video</h4>
                                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                        <?php
                                        $videoUrl = $event['video_url'];
                                        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                            // Extract YouTube video ID
                                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $videoUrl, $matches);
                                            $videoId = $matches[1] ?? '';
                                            if ($videoId) {
                                                echo '<iframe src="https://www.youtube.com/embed/' . $videoId . '" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allowfullscreen></iframe>';
                                            }
                                        } else {
                                            echo '<p>Video URL: <a href="' . htmlspecialchars($videoUrl) . '" target="_blank">' . htmlspecialchars($videoUrl) . '</a></p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <div>
                    <div class="card">
                        <h3>Book This Event</h3>
                        
                        <form method="POST" action="booking-address.php">
                            <div class="form-group">
                                <label>Event</label>
                                <input type="text" value="<?php echo htmlspecialchars($event['title']); ?>" readonly style="background: #f9fafb;">
                            </div>
                            
                            <div class="form-group">
                                <label>Price</label>
                                <input type="text" value="₹<?php echo number_format($event['price'], 2); ?>" readonly style="background: #f9fafb;">
                            </div>
                            
                            <div class="form-group">
                                <label for="booking-date">Preferred Date</label>
                                <input type="date" id="booking-date" name="booking_date" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="booking-time">Preferred Time</label>
                                <input type="time" id="booking-time" name="booking_time" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Total Cost</label>
                                <input type="text" value="₹<?php echo number_format($event['price'], 2); ?>" readonly style="background: #f9fafb; font-weight: bold; color: #059669;">
                            </div>
                            
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Next →</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>