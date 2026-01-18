<?php
ob_start(); // Buffer output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check Admin Auth
// Exclude login page from check
if (!strpos($_SERVER['PHP_SELF'], 'login.php')) {
    if (!is_logged_in() || !is_admin()) {
        header("Location: " . APP_URL . "/admin/login.php");
        exit;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo APP_NAME; ?></title>

    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css"> <!-- Base styles -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/admin.css"> <!-- Admin overrides -->
</head>
<body class="admin-body">

<?php if (!strpos($_SERVER['PHP_SELF'], 'login.php')): ?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        BON Admin
    </div>
    <nav class="admin-nav">
        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">📊 Dashboard</a>
        <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">📦 Products</a>
        <a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">📁 Categories</a>
        <a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">🛒 Orders</a>
        <a href="users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">👥 Users</a>
        <a href="locations.php" class="<?php echo $current_page == 'locations.php' ? 'active' : ''; ?>">📍 Locations</a>
        <a href="enquiries.php" class="<?php echo $current_page == 'enquiries.php' ? 'active' : ''; ?>">📨 Enquiries</a>
        <a href="<?php echo APP_URL; ?>/logout.php">🚪 Logout</a>
    </nav>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <h2><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h2>
        <div>
            User: <?php echo $_SESSION['user_name']; ?>
        </div>
    </header>
    
    <div class="admin-content">
        <?php 
        $flash = get_flash_message();
        if ($flash): 
        ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>
<?php endif; ?>
