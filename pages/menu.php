<?php
$page_title = "Menu";
require_once '../includes/db.php';
require_once '../includes/header.php';

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
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fs-4 fw-bold" style="color: #8B4513;">$<?php echo number_format($item['price'], 2); ?></span>
                                <a href="order.php?item_id=<?php echo $item['id']; ?>" class="btn btn-warning" style="border-radius: 20px;">
                                    <i class="bi bi-cart-plus me-1"></i> Order
                                </a>
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

<?php require_once '../includes/footer.php'; ?>