<?php
require_once 'includes/auth_check.php';
require_once '../includes/db.php';

$page_title = "Orders Management";

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = cleanInput($_POST['status']);
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $success_message = "Order status updated!";
    } catch (PDOException $e) {
        $error_message = "Failed to update status.";
    }
}

// Fetch all orders
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
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
            <!-- Sidebar -->
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
                        <li class="nav-item"><a class="nav-link active text-white bg-warning bg-opacity-25" href="orders.php"><i class="bi bi-bag-check me-2"></i>Orders</a></li>
                        <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 fw-bold" style="color: #8B4513;">
                        <i class="bi bi-bag-check me-2"></i>Orders Management
                    </h1>
                </div>
                
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                        <td><?php echo sanitize($order['customer_name']); ?></td>
                                        <td><?php echo sanitize($order['phone']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </td>
                                        <td class="fw-bold">$<?php echo number_format($order['total'], 2); ?></td>
                                        <td>
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                    </tr>
                                    
                                    <!-- Order Details Modal -->
                                    <div class="modal fade" id="orderModal<?php echo $order['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Order #<?php echo $order['id']; ?> Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Customer:</strong> <?php echo sanitize($order['customer_name']); ?></p>
                                                    <p><strong>Phone:</strong> <?php echo sanitize($order['phone']); ?></p>
                                                    <p><strong>Email:</strong> <?php echo sanitize($order['email'] ?: 'N/A'); ?></p>
                                                    <p><strong>Items:</strong> <?php echo sanitize($order['items']); ?></p>
                                                    <p><strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?></p>
                                                    <p><strong>Notes:</strong> <?php echo sanitize($order['notes'] ?: 'None'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>