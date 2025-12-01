<?php
$page_title = "Contact Us";
require_once '../includes/db.php';
require_once '../includes/header.php';

$success_message = '';
$error_message = '';

// Process contact form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $message = cleanInput($_POST['message'] ?? '');
    
    $errors = [];
    
    if (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    
    if (!empty($phone) && !preg_match('/^[0-9]{7,15}$/', $phone)) {
        $errors[] = "Phone number must be 7-15 digits.";
    }
    
    if (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters.";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $message]);
            $success_message = "Thank you for contacting us! We'll get back to you soon.";
        } catch (PDOException $e) {
            $error_message = "Failed to send message. Please try again.";
            error_log("Contact form error: " . $e->getMessage());
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<!-- Page Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            <i class="bi bi-envelope-fill me-3"></i>Contact Us
        </h1>
        <p class="lead">We'd love to hear from you</p>
    </div>
</div>

<!-- Contact Section -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4" style="color: #8B4513;">Send Us a Message</h3>
                        
                        <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Your Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required minlength="2" placeholder="John Doe">
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="john@example.com">
                                <div class="invalid-feedback">Please enter a valid email.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Phone Number (Optional)</label>
                                <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{7,15}" placeholder="1234567890">
                                <div class="invalid-feedback">Please enter a valid phone number.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label fw-semibold">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required minlength="10" placeholder="Tell us how we can help you..."></textarea>
                                <div class="invalid-feedback">Please enter your message (at least 10 characters).</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-lg text-white fw-bold" style="background: linear-gradient(135deg, #8B4513, #D2691E); border-radius: 25px; padding: 15px;">
                                    <i class="bi bi-send-fill me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-5">
                <div class="sticky-top" style="top: 80px;">
                    <h3 class="fw-bold mb-4" style="color: #8B4513;">Get In Touch</h3>
                    
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-warning bg-opacity-25 rounded p-3 me-3">
                                    <i class="bi bi-geo-alt-fill fs-4" style="color: #8B4513;"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Visit Us</h5>
                                    <p class="text-muted mb-0">123 Coffee Street<br>Brew City, BC 12345</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-warning bg-opacity-25 rounded p-3 me-3">
                                    <i class="bi bi-telephone-fill fs-4" style="color: #8B4513;"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Call Us</h5>
                                    <p class="text-muted mb-0">+1 (555) 123-4567<br>Mon-Sun: 7AM - 10PM</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start">
                                <div class="bg-warning bg-opacity-25 rounded p-3 me-3">
                                    <i class="bi bi-envelope-fill fs-4" style="color: #8B4513;"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Email Us</h5>
                                    <p class="text-muted mb-0">info@eclipsecafe.com<br>support@eclipsecafe.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow">
                        <div class="card-body p-4 text-center">
                            <h5 class="fw-bold mb-3">Follow Us</h5>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#" class="btn btn-outline-dark btn-lg rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="btn btn-outline-dark btn-lg rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-dark btn-lg rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-dark btn-lg rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<?php require_once '../includes/footer.php'; ?>