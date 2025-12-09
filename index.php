<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$auth = new Auth();

// If user is already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    if ($_SESSION['user_role'] === 'user') {
        header('Location: user/dashboard.php');
    } else {
        header('Location: organizer/dashboard.php');
    }
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    if ($auth->login($email, $password, $role)) {
        if ($role === 'user') {
            header('Location: user/dashboard.php');
        } else {
            header('Location: organizer/dashboard.php');
        }
        exit;
    } else {
        $error = "Invalid email or password";
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    if ($password !== $confirm_password) {
        $register_error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $register_error = "Password must be at least 6 characters long";
    } else {
        $userId = $auth->register($name, $email, $password, $role);
        if ($userId) {
            // If organizer is registered, create organizer profile
            if ($role === 'organizer') {
                $db = new Database();
                $db->insert('organizers', [
                    'user_id' => $userId,
                    'name' => $name,
                    'bio' => '',
                    'video_url' => ''
                ]);
            }
            
            $register_success = "Registration successful! Please login.";
        } else {
            $register_error = "Email already exists";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Event Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h1>EventHub</h1>
            <p>Discover amazing events, create unforgettable experiences, and connect with people who share your passions.</p>
            
            <div class="features">
                <div class="feature">
                    <h3>1000+ Events</h3>
                    <p>Wide variety of events to choose from</p>
                </div>
                <div class="feature">
                    <h3>Live Music</h3>
                    <p>Experience the best live performances</p>
                </div>
                <div class="feature">
                    <h3>Top Organizers</h3>
                    <p>Connect with trusted event organizers</p>
                </div>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form">
                <h2>Welcome Back!</h2>
                
                <div class="login-tabs">
                    <div class="login-tab active" data-tab="user-login">User Login</div>
                    <div class="login-tab" data-tab="organizer-login">Organizer</div>
                    <div class="login-tab" data-tab="signup">Sign Up</div>
                </div>
                
                <!-- User Login Form -->
                <div id="user-login" class="login-tab-content active">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php">
                        <div class="form-group">
                            <label for="user-email">Email</label>
                            <input type="email" id="user-email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user-password">Password</label>
                            <input type="password" id="user-password" name="password" required>
                        </div>
                        
                        <input type="hidden" name="role" value="user">
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Login as User</button>
                    </form>
                </div>
                
                <!-- Organizer Login Form -->
                <div id="organizer-login" class="login-tab-content">
                    <form method="POST" action="index.php">
                        <div class="form-group">
                            <label for="organizer-email">Email</label>
                            <input type="email" id="organizer-email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="organizer-password">Password</label>
                            <input type="password" id="organizer-password" name="password" required>
                        </div>
                        
                        <input type="hidden" name="role" value="organizer">
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Login as Organizer</button>
                    </form>
                </div>
                
                <!-- Sign Up Form -->
                <div id="signup" class="login-tab-content">
                    <?php if (isset($register_error)): ?>
                        <div class="alert alert-danger"><?php echo $register_error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($register_success)): ?>
                        <div class="alert alert-success"><?php echo $register_success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php">
                        <div class="form-group">
                            <label for="register-name">Full Name</label>
                            <input type="text" id="register-name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-email">Email</label>
                            <input type="email" id="register-email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-password">Password</label>
                            <input type="password" id="register-password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-confirm-password">Confirm Password</label>
                            <input type="password" id="register-confirm-password" name="confirm_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-role">I am a:</label>
                            <select id="register-role" name="role" required>
                                <option value="user">User (Attendee)</option>
                                <option value="organizer">Organizer</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-success" style="width: 100%;">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>