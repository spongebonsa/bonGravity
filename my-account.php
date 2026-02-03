<?php
require_once 'auth_check.php';
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

// Handle Order Cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    
    // Verify the order belongs to the user and can be cancelled
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        // Define which statuses can be cancelled (pending, processing, etc.)
        $cancellable_statuses = ['pending', 'processing'];
        
        if (in_array($order['status'], $cancellable_statuses)) {
            // Update order status to cancelled (remove updated_at since column doesn't exist)
            $update_stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            if ($update_stmt->execute([$order_id])) {
                $_SESSION['success_message'] = "Order #{$order['order_number']} has been cancelled successfully.";
                redirect('my-account.php?view=orders');
            } else {
                $_SESSION['error_message'] = "Failed to cancel order. Please try again.";
            }
        } else {
            $_SESSION['error_message'] = "This order cannot be cancelled because it's already {$order['status']}.";
        }
    } else {
        $_SESSION['error_message'] = "Order not found or you don't have permission to cancel it.";
    }
}

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
            // Handle profile update and account deletion
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

            // Handle account deletion (user-initiated)
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
                // Remove user (wishlist will cascade; orders.user_id is set to NULL per schema)
                $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $del->execute([$user_id]);

                // Log out and redirect to homepage
                session_unset();
                session_destroy();
                redirect('index.php');
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
                        <!-- Phone and Address removed per request -->
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
                    <!-- Phone and Address removed from view -->
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

            <!-- Delete Account -->
            <div style="margin-top: 2rem; border-top: 1px solid var(--border-light); padding-top: 1.5rem;">
                <h3 style="margin-bottom: 1rem; color: #b91c1c;">Delete Account</h3>
                <p style="color: var(--text-muted);">Deleting your account will remove your user record and related wishlist entries. Orders will remain but will no longer be associated with your account. This action is irreversible.</p>
                <form method="POST" onsubmit="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.');">
                    <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account</button>
                </form>
            </div>
            
        <?php elseif ($view == 'orders'): ?>
            <h2 style="margin-bottom: 2rem;">My Orders</h2>
            
            <?php 
            // Display success/error messages
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>
            
            <?php if (count($orders) > 0): ?>
                <div style="background: white; border-radius: 16px; overflow: hidden;">
                    <?php foreach ($orders as $order): 
                        // Define which statuses can be cancelled - based on your orders.php statuses
                        $cancellable = in_array($order['status'], ['pending', 'processing']);
                        $status_color = '';
                        
                        // Set color based on status
                        switch ($order['status']) {
                            case 'pending':
                                $status_color = '#FEF9C3'; // yellow
                                break;
                            case 'processing':
                                $status_color = '#DBEAFE'; // blue
                                break;
                            case 'delivered':
                                $status_color = '#DCFCE7'; // green
                                break;
                            case 'cancelled':
                                $status_color = '#FEE2E2'; // red
                                break;
                            case 'shipped':
                                $status_color = '#E0E7FF'; // indigo
                                break;
                            default:
                                $status_color = '#F3F4F6'; // gray
                        }
                    ?>
                        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Order #<?php echo sanitize($order['order_number']); ?></div>
                                    <div style="color: var(--text-muted); font-size: 0.9rem;">
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        <!-- Removed updated_at since column doesn't exist -->
                                    </div>
                                </div>
                                <div style="text-align: right; margin-right: 1rem;">
                                    <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;"><?php echo format_price($order['total']); ?></div>
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; background: <?php echo $status_color; ?>; color: #333;">
                                        <?php echo ucfirst(sanitize($order['status'])); ?>
                                    </span>
                                </div>
                                <div>
                                    <?php if ($cancellable): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                style="font-size: 0.8rem; padding: 0.3rem 0.8rem;"
                                                onclick="showCancelModal(<?php echo $order['id']; ?>, '<?php echo $order['order_number']; ?>')">
                                            Cancel Order
                                        </button>
                                    <?php elseif ($order['status'] == 'cancelled'): ?>
                                        <span style="color: #dc2626; font-size: 0.85rem;">Cancelled</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Order Details -->
                            <div style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">
                                <div>Shipping: <?php echo sanitize($order['shipping_method'] ?? 'Standard'); ?></div>
                                <div>Payment: <?php echo ucfirst(sanitize($order['payment_method'] ?? 'Not specified')); ?></div>
                            </div>
                            
                            <!-- Cancel Order Modal -->
                            <div id="cancelModal-<?php echo $order['id']; ?>" 
                                 style="display: none; margin-top: 1rem; padding: 1rem; background: #FEF2F2; border-radius: 8px; border: 1px solid #FCA5A5;">
                                <h4 style="color: #991B1B; margin-bottom: 0.5rem;">Cancel Order #<?php echo $order['order_number']; ?></h4>
                                <p style="color: #7F1D1D; margin-bottom: 1rem;">Are you sure you want to cancel this order? This action cannot be undone.</p>
                                <form method="POST" action="" style="display: flex; gap: 10px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="cancel_order" class="btn btn-danger btn-sm">Yes, Cancel Order</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="hideCancelModal(<?php echo $order['id']; ?>)">No, Keep Order</button>
                                </form>
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
            
            <script>
            function showCancelModal(orderId, orderNumber) {
                // Hide all other modals first
                document.querySelectorAll('[id^="cancelModal-"]').forEach(modal => {
                    modal.style.display = 'none';
                });
                
                // Show the selected modal
                const modal = document.getElementById('cancelModal-' + orderId);
                if (modal) {
                    modal.style.display = 'block';
                    
                    // Scroll to the modal
                    modal.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
            
            function hideCancelModal(orderId) {
                const modal = document.getElementById('cancelModal-' + orderId);
                if (modal) {
                    modal.style.display = 'none';
                }
            }
            
            // Close modal when clicking outside
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('btn-outline-danger')) {
                    return;
                }
                
                const modals = document.querySelectorAll('[id^="cancelModal-"]');
                modals.forEach(modal => {
                    if (!modal.contains(event.target) && modal.style.display === 'block') {
                        modal.style.display = 'none';
                    }
                });
            });
            </script>
            
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
                        button.closest('.product-card').remove();
                        
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