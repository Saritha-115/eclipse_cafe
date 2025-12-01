<footer class="mt-5 py-5 text-white" style="background: linear-gradient(135deg, #2C1810 0%, #8B4513 100%);">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3" style="font-family: 'Playfair Display', serif;">
                        <i class="bi bi-cup-hot-fill me-2"></i>Eclipse Café
                    </h5>
                    <p class="text-light opacity-75">
                        Experience the perfect blend of premium coffee and exceptional service. 
                        Your daily dose of happiness, crafted with love.
                    </p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/eclipse_cafe/index.php" class="text-light text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/eclipse_cafe/pages/menu.php" class="text-light text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Menu
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/eclipse_cafe/pages/about.php" class="text-light text-decoration-none">
                                <i class="bi bi-chevron-right"></i> About Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/eclipse_cafe/pages/contact.php" class="text-light text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Contact
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/eclipse_cafe/admin/admin_login.php" class="text-light text-decoration-none">
                                <i class="bi bi-chevron-right"></i> Admin Login
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Contact Us</h5>
                    <ul class="list-unstyled text-light">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                            123 Coffee Street, Brew City, BC 12345
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone-fill text-warning me-2"></i>
                            +1 (555) 123-4567
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope-fill text-warning me-2"></i>
                            info@eclipsecafe.com
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock-fill text-warning me-2"></i>
                            Mon-Fri: 7AM - 9PM<br>
                            <span class="ms-4">Sat-Sun: 8AM - 10PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-light opacity-25 my-4">
            
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-light opacity-75">
                        &copy; <?php echo date('Y'); ?> Eclipse Café. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-light opacity-75">
                        Crafted with <i class="bi bi-heart-fill text-danger"></i> for coffee lovers
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/eclipse_cafe/js/validation.js"></script>
    
    <?php if (isset($extra_scripts)): ?>
        <?php echo $extra_scripts; ?>
    <?php endif; ?>
</body>
</html>