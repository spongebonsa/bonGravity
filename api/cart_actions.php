<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Initialize session cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'] ?? 1;
        
        // Validate Stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product && $product['stock'] >= $quantity) {
            // Update Session
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            // Sync with DB if logged in
            if (is_logged_in()) {
                sync_cart_db($pdo, $product_id, $_SESSION['cart'][$product_id]);
            }
            
            set_flash_message("Item added to cart!", "success");
        } else {
            set_flash_message("Not enough stock!", "danger");
        }
    } 
    elseif ($action === 'increase') {
        $product_id = (int)$_POST['product_id'];
        
        // Validate Stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        $current_qty = $_SESSION['cart'][$product_id] ?? 0;
        $new_qty = $current_qty + 1;
        
        if ($product && $product['stock'] >= $new_qty) {
            $_SESSION['cart'][$product_id] = $new_qty;
            if (is_logged_in()) {
                sync_cart_db($pdo, $product_id, $new_qty);
            }
            set_flash_message("Cart updated.", "success");
        } else {
            set_flash_message("Not enough stock!", "danger");
        }
    }
    elseif ($action === 'decrease') {
        $product_id = (int)$_POST['product_id'];
        $current_qty = $_SESSION['cart'][$product_id] ?? 0;
        $new_qty = $current_qty - 1;
        
        if ($new_qty > 0) {
            $_SESSION['cart'][$product_id] = $new_qty;
            if (is_logged_in()) {
                sync_cart_db($pdo, $product_id, $new_qty);
            }
            set_flash_message("Cart updated.", "success");
        } else {
            unset($_SESSION['cart'][$product_id]);
            if (is_logged_in()) {
                remove_cart_db($pdo, $product_id);
            }
            set_flash_message("Item removed.", "success");
        }
    }
    elseif ($action === 'update') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
            if (is_logged_in()) {
                sync_cart_db($pdo, $product_id, $quantity);
            }
            set_flash_message("Cart updated.", "success");
        } else {
            // Remove if 0
            unset($_SESSION['cart'][$product_id]);
            if (is_logged_in()) {
                remove_cart_db($pdo, $product_id);
            }
            set_flash_message("Item removed.", "success");
        }
    } 
    elseif ($action === 'remove') {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        if (is_logged_in()) {
            remove_cart_db($pdo, $product_id);
        }
        set_flash_message("Item removed.", "success");
    }
    
    // Redirect back
    $redirect = $_SERVER['HTTP_REFERER'] ?? '../shop.php';
    if (strpos($redirect, 'cart.php') === false && $action === 'add') {
        // Stay on shop/product page if adding
    } else {
        // Go to cart if updating/removing usually, but let's just go back
    }
    header("Location: " . $redirect);
    exit;
}

// Helper to sync single item
function sync_cart_db($pdo, $product_id, $quantity) {
    if (!isset($_SESSION['user_uid'])) { // Assuming user_id is set in session on login
         // Actually functions.php used isset($_SESSION['user_id'])
         if (!isset($_SESSION['user_id'])) return;
    }
    $user_id = $_SESSION['user_id'];
    $session_id = session_id(); // Or use user_id logic. 
    // Requirement says cart_items table has session_id. 
    // Usually for logged in users we bind to user_id, 
    // but the schema has `session_id` column in `cart_items` but NO `user_id`.
    // Wait, let's check schema provided in prompt?
    // "cart_items (id, session_id, product_id, quantity)" -> It links by session_id? 
    // If so, we just use session_id for everyone? 
    // But persistence implies user account. 
    // I should probably have added user_id to cart_items or use session_id as the persistent key.
    
    // Let's use session_id for now as per schema in prompt, 
    // but maybe update schema to include user_id if I can? 
    // The prompt was "Create these tables user_id... cart_items(id, session_id...)".
    // I will stick to schema. 
    // If user logs in, I should probably update the session_id in the DB to match their new session 
    // OR just use the session cookie which stays.
    
    // For now, I'll just upsert based on session_id.
    
    $sid = session_id();
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM cart_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$sid, $product_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $update = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update->execute([$quantity, $existing['id']]);
    } else {
        $insert = $pdo->prepare("INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->execute([$sid, $product_id, $quantity]);
    }
}

function remove_cart_db($pdo, $product_id) {
    $sid = session_id();
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$sid, $product_id]);
}
?>
