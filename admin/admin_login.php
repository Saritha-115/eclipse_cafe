<?php
session_start();
require_once '../includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Update last login
                $update_stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$admin['id']]);
                
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error_message = "Login failed. Please try again.";
            error_log("Login error: " . $e->getMessage());
        }
    } else {
        $error_message = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Eclipse Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Poppins', sans-serif;">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <!-- Login Card -->
                <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <!-- Header -->
                    <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #2C1810 0%, #8B4513 100%); border: none;">
                        <i class="bi bi-shield-lock-fill fs-1 mb-3 d-block"></i>
                        <h3 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Admin Login</h3>
                        <p class="mb-0 small opacity-75">Eclipse Café Management</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="card-body p-5">
                        <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="username" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill text-warning me-2"></i>Username
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username" required autofocus placeholder="Enter username">
                                <div class="invalid-feedback">Please enter your username.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-key-fill text-warning me-2"></i>Password
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required placeholder="Enter password">
                                <div class="invalid-feedback">Please enter your password.</div>
                            </div>
                            
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-lg text-white fw-bold" style="background: linear-gradient(135deg, #8B4513, #D2691E); border-radius: 10px; padding: 12px;">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="../index.php" class="text-decoration-none text-muted">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Website
                                </a>
                            </div>
                        </form>
                        
                        <!-- Default Credentials Info -->
                        <div class="alert alert-info mt-4 mb-0">
                            <small>
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Default Login:</strong><br>
                                Username: <code>admin</code><br>
                                Password: <code>admin123</code>
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Text -->
                <div class="text-center mt-4 text-white">
                    <small>&copy; <?php echo date('Y'); ?> Eclipse Café. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            'use strict';
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
</body>
</html>