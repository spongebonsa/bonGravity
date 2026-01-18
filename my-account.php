<?php
define('PAGE_TITLE', 'My Account');
require_once 'includes/header.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$view = isset($_GET['view']) ? $_GET['view'] : 'profile';

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="container account-container">
    <!-- Sidebar -->
    <div class="account-sidebar">
        <div class="account-profile">
            <div class="account-avatar">😊</div>
            <h3><?php echo sanitize($user['full_name']); ?></h3>
            <p><?php echo sanitize($user['email']); ?></p>
        </div>
        
        <nav class="account-nav">
            <a href="?view=profile" class="<?php echo $view == 'profile' ? 'active' : ''; ?>">
                👤 Profile
            </a>
            <a href="?view=orders" class="<?php echo $view == 'orders' ? 'active' : ''; ?>">
                📦 My Orders
            </a>
            <a href="?view=wishlist" class="<?php echo $view == 'wishlist' ? 'active' : ''; ?>">
                ❤️ Wishlist
            </a>
            <a href="logout.php" class="logout">
                🚪 Logout
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="account-content">
        <?php if ($view == 'profile'): ?>
            <?php
            // Handle profile update
            $edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';
            $update_success = false;
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
                $new_name = sanitize($_POST['full_name']);
                
                if (!empty($new_name)) {
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
                    if ($stmt->execute([$new_name, $user_id])) {
                        $update_success = true;
                        // Refresh user data
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetch();
                        $edit_mode = false;
                    }
                }
            }
            ?>
            
            <?php if ($update_success): ?>
                <div class="alert alert-success">Profile updated successfully!</div>
            <?php endif; ?>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>Profile Information</h2>
                <?php if (!$edit_mode): ?>
                    <a href="?view=profile&edit=1" class="btn btn-secondary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">✏️ Edit</a>
                <?php else: ?>
                    <a href="?view=profile" class="btn btn-secondary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">✖ Cancel</a>
                <?php endif; ?>
            </div>
            
            <?php if ($edit_mode): ?>
                <!-- Edit Mode -->
                <form method="POST" action="?view=profile">
                    <div class="profile-info">
                        <div class="info-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo sanitize($user['full_name']); ?>" required>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo sanitize($user['email']); ?></div>
                            <small style="color: var(--text-muted); font-size: 0.85rem;">Email cannot be changed</small>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Phone</div>
                            <div class="info-value">-</div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Address</div>
                            <div class="info-value">-</div>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary" style="margin-top: 1.5rem;">💾 Save Changes</button>
                </form>
            <?php else: ?>
                <!-- View Mode -->
                <div class="profile-info">
                    <div class="info-group">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo sanitize($user['full_name']); ?></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo sanitize($user['email']); ?></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Phone</div>
                        <div class="info-value">-</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Address</div>
                        <div class="info-value">-</div>
                    </div>
                </div>
            <?php endif; ?>

            <div style="margin-top: 3rem; border-top: 1px solid var(--border-light); padding-top: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Change Password</h3>
                
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
                    $current_password = $_POST['current_password'];
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];
                    
                    if (!password_verify($current_password, $user['password_hash'])) { // strict check
                        echo '<div class="alert alert-danger">Current password is incorrect.</div>';
                    } elseif ($new_password !== $confirm_password) {
                        echo '<div class="alert alert-danger">New passwords do not match.</div>';
                    } elseif (strlen($new_password) < 6) {
                        echo '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
                    } else {
                        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                        if ($stmt->execute([$new_hash, $user_id])) {
                             echo '<div class="alert alert-success">Password updated successfully!</div>';
                             // Update local user array to reflect new hash if needed (though hash changes, we usually don't need it immediately unless re-verifying in same request)
                             $user['password_hash'] = $new_hash;
                        } else {
                             echo '<div class="alert alert-danger">Failed to update password.</div>';
                        }
                    }
                }
                ?>
                
                <form method="POST" action="?view=profile" style="max-width: 500px;">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-secondary">Update Password</button>
                </form>
            </div>
            
        <?php elseif ($view == 'orders'): ?>
            <h2 style="margin-bottom: 2rem;">My Orders</h2>
            
            <?php if (count($orders) > 0): ?>
                <div style="background: white; border-radius: 16px; overflow: hidden;">
                    <?php foreach ($orders as $order): ?>
                        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Order #<?php echo sanitize($order['order_number']); ?></div>
                                    <div style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;"><?php echo format_price($order['total']); ?></div>
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; background: #FEF9C3; color: #854D0E;">
                                        <?php echo ucfirst(sanitize($order['status'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>No orders yet</h3>
                    <p>Start shopping to see your orders here</p>
                    <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <h2 style="margin-bottom: 2rem;">Wishlist</h2>
            
            <?php
            // Fetch wishlist items
            $wishlist_stmt = $pdo->prepare("
                SELECT w.*, p.name, p.price, p.image_url, c.name as category_name 
                FROM wishlist w 
                JOIN products p ON w.product_id = p.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE w.user_id = ? 
                ORDER BY w.created_at DESC
            ");
            $wishlist_stmt->execute([$user_id]);
            $wishlist_items = $wishlist_stmt->fetchAll();
            ?>
            
            <?php if (count($wishlist_items) > 0): ?>
                <div class="product-grid">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="product-card">
                            <a href="product.php?id=<?php echo $item['product_id']; ?>">
                                <div class="product-image">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo $item['image_url']; ?>" alt="<?php echo sanitize($item['name']); ?>">
                                    <?php else: ?>
                                        <span>🥤</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <span class="product-category"><?php echo strtoupper(sanitize($item['category_name'])); ?></span>
                                    <h3><?php echo sanitize($item['name']); ?></h3>
                                    <span class="product-price"><?php echo format_price($item['price']); ?></span>
                                </div>
                            </a>
                            <button class="btn btn-secondary btn-small" style="margin-top: 0.5rem;" onclick="removeFromWishlist(<?php echo $item['product_id']; ?>, this)">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">❤️</div>
                    <h3>Your wishlist is empty</h3>
                    <p>Save your favorite items here</p>
                    <a href="shop.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php endif; ?>
            
            <script>
            function removeFromWishlist(productId, button) {
                if (!confirm('Remove this item from your wishlist?')) return;
                
                fetch('<?php echo APP_URL; ?>/api/wishlist_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=remove&product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the product card from the page
                        button.closest('.product-card').remove();
                        
                        // Check if wishlist is now empty
                        const grid = document.querySelector('.product-grid');
                        if (grid && grid.children.length === 0) {
                            location.reload();
                        }
                    } else {
                        alert(data.message || 'Failed to remove item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
            </script>
        <?php endif; ?>
    </div>
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
