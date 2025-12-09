<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('organizer');

$user = $auth->getCurrentUser();
$db = new Database();
$organizer = $db->fetch("SELECT * FROM organizers WHERE user_id = ?", [$user['id']]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Profile - EventHub</title>
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
                <h1>Organizer Profile</h1>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                <!-- Profile Summary -->
                <div>
                    <div class="card">
                        <div style="text-align: center;">
                            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                <?php echo strtoupper(substr($organizer['name'], 0, 1)); ?>
                            </div>
                            <h3><?php echo htmlspecialchars($organizer['name']); ?></h3>
                            <p style="color: #6b7280;"><?php echo htmlspecialchars($user['email']); ?></p>
                            <span style="background: #e0e7ff; color: #3730a3; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                Organizer
                            </span>
                        </div>
                        
                        <hr style="margin: 2rem 0;">
                        
                        <h4>Quick Actions</h4>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            <a href="dashboard.php" class="btn btn-primary" style="text-align: center;">Manage Events</a>
                            <a href="#" class="btn btn-secondary" style="text-align: center;">View Analytics</a>
                            <a href="../logout.php" class="btn btn-danger" style="text-align: center;">Logout</a>
                        </div>
                    </div>
                </div>

                <!-- Profile Details & Edit Form -->
                <div>
                    <div class="card">
                        <h3>Profile Information</h3>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="profile.php">
                            <div class="form-group">
                                <label for="organizer-name">Organizer Name</label>
                                <input type="text" id="organizer-name" name="name" value="<?php echo htmlspecialchars($organizer['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="organizer-bio">Bio/Description</label>
                                <textarea id="organizer-bio" name="bio" rows="4" placeholder="Tell us about your organization and the types of events you host..."><?php echo htmlspecialchars($organizer['bio']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="organizer-video">Video URL (Optional)</label>
                                <input type="url" id="organizer-video" name="video_url" value="<?php echo htmlspecialchars($organizer['video_url']); ?>" placeholder="https://youtube.com/watch?v=...">
                                <small style="color: #6b7280;">YouTube or Vimeo URL showcasing your work</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: #f9fafb;">
                            </div>
                            
                            <div class="form-group">
                                <label>Member Since</label>
                                <input type="text" value="<?php echo date('F j, Y', strtotime($organizer['created_at'])); ?>" readonly style="background: #f9fafb;">
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>