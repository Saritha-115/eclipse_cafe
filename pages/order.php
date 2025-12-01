<?php
$page_title = "Place Order";
require_once '../includes/db.php';
require_once '../includes/header.php';

$success_message = '';
$error_message = '';
$selected_item = null;

// Get item if specified in URL
if (isset($_GET['item_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND is_available = 1");
        $stmt->execute([cleanInput($_GET['item_id'])]);
        $selected_item = $stmt->fetch();
    } catch (PDOException $e) {
        $error_message = "Error loading item details.";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $customer_name = cleanInput($_POST['customer_name'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $item_id = cleanInput($_POST['item_id'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    $notes = cleanInput($_POST['notes'] ?? '');
    
    $errors = [];
    
    // Server-side validation
    if (strlen($customer_name) < 2) {
        $errors[] = "Name must be at least 2 characters long.";
    }
    
    if (!preg_match('/^[0-9]{7,15}$/', $phone)) {
        $errors[] = "Phone number must be 7-15 digits.";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if ($quantity < 1 || $quantity > 50) {
        $errors[] = "Quantity must be between 1 and 50.";
    }
    
    if (empty($errors)) {
        try {
            // Get item details
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();
            
            if ($item) {
                $total = $item['price'] * $quantity;
                $items_json = json_encode([
                    'item_id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $quantity
                ]);
                
                // Insert order
                $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, email, items, total, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->execute([$customer_name, $phone, $email, $items_json, $total, $notes]);
                
                $order_id = $pdo->lastInsertId();
                $success_message = "Order #$order_id placed successfully! Total: $" . number_format($total, 2);
                
                // Clear form
                $selected_item = null;
            } else {
                $error_message = "Selected item not available.";
            }
        } catch (PDOException $e) {
            $error_message = "Failed to place order. Please try again.";
            error_log("Order error: " . $e->getMessage());
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Get all available items for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name");
    $all_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $all_items = [];
}
?>

<!-- Page Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            <i class="bi bi-cart-fill me-3"></i>Place Your Order
        </h1>
        <p class="lead">Fill in your details and we'll prepare your order</p>
    </div>
</div>

<!-- Order Form -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Success Alert -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Success!</strong> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Error Alert -->
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Order Form Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" id="orderForm" class="needs-validation" novalidate>
                            
                            <!-- Customer Information -->
                            <h4 class="fw-bold mb-4" style="color: #8B4513;">
                                <i class="bi bi-person-fill me-2"></i>Customer Information
                            </h4>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="customer_name" class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required minlength="2" placeholder="John Doe">
                                    <div class="invalid-feedback">Please enter your name (at least 2 characters).</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required pattern="[0-9]{7,15}" placeholder="1234567890">
                                    <div class="invalid-feedback">Please enter a valid phone number (7-15 digits).</div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="email" class="form-label fw-semibold">Email (Optional)</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com">
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                            </div>
                            
                            <!-- Order Details -->
                            <h4 class="fw-bold mb-4" style="color: #8B4513;">
                                <i class="bi bi-bag-fill me-2"></i>Order Details
                            </h4>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label for="item_id" class="form-label fw-semibold">Select Item *</label>
                                    <select class="form-select" id="item_id" name="item_id" required onchange="updatePrice()">
                                        <option value="">Choose an item...</option>
                                        <?php 
                                        $current_category = '';
                                        foreach ($all_items as $item): 
                                            if ($current_category !== $item['category']):
                                                if ($current_category !== '') echo '</optgroup>';
                                                echo '<optgroup label="' . sanitize($item['category']) . '">';
                                                $current_category = $item['category'];
                                            endif;
                                        ?>
                                            <option value="<?php echo $item['id']; ?>" 
                                                    data-price="<?php echo $item['price']; ?>"
                                                    <?php echo ($selected_item && $selected_item['id'] == $item['id']) ? 'selected' : ''; ?>>
                                                <?php echo sanitize($item['name']); ?> - $<?php echo number_format($item['price'], 2); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php if ($current_category !== '') echo '</optgroup>'; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select an item.</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="quantity" class="form-label fw-semibold">Quantity *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="50" required onchange="updatePrice()">
                                    <div class="invalid-feedback">Quantity must be 1-50.</div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="notes" class="form-label fw-semibold">Special Instructions (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special requests or dietary requirements..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Order Total -->
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-semibold">Order Total:</span>
                                    <span class="fs-3 fw-bold" style="color: #8B4513;" id="totalPrice">$0.00</span>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg text-white fw-bold" style="background: linear-gradient(135deg, #8B4513, #D2691E); border-radius: 25px; padding: 15px;">
                                    <i class="bi bi-check-circle-fill me-2"></i>Place Order
                                </button>
                                <a href="menu.php" class="btn btn-outline-secondary btn-lg" style="border-radius: 25px;">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Menu
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Update price when item or quantity changes
function updatePrice() {
    const itemSelect = document.getElementById('item_id');
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    const total = price * quantity;
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(2);
}

// Initialize price on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePrice();
});

// Bootstrap form validation
(function() {
    'use strict';
    const form = document.getElementById('orderForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
})();
</script>

<?php require_once '../includes/footer.php'; ?>