<?php
// Ensure functions are loaded
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('PAGE_TITLE') ? PAGE_TITLE . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body>

<header>
    <div class="container">
        <a href="<?php echo APP_URL; ?>" class="logo">
            BON
        </a>
        
        <nav>
            <ul>
                <li><a href="<?php echo APP_URL; ?>/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo APP_URL; ?>/shop.php" class="<?php echo $current_page == 'shop.php' ? 'active' : ''; ?>">Shop</a></li>
                <li><a href="<?php echo APP_URL; ?>/about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="<?php echo APP_URL; ?>/locations.php" class="<?php echo $current_page == 'locations.php' ? 'active' : ''; ?>">Locations</a></li>
                <li><a href="<?php echo APP_URL; ?>/contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
            </ul>
        </nav>
        
        <div class="header-actions">
            <a href="<?php echo APP_URL; ?>/cart.php" class="cart-icon">
                🛒
                <?php 
                $cart_count = get_cart_count();
                if ($cart_count > 0): 
                ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
            
            <?php if (is_logged_in()): ?>
                <a href="<?php echo APP_URL; ?>/my-account.php" class="btn btn-primary">My Account</a>
            <?php else: ?>
                <a href="<?php echo APP_URL; ?>/login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <?php 
    $flash = get_flash_message();
    if ($flash): 
    ?>
        <div class="container" style="padding-top: 2rem;">
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        </div>
    <?php endif; ?>
