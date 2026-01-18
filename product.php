<?php
define('PAGE_TITLE', 'Product Details');
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container' style='padding: 4rem;'><p>Product not found.</p></div>";
    require_once 'includes/footer.php';
    exit;
}

// Get related products from the same category
$related_stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' LIMIT 3");
$related_stmt->execute([$product['category_id'], $product['id']]);
$related_products = $related_stmt->fetchAll();
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-nav">
            <a href="index.php">Home</a>
            <span>›</span>
            <a href="shop.php">Shop</a>
            <span>›</span>
            <span class="current"><?php echo sanitize($product['name']); ?></span>
        </div>
    </div>
</div>

<!-- Product Detail -->
<div class="product-detail">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Left Column: Image + Tabs -->
            <div>
                <div class="product-detail-image">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?php echo APP_URL; ?>/uploads/<?php echo $product['image_url']; ?>" alt="<?php echo sanitize($product['name']); ?>">
                <?php else: ?>
                    <span class="emoji">🥤</span>
                <?php endif; ?>
                </div>

                <!-- Product Tabs - Moved under image -->
                <div class="product-tabs" style="margin-top: 2rem;">
                    <div class="tab-buttons">
                        <button class="tab-button active" onclick="switchTab('description')">Description</button>
                        <button class="tab-button" onclick="switchTab('ingredients')">Ingredients</button>
                    </div>

                    <div id="description-tab" class="tab-content active">
                        <p><?php echo nl2br(sanitize($product['description'])); ?></p>
                    </div>

                    <div id="ingredients-tab" class="tab-content">
                        <?php if (!empty($product['ingredients'])): ?>
                            <p><?php echo nl2br(sanitize($product['ingredients'])); ?></p>
                        <?php else: ?>
                            <p>Pure sparkling water, natural fruit extracts, essential vitamins and minerals. No added sugar, no artificial sweeteners, no preservatives.</p>
                        <?php endif; ?>
                    </div>


                </div>
            </div>

            <!-- Product Info -->
            <div class="product-detail-info">
                <h1><?php echo sanitize($product['name']); ?></h1>
                
                <!-- Star Rating -->
                <div class="rating-info">
                    <div class="star-rating">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                    </div>

                </div>

                <div class="product-detail-price"><?php echo format_price($product['price']); ?></div>

                <p class="product-description"><?php echo nl2br(sanitize($product['description'])); ?></p>

                <?php if ($product['stock'] > 0): ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User is logged in - show add to cart form -->
                        <form action="api/cart_actions.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <!-- Quantity Selector -->
                            <div class="quantity-selector">
                                <label>Quantity:</label>
                                <div class="quantity-controls-inline">
                                    <button type="button" onclick="decrementQty()">−</button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                                    <button type="button" onclick="incrementQty()">+</button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="add-to-cart-section">
                                <button type="submit" class="btn-add-cart">
                                    🛒 Add to Cart
                                </button>
                                <button type="button" class="btn-wishlist">♡</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- User not logged in - show login prompt -->
                        <div style="background: #FEF9C3; border: 1px solid #FCD34D; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <p style="color: #854D0E; margin-bottom: 0.5rem; font-weight: 600;">Please login to add items to cart</p>
                            <a href="login.php" class="btn btn-primary">Login to Continue</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color: #DC2626; font-weight: 600; font-size: 1.2rem;">Out of Stock</p>
                <?php endif; ?>

                <!-- Product Features -->
                <div class="product-features">
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <div class="feature-text">Free Shipping<br><small style="color: var(--text-muted);">Orders over $50</small></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🔒</div>
                        <div class="feature-text">Secure Payment<br><small style="color: var(--text-muted);">100% protected</small></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">↻</div>
                        <div class="feature-text">Easy Returns<br><small style="color: var(--text-muted);">30-day policy</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- You May Also Like Section -->
<?php if (count($related_products) > 0): ?>
<div class="related-products">
    <div class="container">
        <h2>You May Also Like</h2>
        <div class="related-grid">
            <?php foreach ($related_products as $related): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $related['id']; ?>">
                        <div class="product-image">
                            <?php if (!empty($related['image_url'])): ?>
                                <img src="<?php echo APP_URL; ?>/uploads/<?php echo $related['image_url']; ?>" alt="<?php echo sanitize($related['name']); ?>">
                            <?php else: ?>
                                <span>🥤</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo ucfirst(sanitize($related['category_name'])); ?></span>
                            <h3><?php echo sanitize($related['name']); ?></h3>
                            <span class="product-price"><?php echo format_price($related['price']); ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Quantity controls
function incrementQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Tab switching
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

// Wishlist functionality
const wishlistBtn = document.querySelector('.btn-wishlist');
const productId = <?php echo $product['id']; ?>;

// Check if product is in wishlist on page load
<?php if (isset($_SESSION['user_id'])): ?>
fetch('<?php echo APP_URL; ?>/api/wishlist_actions.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=check&product_id=' + productId
})
.then(response => response.json())
.then(data => {
    if (data.success && data.in_wishlist) {
        wishlistBtn.innerHTML = '♥';
        wishlistBtn.style.color = '#EF4444';
        wishlistBtn.dataset.inWishlist = 'true';
    }
});
<?php endif; ?>

// Wishlist button click handler
wishlistBtn.addEventListener('click', function(e) {
    e.preventDefault();
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        alert('Please login to add items to your wishlist');
        window.location.href = '<?php echo APP_URL; ?>/login.php';
        return;
    <?php endif; ?>
    
    const inWishlist = this.dataset.inWishlist === 'true';
    const action = inWishlist ? 'remove' : 'add';
    
    fetch('<?php echo APP_URL; ?>/api/wishlist_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=' + action + '&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.in_wishlist) {
                this.innerHTML = '♥';
                this.style.color = '#EF4444';
                this.dataset.inWishlist = 'true';
            } else {
                this.innerHTML = '♡';
                this.style.color = '';
                this.dataset.inWishlist = 'false';
            }
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
