<?php
require_once 'auth_check.php';
define('PAGE_TITLE', 'Checkout');
require_once 'includes/header.php';

if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Calculate Total
$total = 0;
$cart_details = [];
$ids = array_keys($_SESSION['cart']);
if ($ids) {
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $total += $p['price'] * $qty;
        $cart_details[] = ['name' => $p['name'], 'qty' => $qty, 'price' => $p['price']];
    }
}
?>

<div class="container" style="padding: 4rem 1.5rem;">
    <h1 class="mb-4">Checkout</h1>
    
    <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
        <!-- Checkout Form -->
        <div style="flex: 2; min-width: 300px;">
            <form action="process_order.php" method="POST" id="checkoutForm">
                
                <!-- Section 1: Contact -->
                <div class="mb-4" style="background: white; border-radius: var(--radius); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h3 class="mb-2">1. Contact Information</h3>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?php echo is_logged_in() && isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>">
                    </div>
                </div>
                
                <!-- Section 2: Shipping -->
                <div class="mb-4" style="background: white; border-radius: var(--radius); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h3 class="mb-2">2. Shipping Address</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Zip</label>
                            <input type="text" name="zip" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <!-- Section 3: Payment -->
                <div class="mb-4" style="background: white; border-radius: var(--radius); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h3 class="mb-2">3. Payment</h3>
                    <div class="form-group">
                        <label class="form-label">Card Number (Demo: 4242...)</label>
                        <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Expiry</label>
                            <input type="text" name="expiry" class="form-control" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">CVC</label>
                            <input type="text" name="cvc" class="form-control" placeholder="123" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.2rem;">Place Order</button>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div style="flex: 1; min-width: 300px;">
            <div style="background: #f8fafc; border-radius: var(--radius); padding: 2rem; position: sticky; top: 120px;">
                <h3 class="mb-2">Order Summary</h3>
                <div style="margin-bottom: 2rem;">
                    <?php foreach ($cart_details as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                            <span><?php echo $item['qty']; ?>x <?php echo sanitize($item['name']); ?></span>
                            <span><?php echo format_price($item['price'] * $item['qty']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.25rem;">
                        <span>Total</span>
                        <span><?php echo format_price($total); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
