<?php
$page_title = "About Us";
require_once '../includes/header.php';
?>

<!-- Page Header -->
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            <i class="bi bi-info-circle-fill me-3"></i>About Eclipse Café
        </h1>
        <p class="lead">Our Story, Our Passion, Our Promise</p>
    </div>
</div>

<!-- Our Story -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800" class="img-fluid rounded shadow-lg" alt="Café Interior">
            </div>
            <div class="col-lg-6">
                <h2 class="display-6 fw-bold mb-4" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Our Story
                </h2>
                <p class="lead text-muted mb-3">
                    Founded in 2020, Eclipse Café was born from a simple passion: to create the perfect coffee experience.
                </p>
                <p class="text-muted">
                    We believe that coffee is more than just a beverage—it's a ritual, a moment of pause in our busy lives, and a connection to people and places around the world. Every cup we serve is a testament to our commitment to quality, from sourcing the finest beans to perfecting every brew.
                </p>
                <p class="text-muted mb-0">
                    Our baristas are trained artisans who understand that making great coffee is both a science and an art. We're not just serving drinks; we're creating experiences and building a community of coffee lovers.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                Our Core Values
            </h2>
            <p class="lead text-muted">What drives us every single day</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4 h-100">
                    <div class="bg-warning bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="bi bi-gem fs-1" style="color: #8B4513;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Quality First</h4>
                    <p class="text-muted">We never compromise on the quality of our ingredients. From bean selection to the final pour, excellence is our standard.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4 h-100">
                    <div class="bg-warning bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="bi bi-globe fs-1" style="color: #8B4513;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Sustainability</h4>
                    <p class="text-muted">We're committed to ethical sourcing and environmental responsibility. Our coffee is fair-trade and eco-friendly.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4 h-100">
                    <div class="bg-warning bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="bi bi-people-fill fs-1" style="color: #8B4513;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Community</h4>
                    <p class="text-muted">We're more than a café—we're a gathering place where friendships are made and ideas are born.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visit Us -->
<section class="py-5" style="background-color: #FFF8DC;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4" style="color: #8B4513;">
                            <i class="bi bi-clock-fill me-2"></i>Opening Hours
                        </h3>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="fw-semibold">Monday - Friday</span>
                            <span class="text-muted">7:00 AM - 9:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="fw-semibold">Saturday</span>
                            <span class="text-muted">8:00 AM - 10:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Sunday</span>
                            <span class="text-muted">8:00 AM - 10:00 PM</span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h3 class="fw-bold mb-4" style="color: #8B4513;">
                            <i class="bi bi-geo-alt-fill me-2"></i>Location
                        </h3>
                        <p class="mb-2">
                            <i class="bi bi-pin-map text-warning me-2"></i>
                            123 Coffee Street, Brew City, BC 12345
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone text-warning me-2"></i>
                            +1 (555) 123-4567
                        </p>
                        <p class="mb-4">
                            <i class="bi bi-envelope text-warning me-2"></i>
                            info@eclipsecafe.com
                        </p>
                        
                        <a href="../pages/contact.php" class="btn btn-warning w-100" style="border-radius: 25px;">
                            <i class="bi bi-envelope-fill me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 order-lg-1">
                <h2 class="display-6 fw-bold mb-4" style="font-family: 'Playfair Display', serif; color: #8B4513;">
                    Visit Us Today
                </h2>
                <p class="lead text-muted mb-4">
                    Experience the Eclipse difference in person. We're conveniently located in the heart of Brew City.
                </p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white p-3 rounded shadow-sm text-center">
                            <i class="bi bi-wifi fs-2 text-warning mb-2"></i>
                            <p class="mb-0 small fw-semibold">Free WiFi</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white p-3 rounded shadow-sm text-center">
                            <i class="bi bi-p-square fs-2 text-warning mb-2"></i>
                            <p class="mb-0 small fw-semibold">Free Parking</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white p-3 rounded shadow-sm text-center">
                            <i class="bi bi-power fs-2 text-warning mb-2"></i>
                            <p class="mb-0 small fw-semibold">Power Outlets</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white p-3 rounded shadow-sm text-center">
                            <i class="bi bi-book fs-2 text-warning mb-2"></i>
                            <p class="mb-0 small fw-semibold">Reading Area</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 text-white text-center" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);">
    <div class="container">
        <h2 class="display-6 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
            Ready to Taste the Difference?
        </h2>
        <p class="lead mb-4">Join our community of coffee enthusiasts today</p>
        <a href="../pages/order.php" class="btn btn-warning btn-lg px-5" style="border-radius: 25px;">
            <i class="bi bi-cart-fill me-2"></i>Order Now
        </a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>