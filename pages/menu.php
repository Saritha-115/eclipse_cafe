<?php
$page_title = "Menu";
require_once '../includes/db.php';
require_once '../includes/header.php';

// Check if admin is logged in
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get success/error messages from session (for delete operations)
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Get all categories
try {
    $cat_stmt = $pdo->query("SELECT DISTINCT category FROM menu_items WHERE is_available = 1 ORDER BY category");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Get selected category from URL
$selected_category = isset($_GET['category']) ? cleanInput($_GET['category']) : 'all';
$search_term = isset($_GET['search']) ? cleanInput($_GET['search']) : '';

// Build query based on filters
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
?>

<!-- Page Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            <i class="bi bi-journal-text me-3"></i>Our Menu
        </h1>
        <p class="lead">Discover our handcrafted selections</p>
        
        <?php if ($is_admin): ?>
        <div class="mt-3">
            <a href="../admin/dashboard.php" class="btn btn-light me-2">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="../admin/add_item.php" class="btn btn-warning">
                <i class="bi bi-plus-circle me-1"></i> Add New Item
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filter & Search Section -->
<section class="py-4 bg-light sticky-top" style="top: 56px; z-index: 10;">
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

<!-- Success/Error Messages -->
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
                                    <a href="order.php?item_id=<?php echo $item['id']; ?>" class="btn btn-warning" style="border-radius: 20px;">
                                        <i class="bi bi-cart-plus me-1"></i> Order
                                    </a>
                                </div>
                                
                                <!-- Admin Controls -->
                                <?php if ($is_admin): ?>
                                <div class="d-grid gap-2 mt-3 pt-3 border-top">
                                    <a href="../admin/update_item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form method="POST" action="../admin/delete_item.php" style="display: inline;">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="item_name" value="<?php echo sanitize($item['name']); ?>">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 delete-btn" data-item-id="<?php echo $item['id']; ?>" data-item-name="<?php echo sanitize($item['name']); ?>">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
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

<!-- Global Delete Confirmation Modal -->
<div class="modal fade" id="globalDeleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete <strong id="itemNameDisplay"></strong>?</p>
                <p class="text-muted small mb-0 mt-2">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const globalDeleteModal = new bootstrap.Modal(document.getElementById('globalDeleteModal'));
    let currentItemId = null;

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentItemId = this.getAttribute('data-item-id');
            const itemName = this.getAttribute('data-item-name');
            
            // Update the modal content
            document.getElementById('itemNameDisplay').textContent = itemName;
            
            // Show the modal
            globalDeleteModal.show();
        });
    });

    // Handle confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentItemId) {
            window.location.href = '/eclipse_cafe/admin/delete_item.php?id=' + currentItemId + '&confirm=yes';
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>