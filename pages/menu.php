<?php
$page_title = "Menu";
require_once '../includes/db.php';
require_once '../includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Quick add to cart from menu
if (isset($_POST['quick_add'])) {
    $item_id = intval($_POST['item_id']);
    $quantity = 1;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND is_available = 1");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if ($item) {
            $found = false;
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['id'] == $item_id) {
                    $cart_item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $quantity
                ];
            }
            
            $_SESSION['cart_message'] = "Added to cart!";
        }
    } catch (PDOException $e) {
        $_SESSION['cart_error'] = "Error adding to cart.";
    }
    
    header('Location: menu.php');
    exit;
}

$cart_message = isset($_SESSION['cart_message']) ? $_SESSION['cart_message'] : '';
$cart_error = isset($_SESSION['cart_error']) ? $_SESSION['cart_error'] : '';
unset($_SESSION['cart_message']);
unset($_SESSION['cart_error']);

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

try {
    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM menu_items WHERE is_available = 1 ORDER BY category");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

$selected_category = isset($_GET['category']) ? cleanInput($_GET['category']) : 'all';
$search_term = isset($_GET['search']) ? cleanInput($_GET['search']) : '';

try {
    if ($selected_category === 'all' && empty($search_term)) {
        $stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name");
    } elseif ($selected_category !== 'all' && empty($search_term)) {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ? AND is_available = 1 ORDER BY name");
        $stmt->execute([$selected_category]);
    } elseif (!empty($search_term) && $selected_category === 'all') {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE (name LIKE ? OR description LIKE ?) AND is_available = 1 ORDER BY name");
        $search_param = "%{$search_term}%";
        $stmt->execute([$search_param, $search_param]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ? AND (name LIKE ? OR description LIKE ?) AND is_available = 1 ORDER BY name");
        $search_param = "%{$search_term}%";
        $stmt->execute([$selected_category, $search_param, $search_param]);
    }
    $menu_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $menu_items = [];
}

$cart_count = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $cart_count += $cart_item['quantity'];
}
?>

<!-- Page Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            <i class="bi bi-journal-text me-3"></i>Our Menu
        </h1>
        <p class="lead">Discover our handcrafted selections</p>
        
        <?php if ($cart_count > 0): ?>
        <div class="mt-3">
            <a href="order.php" class="btn btn-warning btn-lg">
                <i class="bi bi-cart-fill me-2"></i>View Cart 
                <span class="badge bg-danger"><?php echo $cart_count; ?></span>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($is_admin): ?>
        <div class="mt-3">
            <a href="../admin/dashboard.php" class="btn btn-light me-2">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="../admin/add_item.php" class="btn btn-outline-light">
                <i class="bi bi-plus-circle me-1"></i> Add New Item
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filter & Search Section -->
<section class="py-4 bg-light sticky-top" style="top: 56px; z-index: 999;">
    <div class="container">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $selected_category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo sanitize($cat['category']); ?>" <?php echo $selected_category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($cat['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search menu items..." value="<?php echo sanitize($search_term); ?>">
                    <button class="btn btn-warning" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </div>
            <div class="col-md-2">
                <a href="menu.php" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle"></i> Clear</a>
            </div>
        </form>
    </div>
</section>

<!-- Messages -->
<?php if ($cart_message): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo $cart_message; ?>
        <a href="order.php" class="alert-link ms-2">View Cart</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<?php if ($success_message): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<?php if ($error_message): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<!-- Menu Items Grid -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        <?php if (!empty($menu_items)): ?>
            <div class="row g-4">
                <?php foreach ($menu_items as $item): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm" style="transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.12)'">
                        <div class="position-relative overflow-hidden" style="height: 220px;">
                            <img src="/eclipse_cafe/uploads/<?php echo sanitize($item['image']); ?>" class="card-img-top w-100 h-100 object-fit-cover" alt="<?php echo sanitize($item['name']); ?>" onerror="this.src='https://via.placeholder.com/400x300?text=<?php echo urlencode($item['name']); ?>'">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark px-3 py-2"><?php echo sanitize($item['category']); ?></span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo sanitize($item['name']); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo sanitize($item['description']); ?></p>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-4 fw-bold" style="color: #8B4513;">$<?php echo number_format($item['price'], 2); ?></span>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="quick_add" class="btn btn-warning w-100">
                                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                        </button>
                                    </form>
                                    <a href="order.php?item_id=<?php echo $item['id']; ?>" class="btn btn-outline-warning">
                                        <i class="bi bi-bag me-1"></i> Order Now
                                    </a>
                                </div>
                                
                                <?php if ($is_admin): ?>
                                <div class="d-grid gap-2 mt-3 pt-3 border-top">
                                    <a href="../admin/update_item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="<?php echo $item['id']; ?>" 
                                            data-name="<?php echo sanitize($item['name']); ?>">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <h4 class="mt-3 text-muted">No items found</h4>
                <p class="text-muted">Try adjusting your filters or search terms</p>
                <a href="menu.php" class="btn btn-warning mt-3">View All Items</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Floating Cart Button (Mobile) -->
<?php if ($cart_count > 0): ?>
<a href="order.php" class="btn btn-warning btn-lg rounded-circle d-lg-none" 
   style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 1000;">
    <i class="bi bi-cart-fill"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?php echo $cart_count; ?>
    </span>
</a>
<?php endif; ?>

<!-- Single Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to delete <strong id="itemNameToDelete"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Handle delete button clicks
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const itemNameElement = document.getElementById('itemNameToDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');
            
            // Update modal content
            itemNameElement.textContent = itemName;
            
            // Set the delete URL
            confirmDeleteBtn.href = '../admin/delete_item.php?id=' + itemId + '&confirm=yes';
            
            // Show modal
            deleteModal.show();
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>