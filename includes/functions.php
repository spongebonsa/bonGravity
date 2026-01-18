<?php
// Helper Functions

/**
 * Sanitize user input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format price with currency
 */
function format_price($price) {
    return CURRENCY . number_format($price, 2);
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirect and exit
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Flash message helper
 */
function set_flash_message($message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Get Cart Total Count
 */
function get_cart_count() {
    global $pdo;
    $count = 0;
    
    // If logged in, check database
    if (is_logged_in()) {
        // Basic implementation for now - logic handled in cart actions usually
        // But for display count:
        // Merge session cart logic if complex, but simple version:
    }
    
    // Check session cart
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $count += $qty;
        }
    }
    
    return $count;
}
?>
