<?php
define('PAGE_TITLE', 'Home');
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-home">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <p class="hero-label">It's The Drink You Need</p>
                <h1>Feels<br><span class="highlight">Better</span></h1>
                <p class="hero-description">A healthy meal replacement you can drink anytime, anywhere. Low in sugar, high in protein and full of 26 essential vitamins.</p>
                <div class="hero-buttons">
                    <a href="shop.php" class="btn btn-primary">Shop Now</a>
                    <a href="about.php" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?php echo APP_URL; ?>/assets/images/can_pure_alpine.png" alt="BON Pure Alpine" class="can-display">
            </div>
        </div>
    </div>
    <div class="wave-decoration"></div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Billboard</h3>
                <p>Advertise your brand with us and reach thousands of customers.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Packages</h3>
                <p>We provide the best packages for your business needs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🍎</div>
                <h3>Nutrient</h3>
                <p>Full of essential vitamins and minerals for your health.</p>
            </div>
        </div>
    </div>
</section>

<!-- Shop by Category -->
<section class="categories">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Explore our wide range of products</p>
        </div>
        
        <div class="category-grid">
            <div class="category-card orange-gradient">
                <div class="category-icon">🏃</div>
                <h3>Sporting Goods</h3>
                <p>Get fit and stay active</p>
                <a href="shop.php?category=sporting" class="category-link">Shop Now →</a>
            </div>
            
            <div class="category-card green-gradient">
                <div class="category-icon">🌸</div>
                <h3>Fresh Flowers</h3>
                <p>Beautiful blooms delivered</p>
                <a href="shop.php?category=flowers" class="category-link">Shop Now →</a>
            </div>
            
            <div class="category-card blue-gradient">
                <div class="category-icon">💼</div>
                <h3>Gym Bag</h3>
                <p>Carry your essentials</p>
                <a href="shop.php?category=gym" class="category-link">Shop Now →</a>
            </div>
            
            <div class="category-card purple-gradient">
                <div class="category-icon">🛒</div>
                <h3>Grocery Boost</h3>
                <p>Fresh groceries daily</p>
                <a href="shop.php?category=grocery" class="category-link">Shop Now →</a>
            </div>
            
            <div class="category-card yellow-gradient">
                <div class="category-icon">🍊</div>
                <h3>Healthy Bites</h3>
                <p>Nutritious snacks</p>
                <a href="shop.php?category=healthy" class="category-link">Shop Now →</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Products</h2>
            <p>Check out our best sellers</p>
        </div>
        
        <?php
        // Fetch featured products from database
        $featured_stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 AND p.status = 'active' LIMIT 6");
        $featured_products = $featured_stmt->fetchAll();
        ?>
        
        <div class="product-grid">
            <?php if (count($featured_products) > 0): ?>
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card">
                        <?php if ($product['sale_price']): ?>
                            <span class="sale-badge">SALE</span>
                        <?php endif; ?>
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <div class="product-image">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo APP_URL; ?>/uploads/<?php echo $product['image_url']; ?>" alt="<?php echo sanitize($product['name']); ?>">
                                <?php else: ?>
                                    <span>🥤</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <p class="product-category"><?php echo strtoupper(sanitize($product['category_name'])); ?></p>
                                <h3><?php echo sanitize($product['name']); ?></h3>
                                <p class="product-price"><?php echo format_price($product['sale_price'] ?: $product['price']); ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback if no featured products -->
                <p style="text-align: center; color: var(--text-muted);">No featured products available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-orange">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <h2>Refreshment That<br>Feels Better</h2>
                <p>Try our delicious flavors and feel the difference. Made with natural ingredients and packed with vitamins.</p>
                <div class="rating-stars">
                    <span>⭐⭐⭐⭐⭐</span>
                    <span class="rating-text">4.8/5.0 (2,157 reviews)</span>
                </div>
            </div>
            <div class="cta-image">
                <img src="<?php echo APP_URL; ?>/assets/images/can_mango.png" alt="BON Mango" class="can-display-large">
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2>What People Say</h2>
            <p>Don't just take our word for it</p>
        </div>
        
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"This is the best energy drink I've ever had! No crash, just smooth energy all day long."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">👤</div>
                    <div class="author-info">
                        <h4>Sarah M.</h4>
                        <p>Verified Buyer</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"Love the taste and the fact that it's healthy. Perfect for my morning routine!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">👤</div>
                    <div class="author-info">
                        <h4>Mike R.</h4>
                        <p>Verified Buyer</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"Finally, a drink that tastes great and is actually good for you. Highly recommend!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">👤</div>
                    <div class="author-info">
                        <h4>Emma L.</h4>
                        <p>Verified Buyer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter-section">
    <div class="container">
        <h3>Stay Updated</h3>
        <p>Get the latest news and exclusive offers delivered to your inbox</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit" class="btn btn-white">Subscribe</button>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
