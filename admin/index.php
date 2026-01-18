<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';

// Fetch Statistics
$revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// Recent Orders
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card stat-card">
        <div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value"><?php echo format_price($revenue); ?></div>
        </div>
        <div style="font-size: 2.5rem;">💰</div>
    </div>
    <div class="card stat-card">
        <div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value"><?php echo $order_count; ?></div>
        </div>
        <div style="font-size: 2.5rem;">📦</div>
    </div>
    <div class="card stat-card">
        <div>
            <div class="stat-label">Products</div>
            <div class="stat-value"><?php echo $product_count; ?></div>
        </div>
        <div style="font-size: 2.5rem;">🥫</div>
    </div>
    <div class="card stat-card">
        <div>
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value"><?php echo $pending_orders; ?></div>
        </div>
        <div style="font-size: 2.5rem;">⏳</div>
    </div>
</div>

<div class="card">
    <h3 class="mb-1">Recent Orders</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td><?php echo sanitize($order['order_number']); ?></td>
                    <td><?php echo sanitize($order['customer_email']); ?></td>
                    <td><?php echo format_price($order['total']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning'); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></td>
                    <td><a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
