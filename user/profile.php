<?php
session_start();
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireRole('user');

$user = $auth->getCurrentUser();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (!empty($name) && !empty($email)) {
        $db = new Database();
        $db->update('users', [
            'name' => $name,
            'email' => $email
        ], 'id = ?', [$user['id']]);
        
        // Update session
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        $success = "Profile updated successfully!";
        $user = $auth->getCurrentUser(); // Refresh user data
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - EventHub</title>
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
                <h1>User Profile</h1>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                <!-- Profile Summary -->
                <div>
                    <div class="card">
                        <div style="text-align: center;">
                            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p style="color: #6b7280;"><?php echo htmlspecialchars($user['email']); ?></p>
                            <span style="background: #e0e7ff; color: #3730a3; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </div>
                        
                        <hr style="margin: 2rem 0;">
                        
                        <h4>Quick Actions</h4>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            <a href="dashboard.php" class="btn btn-primary" style="text-align: center;">Browse Events</a>
                            <a href="orders.php" class="btn btn-secondary" style="text-align: center;">My Orders</a>
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
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Account Type</label>
                                <input type="text" value="<?php echo ucfirst(htmlspecialchars($user['role'])); ?>" readonly style="background: #f9fafb;">
                            </div>
                            
                            <div class="form-group">
                                <label>Member Since</label>
                                <input type="text" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" readonly style="background: #f9fafb;">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>