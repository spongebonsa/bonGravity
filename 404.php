<?php
define('PAGE_TITLE', '404 - Page Not Found');
require_once 'includes/header.php';
?>

<div class="container" style="padding: 6rem 0; text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
        <!-- 404 Illustration -->
        <div style="font-size: 10rem; font-weight: 900; color: var(--primary-blue); line-height: 1; margin-bottom: 1rem;">
            404
        </div>
        
        <!-- Error Message -->
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-dark);">Page Not Found</h1>
        <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 3rem;">
            Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
        </p>
        
        <!-- Emoji Icon -->
        <div style="font-size: 5rem; margin-bottom: 2rem;">🥤</div>
        
        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                🏠 Back to Home
            </a>
            <a href="<?php echo APP_URL; ?>/shop.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                🛒 Browse Products
            </a>
        </div>
        
        <!-- Helpful Links -->
        <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border-light);">
            <p style="color: var(--text-muted); margin-bottom: 1rem;">You might be looking for:</p>
            <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo APP_URL; ?>/shop.php" style="color: var(--primary-blue);">Shop</a>
                <a href="<?php echo APP_URL; ?>/about.php" style="color: var(--primary-blue);">About Us</a>
                <a href="<?php echo APP_URL; ?>/contact.php" style="color: var(--primary-blue);">Contact</a>
                <a href="<?php echo APP_URL; ?>/my-account.php" style="color: var(--primary-blue);">My Account</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
