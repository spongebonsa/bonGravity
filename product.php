<?php
define('PAGE_TITLE', 'Product Details');
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// Detect if `reviews` table exists (used before handling POST to avoid fatal errors)
$has_reviews = false;
try {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'reviews'");
    $chk->execute([DB_NAME]);
    $has_reviews = $chk->fetchColumn() > 0;
} catch (Exception $e) {
    $has_reviews = false;
}
// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        set_flash_message('Please login to submit a review', 'danger');
        redirect('login.php');
    }

    $rating = (int)($_POST['rating'] ?? 0);
    $review_text = sanitize($_POST['review'] ?? '');

    if ($rating < 1 || $rating > 5 || empty($review_text)) {
        set_flash_message('Please provide a valid rating (1-5) and a review message.', 'danger');
        redirect('product.php?id=' . $id);
    }

    if (!$has_reviews) {
        // Reviews are not available on this installation — avoid setting a persistent flash
        // and silently redirect so users don't see the message across other pages.
        redirect('product.php?id=' . $id);
    }

    $ins = $pdo->prepare('INSERT INTO reviews (product_id, user_id, rating, review) VALUES (?, ?, ?, ?)');
    $ins->execute([$id, $_SESSION['user_id'], $rating, $review_text]);
    set_flash_message('Thank you for your review!', 'success');
    redirect('product.php?id=' . $id);
}

if (!$product) {
    echo "<div class='container' style='padding: 4rem;'><p>Product not found.</p></div>";
    require_once 'includes/footer.php';
    exit;
}

// Get related products from the same category
$related_stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' LIMIT 3");
$related_stmt->execute([$product['category_id'], $product['id']]);
$related_products = $related_stmt->fetchAll();

// Fetch reviews for this product (if reviews table exists)
if ($has_reviews) {
    $reviews_stmt = $pdo->prepare("SELECT r.*, u.full_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
    $reviews_stmt->execute([$id]);
    $reviews = $reviews_stmt->fetchAll();

    // Compute average rating for display
    $avg_stmt = $pdo->prepare("SELECT IFNULL(ROUND(AVG(rating),2),0) as avg_rating, COUNT(*) as review_count FROM reviews WHERE product_id = ?");
    $avg_stmt->execute([$id]);
    $avg_data = $avg_stmt->fetch();
    $avg_rating = $avg_data ? (float)$avg_data['avg_rating'] : 0;
    $review_count = $avg_data ? (int)$avg_data['review_count'] : 0;
} else {
    $reviews = [];
    $avg_rating = 0;
    $review_count = 0;
}
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

                <div class="product-detail-price"><?php echo format_product_price($product['price'], $product['currency'] ?? null); ?></div>
                <div style="margin-top:8px; color:#FFB02E; font-size:0.95rem;">
                    <?php for ($s=1;$s<=5;$s++): ?>
                        <?php if ($avg_rating >= $s-0.25): ?>
                            <span>★</span>
                        <?php else: ?>
                            <span style="color:#E5E7EB">★</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <span style="color:#64748B; font-size:0.9rem; margin-left:8px;"><?php echo $avg_rating; ?> (<?php echo $review_count; ?>)</span>
                </div>

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

                <!-- Reviews Section -->
                <div style="margin-top: 2.5rem;">
                    <h3>Customer Reviews</h3>
                    <?php if ($has_reviews && count($reviews) > 0): ?>
                        <div id="reviews-list" style="margin-top:1rem;">
                            <?php foreach ($reviews as $rv): ?>
                                <div class="review-item" style="padding:0.75rem 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="font-weight:700"><?php echo sanitize($rv['full_name'] ?: 'Anonymous'); ?></div>
                                    <div style="color:#FFB02E;">
                                        <?php for ($s=1;$s<=5;$s++): ?>
                                            <?php echo $rv['rating'] >= $s ? '★' : '<span style="color:#E5E7EB">★</span>'; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <div style="color:#374151; margin-top:6px;" class="review-text"><?php echo nl2br(sanitize($rv['review'])); ?></div>
                                    <div style="color:#9CA3AF; font-size:0.85rem; margin-top:6px;" class="review-date"><?php echo date('M d, Y H:i', strtotime($rv['created_at'])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div id="reviews-list"><p style="color:var(--text-muted); margin-top:0.5rem;">No reviews yet — be the first to review!</p></div>
                    <?php endif; ?>

                    <?php if ($has_reviews): ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" style="margin-top:1.25rem; max-width:600px;">
                            <h4>Write a review</h4>
                            <div class="form-group">
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-control" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Your review</label>
                                <textarea name="review" class="form-control" rows="4" required></textarea>
                            </div>
                            <div style="margin-top:0.75rem;">
                                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                            </div>
                        </form>
                            <script>
                            (function(){
                                const form = document.getElementById('review-form');
                                const submitBtn = document.getElementById('submit-review');
                                form.addEventListener('submit', function(e){
                                    e.preventDefault();
                                    submitBtn.disabled = true;
                                    const rating = document.getElementById('rating').value;
                                    const review = document.getElementById('review-text').value;

                                    fetch('<?php echo APP_URL; ?>/api/review_actions.php', {
                                        method: 'POST',
                                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                        body: 'product_id=<?php echo $product['id']; ?>&rating='+encodeURIComponent(rating)+'&review='+encodeURIComponent(review)
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        submitBtn.disabled = false;
                                        if (data.success && data.review) {
                                            // Append new review to list
                                            const list = document.getElementById('reviews-list');
                                            const div = document.createElement('div');
                                            div.className = 'review-item';
                                            div.style.padding = '0.75rem 0';
                                            div.style.borderBottom = '1px solid #f1f5f9';
                                            const name = document.createElement('div');
                                            name.style.fontWeight = '700';
                                            name.textContent = data.review.full_name || 'You';
                                            const stars = document.createElement('div');
                                            stars.style.color = '#FFB02E';
                                            for (let s=1;s<=5;s++) stars.innerHTML += (data.review.rating >= s) ? '★' : '<span style="color:#E5E7EB">★</span>';
                                            const text = document.createElement('div');
                                            text.style.color = '#374151';
                                            text.style.marginTop = '6px';
                                            text.innerHTML = (data.review.review || '').replace(/\n/g, '<br>');
                                            const date = document.createElement('div');
                                            date.style.color = '#9CA3AF';
                                            date.style.fontSize = '0.85rem';
                                            date.style.marginTop = '6px';
                                            date.textContent = (new Date()).toLocaleString();

                                            div.appendChild(name);
                                            div.appendChild(stars);
                                            div.appendChild(text);
                                            div.appendChild(date);

                                            // If placeholder 'No reviews yet' exists, replace content
                                            if (list.children.length === 1 && list.children[0].tagName === 'P') {
                                                list.innerHTML = '';
                                            }
                                            list.insertBefore(div, list.firstChild);
                                            // Clear form
                                            document.getElementById('review-text').value = '';
                                            document.getElementById('rating').value = '5';
                                        } else {
                                            alert(data.message || 'Failed to submit review');
                                        }
                                    })
                                    .catch(err => {
                                        submitBtn.disabled = false;
                                        console.error(err);
                                        alert('An error occurred. Please try again.');
                                    });
                                });
                            })();
                            </script>
                        <?php else: ?>
                            <p style="margin-top:0.75rem;">Please <a href="login.php">login</a> to write a review.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="margin-top:0.75rem; color: var(--text-muted);">Reviews are currently unavailable.</p>
                    <?php endif; ?>
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
                            <span class="product-price"><?php echo format_price($related['price'], $related['currency'] ?? null); ?></span>
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
