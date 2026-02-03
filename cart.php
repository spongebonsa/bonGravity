<?php
define('PAGE_TITLE', 'Shopping Cart');
require_once 'includes/header.php';

// Fetch Cart Data
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    if (count($ids) > 0) {
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();
        
        foreach ($products as $product) {
            $qty = $_SESSION['cart'][$product['id']];
            $subtotal = $product['price'] * $qty;
            $total += $subtotal;
            
            $product['qty'] = $qty;
            $product['subtotal'] = $subtotal;
            $cart_items[] = $product;
        }
    }
}

$shipping = 5.99;
if ($total >= 50) {
    $shipping = 0;
}
$grand_total = $total + $shipping;
?>

<div class="container" style="padding: 3rem 0;">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Start shopping to see your items here</p>
            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo APP_URL; ?>/uploads/<?php echo $item['image_url']; ?>" alt="<?php echo sanitize($item['name']); ?>">
                            <?php else: ?>
                                🥤
                            <?php endif; ?>
                        </div>
                        <div class="cart-item-details">
                            <h3><?php echo sanitize($item['name']); ?></h3>
                            <div class="price"><?php echo format_product_price($item['price'], $item['currency'] ?? null); ?></div>
                            <div class="quantity-controls">
                                <form action="api/cart_actions.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="decrease">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit">−</button>
                                </form>
                                <span><?php echo $item['qty']; ?></span>
                                <form action="api/cart_actions.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="increase">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit">+</button>
                                </form>
                            </div>
                        </div>
                        <div style="margin-left: auto; text-align: right;">
                            <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;"><?php echo format_product_price($item['subtotal'], $item['currency'] ?? null); ?></div>
                            <form action="api/cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" style="background: none; border: none; color: #94A3B8; cursor: pointer;">🗑️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                    <span><?php echo format_price($total); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $shipping > 0 ? format_price($shipping) : 'Free'; ?></span>
                </div>
                <?php if ($total < 50): ?>
                    <p style="color: var(--primary-blue); font-size: 0.85rem; margin: 0.5rem 0;">Add $<?php echo number_format(50 - $total, 2); ?> more for free shipping!</p>
                <?php endif; ?>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo format_price($grand_total); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    Proceed to Checkout →
                </a>
                <a href="shop.php" class="btn btn-secondary" style="width: 100%; margin-top: 0.75rem; text-align: center;">Continue Shopping</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Newsletter -->
<section class="newsletter">
    <div class="container">
        <h3>Stay Updated</h3>
        <p>Subscribe to get special offers, free giveaways, and new arrivals.</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email" required>
            <button type="submit">→</button>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
