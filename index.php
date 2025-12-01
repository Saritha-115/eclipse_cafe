<?php
$page_title = "Home";
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch featured menu items
try {
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY RAND() LIMIT 6");
    $featured_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_items = [];
}
?>

<!-- Hero Carousel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="position-relative" style="height: 600px; background: linear-gradient(135deg, rgba(139,69,19,0.9) 0%, rgba(210,105,30,0.8) 100%), url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1600') center/cover;">
                <div class="container h-100 d-flex align-items-center">
                    <div class="text-white" style="max-width: 600px;">
                        <h1 class="display-3 fw-bold mb-4" style="font-family: 'Playfair Display', serif;">
                            Welcome to Eclipse Café
                        </h1>
                        <p class="fs-5 mb-4">
                            Where every cup tells a story. Experience the finest coffee, crafted with passion and served with a smile.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="pages/menu.php" class="btn btn-warning btn-lg px-4" style="border-radius: 25px;">
                                <i class="bi bi-journal-text me-2"></i>View Menu
                            </a>
                            <a href="pages/order.php" class="btn btn-outline-light btn-lg px-4" style="border-radius: 25px;">
                                <i class="bi bi-cart-fill me-2"></i>Order Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="carousel-item">
            <div class="position-relative" style="height: 600px; background: linear-gradient(135deg, rgba(44,24,16,0.9) 0%, rgba(139,69,19,0.8) 100%), url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=1600') center/cover;">
                <div class="container h-100 d-flex align-items-center">
                    <div class="text-white" style="max-width: 600px;">
                        <h1 class="display-3 fw-bold mb-4" style="font-family: 'Playfair Display', serif;">
                            Artisan Coffee
                        </h1>
                        <p class="fs-5 mb-4">
                            From bean to cup, we select only the finest ingredients. Discover flavors that awaken your senses.
                        </p>
                        <a href="pages/menu.php" class="btn btn-warning btn-lg px-4" style="border-radius: 25px;">
                            <i class="bi bi-cup-hot me-2"></i>Explore Coffee
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="carousel-item">
            <div class="position-relative" style="height: 600px; background: linear-gradient(135deg, rgba(210,105,30,0.9) 0%, rgba(255,140,66,0.8) 100%), url('https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=1600') center/cover;">
                <div class="container h-100 d-flex align-items-center">
                    <div class="text-white" style="max-width: 600px;">
                        <h1 class="display-3 fw-bold mb-4" style="font-family: 'Playfair Display', serif;">
                            Fresh Daily Pastries
                        </h1>
                        <p class="fs-5 mb-4">
                            Complement your coffee with our handcrafted pastries and desserts. Baked fresh every morning.
                        </p>
                        <a href="pages/menu.php" class="btn btn-warning btn-lg px-4" style="border-radius: 25px;">
                            <i class="bi bi-shop me-2"></i>Browse Treats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Features Section -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="p-4 h-100">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-cup-hot-fill fs-1 text-dark"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Premium Quality</h4>
                    <p class="text-muted">Sourced from the finest coffee plantations worldwide. Every bean is handpicked for excellence.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 h-100">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-lightning-fill fs-1 text-dark"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Fast Service</h4>
                    <p class="text-muted">Quick ordering and preparation without compromising quality. Get your perfect cup in minutes.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 h-100">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-heart-fill fs-1 text-dark"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Made with Love</h4>
                    <p class="text-muted">Every drink is crafted by passionate baristas who care about your experience.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Menu Items -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                Featured Items
            </h2>
            <p class="lead text-muted">Discover our most popular selections</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($featured_items)): ?>
                <?php foreach ($featured_items as $item): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div class="position-relative overflow-hidden" style="height: 250px;">
                            <img src="/eclipse_cafe/uploads/<?php echo sanitize($item['image']); ?>" 
                                 class="card-img-top w-100 h-100 object-fit-cover" 
                                 alt="<?php echo sanitize($item['name']); ?>"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=<?php echo urlencode($item['name']); ?>'">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <?php echo sanitize($item['category']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo sanitize($item['name']); ?></h5>
                            <p class="card-text text-muted small">
                                <?php echo sanitize(substr($item['description'], 0, 80)) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fs-4 fw-bold" style="color: #8B4513;">
                                    $<?php echo number_format($item['price'], 2); ?>
                                </span>
                                <a href="pages/order.php?item_id=<?php echo $item['id']; ?>" 
                                   class="btn btn-warning" style="border-radius: 20px;">
                                    <i class="bi bi-cart-plus me-1"></i> Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No items available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="pages/menu.php" class="btn btn-lg" style="background: linear-gradient(135deg, #8B4513, #D2691E); color: white; border-radius: 25px; padding: 12px 40px;">
                <i class="bi bi-grid-3x3-gap me-2"></i>View Full Menu
            </a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container text-center text-white">
        <h2 class="display-5 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            Ready to Order?
        </h2>
        <p class="lead mb-4">Experience the Eclipse difference. Order now and taste perfection.</p>
        <a href="pages/order.php" class="btn btn-warning btn-lg px-5" style="border-radius: 25px;">
            <i class="bi bi-cart-fill me-2"></i>Place Your Order
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>