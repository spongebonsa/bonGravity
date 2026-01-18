<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Data from form
$email = sanitize($_POST['email']);
$first_name = sanitize($_POST['first_name']);
$last_name = sanitize($_POST['last_name']);
$address = sanitize($_POST['address']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip = sanitize($_POST['zip']);
// Payment info is collected but not processed in this demo

$full_address = "$first_name $last_name\n$address\n$city, $state $zip";

try {
    $pdo->beginTransaction();
    
    // 1. Calculate Total & Prepare items
    $total = 0;
    $order_items = [];
    $ids = array_keys($_SESSION['cart']);
    
    if (empty($ids)) throw new Exception("Cart is empty");
    
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        if ($p['stock'] < $qty) {
            throw new Exception("Product " . $p['name'] . " is out of stock");
        }
        
        $total += $p['price'] * $qty;
        $order_items[] = [
            'product_id' => $p['id'],
            'quantity' => $qty,
            'price' => $p['price']
        ];
        
        // Update Stock
        $update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stock->execute([$qty, $p['id']]);
    }
    
    // 2. Create Order
    $order_number = 'ORD-' . strtoupper(uniqid());
    $user_id = is_logged_in() ? $_SESSION['user_id'] : null;
    
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_email, shipping_address, total, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$order_number, $user_id, $email, $full_address, $total]);
    $order_id = $pdo->lastInsertId();
    
    // 3. Insert Items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($order_items as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }
    
    // 4. Clear Cart
    unset($_SESSION['cart']);
    if (is_logged_in()) {
        // Clear DB cart (using user binding if I had it, but using session_id for now)
        // Note: Logic hole if cleaning up by session_id but user might have multiple sessions.
        // But per `api/cart_actions.php` logic:
        $sid = session_id();
        $del = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ?");
        $del->execute([$sid]);
    }
    
    $pdo->commit();
    
    // Redirect to success
    redirect('order_confirmation.php?order=' . $order_number);
    
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash_message("Order Failed: " . $e->getMessage(), "danger");
    redirect('checkout.php');
}
?>
