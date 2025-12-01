<?php
require_once 'includes/auth_check.php';
require_once '../includes/db.php';

$page_title = "Add Menu Item";
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = cleanInput($_POST['category'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    $errors = [];
    
    // Validation
    if (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters.";
    }
    if ($price <= 0 || $price > 999.99) {
        $errors[] = "Price must be between $0.01 and $999.99.";
    }
    if (empty($category)) {
        $errors[] = "Category is required.";
    }
    
    // Handle image upload
    $image_name = 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Invalid image format. Use JPG, PNG, or GIF.";
        } elseif ($file_size > 5000000) { // 5MB
            $errors[] = "Image size must be less than 5MB.";
        } else {
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('item_') . '.' . $file_ext;
            $upload_path = '../uploads/' . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $errors[] = "Failed to upload image.";
                $image_name = 'default.jpg';
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category, image, is_available) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $category, $image_name, $is_available]);
            $success_message = "Menu item added successfully!";
        } catch (PDOException $e) {
            $error_message = "Failed to add item. Please try again.";
            error_log("Add item error: " . $e->getMessage());
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Get existing categories
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM menu_items ORDER BY category");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Eclipse Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #2C1810 0%, #8B4513 100%); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (same as dashboard) -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar px-0">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 pb-3 border-bottom border-secondary">
                        <i class="bi bi-cup-hot-fill text-warning fs-1 mb-2 d-block"></i>
                        <h4 class="text-white mb-0" style="font-family: 'Playfair Display', serif;">Eclipse Café</h4>
                        <small class="text-white-50">Admin Panel</small>
                    </div>
                    <div class="px-3 mb-3">
                        <div class="bg-white bg-opacity-10 rounded p-2 text-center">
                            <i class="bi bi-person-circle text-warning fs-3"></i>
                            <p class="text-white small mb-0 mt-1"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                        </div>
                    </div>
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link text-white-50 rounded mb-1" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white bg-warning bg-opacity-25 rounded mb-1" href="add_item.php">
                                <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 rounded mb-1" href="../pages/menu.php">
                                <i class="bi bi-journal-text me-2"></i>View Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 rounded mb-1" href="orders.php">
                                <i class="bi bi-bag-check me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item mt-3 pt-3 border-top border-secondary">
                            <a class="nav-link text-white-50 rounded" href="../index.php">
                                <i class="bi bi-house-door me-2"></i>View Website
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 fw-bold" style="color: #8B4513;">
                        <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                    </h1>
                    <a href="../pages/menu.php" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i>View Menu
                    </a>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
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
                        
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-semibold">Item Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" required minlength="2">
                                        <div class="invalid-feedback">Please enter item name.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label fw-semibold">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="price" class="form-label fw-semibold">Price (USD) *</label>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01" max="999.99" required>
                                            <div class="invalid-feedback">Enter a valid price.</div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="category" class="form-label fw-semibold">Category *</label>
                                            <input type="text" class="form-control" id="category" name="category" list="categoryList" required>
                                            <datalist id="categoryList">
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?php echo sanitize($cat['category']); ?>">
                                                <?php endforeach; ?>
                                                <option value="Coffee">
                                                <option value="Tea">
                                                <option value="Cold Drinks">
                                                <option value="Pastries">
                                                <option value="Desserts">
                                                <option value="Food">
                                            </datalist>
                                            <div class="invalid-feedback">Category is required.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-semibold">Item Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div class="form-text">Accepted: JPG, PNG, GIF. Max 5MB.</div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" checked>
                                            <label class="form-check-label fw-semibold" for="is_available">
                                                Available for order
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-warning btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>Add Item
                                        </button>
                                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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