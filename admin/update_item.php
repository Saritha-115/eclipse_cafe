<?php
require_once 'includes/auth_check.php';
require_once '../includes/db.php';

$page_title = "Update Menu Item";
$success_message = '';
$error_message = '';
$item = null;

// Get item ID
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch item details
if ($item_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if (!$item) {
            header('Location: ../pages/menu.php');
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "Error loading item.";
    }
} else {
    header('Location: ../pages/menu.php');
    exit;
}

// Process update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $item) {
    $name = cleanInput($_POST['name'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = cleanInput($_POST['category'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    $errors = [];
    
    if (strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
    if ($price <= 0 || $price > 999.99) $errors[] = "Invalid price.";
    if (empty($category)) $errors[] = "Category is required.";
    
    // Handle image upload
    $image_name = $item['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= 5000000) {
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('item_') . '.' . $file_ext;
            $upload_path = '../uploads/' . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if not default
                if ($item['image'] !== 'default.jpg' && file_exists('../uploads/' . $item['image'])) {
                    unlink('../uploads/' . $item['image']);
                }
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, description = ?, price = ?, category = ?, image = ?, is_available = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category, $image_name, $is_available, $item_id]);
            $success_message = "Item updated successfully!";
            
            // Refresh item data
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();
        } catch (PDOException $e) {
            $error_message = "Failed to update item.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
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
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #2C1810 0%, #8B4513 100%); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include same sidebar as other admin pages -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar px-0">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <i class="bi bi-cup-hot-fill text-warning fs-1"></i>
                        <h4 class="text-white" style="font-family: 'Playfair Display', serif;">Eclipse Café</h4>
                    </div>
                    <ul class="nav flex-column px-2">
                        <li class="nav-item"><a class="nav-link text-white-50" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="add_item.php"><i class="bi bi-plus-circle me-2"></i>Add Item</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="../pages/menu.php"><i class="bi bi-journal-text me-2"></i>Menu</a></li>
                        <li class="nav-item"><a class="nav-link text-white-50" href="orders.php"><i class="bi bi-bag-check me-2"></i>Orders</a></li>
                        <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 fw-bold" style="color: #8B4513;">
                        <i class="bi bi-pencil-square me-2"></i>Update Menu Item
                    </h1>
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
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Current Image</label>
                                        <div>
                                            <img src="../uploads/<?php echo sanitize($item['image']); ?>" class="img-thumbnail" style="max-width: 200px;" onerror="this.src='https://via.placeholder.com/200?text=No+Image'">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-semibold">Item Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($item['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label fw-semibold">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo sanitize($item['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="price" class="form-label fw-semibold">Price *</label>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $item['price']; ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="category" class="form-label fw-semibold">Category *</label>
                                            <input type="text" class="form-control" id="category" name="category" value="<?php echo sanitize($item['category']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-semibold">Update Image (Optional)</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" <?php echo $item['is_available'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-semibold" for="is_available">Available for order</label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-warning btn-lg"><i class="bi bi-save me-2"></i>Update Item</button>
                                        <a href="../pages/menu.php" class="btn btn-outline-secondary">Cancel</a>
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
</body>
</html>