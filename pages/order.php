<?php
$page_title = "Place Order";
require_once '../includes/db.php';
require_once '../includes/header.php';

$success_message = '';
$error_message = '';

// Initialize cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $item_id = intval($_POST['item_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    if ($item_id > 0 && $quantity > 0) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND is_available = 1");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();
            
            if ($item) {
                // Check if item already in cart
                $found = false;
                foreach ($_SESSION['cart'] as &$cart_item) {
                    if ($cart_item['id'] == $item_id) {
                        $cart_item['quantity'] += $quantity;
                        $found = true;
                        break;
                    }
                }
                
                // Add new item if not found
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $quantity
                    ];
                }
                
                $success_message = "Item added to cart!";
            }
        } catch (PDOException $e) {
            $error_message = "Error adding item to cart.";
        }
    }
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    foreach ($_SESSION['cart'] as $key => $cart_item) {
        if ($cart_item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            $success_message = "Item removed from cart!";
            break;
        }
    }
}

// Handle Update Quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $item_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['id'] == $item_id) {
                    $cart_item['quantity'] = $quantity;
                    break;
                }
            }
        }
    }
    $success_message = "Cart updated!";
}

// Handle Clear Cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
    $success_message = "Cart cleared!";
}

// Handle Checkout
if (isset($_POST['checkout'])) {
    $customer_name = cleanInput($_POST['customer_name'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $notes = cleanInput($_POST['notes'] ?? '');
    
    $errors = [];
    
    // Validation
    if (strlen($customer_name) < 2) {
        $errors[] = "Name must be at least 2 characters long.";
    }
    
    if (!preg_match('/^[0-9]{7,15}$/', $phone)) {
        $errors[] = "Phone number must be 7-15 digits.";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($_SESSION['cart'])) {
        $errors[] = "Your cart is empty.";
    }
    
    if (empty($errors)) {
        try {
            // Calculate total
            $total = 0;
            foreach ($_SESSION['cart'] as $cart_item) {
                $total += $cart_item['price'] * $cart_item['quantity'];
            }
            
            // Create readable items string (e.g., "Espresso x2, Latte x1")
            $items_array = [];
            foreach ($_SESSION['cart'] as $cart_item) {
                $items_array[] = $cart_item['name'] . " x" . $cart_item['quantity'];
            }
            $items_string = implode(", ", $items_array);
            
            // Format notes
            $formatted_notes = empty($notes) ? 'None' : $notes;
            
            // Insert order with readable format
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, email, items, total, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([
                $customer_name, 
                $phone, 
                $email ?: NULL, 
                $items_string,  // Readable format: "Item1 x2, Item2 x1"
                $total, 
                $formatted_notes
            ]);
            
            $order_id = $pdo->lastInsertId();
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $success_message = "Order #$order_id placed successfully! Total: $" . number_format($total, 2) . ". We'll contact you shortly.";
        } catch (PDOException $e) {
            $error_message = "Failed to place order. Please try again.";
            error_log("Order error: " . $e->getMessage());
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $cart_total += $cart_item['price'] * $cart_item['quantity'];
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
        <p class="lead">Add items to cart and checkout when ready</p>
    </div>
</div>

<!-- Order Section -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        
        <!-- Alerts -->
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
        
        <div class="row g-4">
            <!-- Left: Add Items to Cart -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #8B4513, #D2691E);">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Items to Cart</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
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
                                        <option value="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>">
                                            <?php echo sanitize($item['name']); ?> - $<?php echo number_format($item['price'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if ($current_category !== '') echo '</optgroup>'; ?>
                                </select>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="quantity" class="form-label fw-semibold">Quantity *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="50" required onchange="updatePrice()">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Item Total</label>
                                    <div class="form-control bg-light fw-bold" style="color: #8B4513;" id="itemTotal">$0.00</div>
                                </div>
                            </div>
                            
                            <button type="submit" name="add_to_cart" class="btn btn-warning w-100 btn-lg">
                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <a href="menu.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>Back to Menu
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Right: Shopping Cart -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg sticky-top" style="top: 80px;">
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #8B4513, #D2691E);">
                        <h5 class="mb-0">
                            <i class="bi bi-cart3 me-2"></i>Shopping Cart 
                            <span class="badge bg-warning text-dark"><?php echo count($_SESSION['cart']); ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <form method="POST">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($_SESSION['cart'] as $cart_item): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo sanitize($cart_item['name']); ?></td>
                                                <td>$<?php echo number_format($cart_item['price'], 2); ?></td>
                                                <td>
                                                    <input type="number" name="quantities[<?php echo $cart_item['id']; ?>]" 
                                                           value="<?php echo $cart_item['quantity']; ?>" 
                                                           min="1" max="50" 
                                                           class="form-control form-control-sm" 
                                                           style="width: 60px;">
                                                </td>
                                                <td class="fw-bold" style="color: #8B4513;">
                                                    $<?php echo number_format($cart_item['price'] * $cart_item['quantity'], 2); ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"
                                                            data-item-id="<?php echo $cart_item['id']; ?>"
                                                            data-item-name="<?php echo sanitize($cart_item['name']); ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3">Cart Total:</th>
                                                <th colspan="2" class="fs-5" style="color: #8B4513;">
                                                    $<?php echo number_format($cart_total, 2); ?>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="p-3 d-grid gap-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="clearCartBtn">
                                        <i class="bi bi-trash me-1"></i>Clear Cart
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Checkout Form -->
                            <div class="border-top p-3">
                                <h6 class="fw-bold mb-3">Customer Details</h6>
                                <form method="POST" class="needs-validation" novalidate>
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" name="customer_name" 
                                               placeholder="Your Name *" required minlength="2">
                                    </div>
                                    <div class="mb-2">
                                        <input type="tel" class="form-control form-control-sm" name="phone" 
                                               placeholder="Phone Number *" required pattern="[0-9]{7,15}">
                                    </div>
                                    <div class="mb-2">
                                        <input type="email" class="form-control form-control-sm" name="email" 
                                               placeholder="Email (optional)">
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control form-control-sm" name="notes" rows="2" 
                                                  placeholder="Special instructions..."></textarea>
                                    </div>
                                    <button type="submit" name="checkout" class="btn btn-success w-100 btn-lg">
                                        <i class="bi bi-check-circle-fill me-2"></i>Checkout - $<?php echo number_format($cart_total, 2); ?>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x fs-1 text-muted"></i>
                                <p class="text-muted mt-3 mb-0">Your cart is empty</p>
                                <small class="text-muted">Add items from the left</small>
                            </div>
                        <?php endif; ?>
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
    document.getElementById('itemTotal').textContent = '$' + total.toFixed(2);
}

// Update cart total and item totals in real-time
function updateCartDisplay() {
    let cartTotal = 0;
    const quantityInputs = document.querySelectorAll('input[name^="quantities["]');
    
    quantityInputs.forEach(function(input) {
        const row = input.closest('tr');
        const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', ''));
        const quantity = parseInt(input.value) || 0;
        const itemTotal = price * quantity;
        
        // Update the total for this item
        row.querySelector('td:nth-child(4)').textContent = '$' + itemTotal.toFixed(2);
        
        cartTotal += itemTotal;
    });
    
    // Update the cart total in footer
    const footerTotal = document.querySelector('tfoot th:last-child');
    if (footerTotal) {
        footerTotal.textContent = '$' + cartTotal.toFixed(2);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePrice();
    
    // Add event listeners to quantity inputs for real-time updates
    const quantityInputs = document.querySelectorAll('input[name^="quantities["]');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', updateCartDisplay);
        input.addEventListener('input', updateCartDisplay);
    });
});

// Form validation
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

// Handle remove from cart
document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.remove-item-btn');
    const removeModal = new bootstrap.Modal(document.getElementById('removeItemModal'));
    const itemNameElement = document.getElementById('itemNameToRemove');
    const confirmRemoveBtn = document.getElementById('confirmRemoveBtn');
    
    removeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const itemName = this.getAttribute('data-item-name');
            
            // Update modal content
            itemNameElement.textContent = itemName;
            
            // Set the remove URL
            confirmRemoveBtn.href = '?remove=' + itemId;
            
            // Show modal
            removeModal.show();
        });
    });
    
    // Handle clear cart button
    const clearCartBtn = document.getElementById('clearCartBtn');
    const clearCartModal = new bootstrap.Modal(document.getElementById('clearCartModal'));
    
    clearCartBtn.addEventListener('click', function() {
        clearCartModal.show();
    });
    
    document.getElementById('confirmClearCartBtn').addEventListener('click', function() {
        window.location.href = '?clear_cart=1';
    });
});
</script>

<!-- Remove Item Confirmation Modal -->
<div class="modal fade" id="removeItemModal" tabindex="-1" aria-labelledby="removeItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="removeItemModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Remove Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to remove <strong id="itemNameToRemove"></strong> from your cart?</p>
                <p class="text-muted small mb-0">You can add it back anytime.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <a href="#" id="confirmRemoveBtn" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Remove
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Clear Cart Confirmation Modal -->
<div class="modal fade" id="clearCartModal" tabindex="-1" aria-labelledby="clearCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="clearCartModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Clear Cart
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to clear your entire cart?</p>
                <p class="text-muted small mb-0">All items will be removed from your cart.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="confirmClearCartBtn" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>