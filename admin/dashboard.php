<?php
require_once 'includes/auth_check.php';
require_once '../includes/db.php';

$page_title = "Admin Dashboard";

// Get statistics
try {
    // Total menu items
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM menu_items");
    $total_items = $stmt->fetch()['total'];
    
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $total_orders = $stmt->fetch()['total'];
    
    // Pending orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $pending_orders = $stmt->fetch()['total'];
    
    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total) as revenue FROM orders WHERE status = 'completed'");
    $revenue_result = $stmt->fetch();
    $total_revenue = $revenue_result['revenue'] ?? 0;
    
    // Recent orders
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $total_items = $total_orders = $pending_orders = $total_revenue = 0;
    $recent_orders = [];
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
        .stat-card { transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar px-0">
                <div class="position-sticky pt-3">
                    <!-- Brand -->
                    <div class="text-center mb-4 pb-3 border-bottom border-secondary">
                        <i class="bi bi-cup-hot-fill text-warning fs-1 mb-2 d-block"></i>
                        <h4 class="text-white mb-0" style="font-family: 'Playfair Display', serif;">Eclipse Café</h4>
                        <small class="text-white-50">Admin Panel</small>
                    </div>
                    
                    <!-- User Info -->
                    <div class="px-3 mb-3">
                        <div class="bg-white bg-opacity-10 rounded p-2 text-center">
                            <i class="bi bi-person-circle text-warning fs-3"></i>
                            <p class="text-white small mb-0 mt-1"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link active text-white bg-warning bg-opacity-25 rounded mb-1" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 hover-warning rounded mb-1" href="add_item.php">
                                <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 hover-warning rounded mb-1" href="../pages/menu.php">
                                <i class="bi bi-journal-text me-2"></i>View Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50 hover-warning rounded mb-1" href="orders.php">
                                <i class="bi bi-bag-check me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item mt-3 pt-3 border-top border-secondary">
                            <a class="nav-link text-white-50 hover-warning rounded" href="../index.php">
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
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </h1>
                    <div class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        <?php echo date('l, F j, Y'); ?>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-journal-text fs-3 text-primary"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?php echo $total_items; ?></h3>
                                <p class="text-muted mb-0">Menu Items</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-bag-check-fill fs-3 text-success"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?php echo $total_orders; ?></h3>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-clock-history fs-3 text-warning"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?php echo $pending_orders; ?></h3>
                                <p class="text-muted mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-currency-dollar fs-3 text-info"></i>
                                </div>
                                <h3 class="fw-bold mb-1">$<?php echo number_format($total_revenue, 2); ?></h3>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3" style="color: #8B4513;">Quick Actions</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="add_item.php" class="btn btn-warning">
                                        <i class="bi bi-plus-circle me-1"></i> Add New Item
                                    </a>
                                    <a href="orders.php" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View Orders
                                    </a>
                                    <a href="../pages/menu.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-journal-text me-1"></i> View Menu
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title fw-bold mb-0" style="color: #8B4513;">
                            <i class="bi bi-clock-history me-2"></i>Recent Orders
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($recent_orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                        <td><?php echo sanitize($order['customer_name']); ?></td>
                                        <td><?php echo sanitize($order['phone']); ?></td>
                                        <td class="fw-bold">$<?php echo number_format($order['total'], 2); ?></td>
                                        <td>
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No orders yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white border-top text-center">
                        <a href="orders.php" class="btn btn-sm btn-outline-primary">View All Orders</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>