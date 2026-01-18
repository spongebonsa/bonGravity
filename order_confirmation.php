<?php
define('PAGE_TITLE', 'Order Confirmed');
require_once 'includes/header.php';

$order_number = isset($_GET['order']) ? sanitize($_GET['order']) : '';
?>

<div class="container text-center" style="padding: 6rem 1.5rem;">
    <div style="font-size: 5rem; margin-bottom: 2rem;">✅</div>
    <h1 class="mb-2">Order Confirmed!</h1>
    <p style="font-size: 1.25rem; color: var(--text-light); margin-bottom: 2rem;">
        Thank you for your purchase. <br>
        Your order <strong><?php echo $order_number; ?></strong> has been received.
    </p>
    
    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        <?php if(is_logged_in()): ?>
            <a href="my-account.php" class="btn btn-outline">View Order</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
