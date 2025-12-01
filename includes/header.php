<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Eclipse Café - Premium coffee and dining experience">
    <title><?php echo isset($page_title) ? $page_title . ' - Eclipse Café' : 'Eclipse Café - Premium Coffee & Dining'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/eclipse_cafe/css/style.css">
    
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D2691E;
            --accent-color: #FF8C42;
            --dark-color: #2C1810;
            --light-color: #FFF8DC;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FFF8DC;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/eclipse_cafe/index.php">
                <i class="bi bi-cup-hot-fill fs-3 me-2"></i>
                <span class="fs-4 fw-bold" style="font-family: 'Playfair Display', serif;">Eclipse Café</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php' || $current_page == 'home.php') ? 'active' : ''; ?>" href="/eclipse_cafe/index.php">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'menu.php' ? 'active' : ''; ?>" href="/eclipse_cafe/pages/menu.php">
                            <i class="bi bi-journal-text me-1"></i> Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" href="/eclipse_cafe/pages/about.php">
                            <i class="bi bi-info-circle me-1"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="/eclipse_cafe/pages/contact.php">
                            <i class="bi bi-envelope me-1"></i> Contact
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning text-dark ms-lg-2 px-3" href="/eclipse_cafe/pages/order.php" style="border-radius: 25px;">
                            <i class="bi bi-cart-fill me-1"></i> Order Now
                        </a>
                    </li>
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="/eclipse_cafe/admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Admin
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>