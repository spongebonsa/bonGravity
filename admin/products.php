<?php
$page_title = 'Products';
require_once 'includes/header.php';

// Handle Delete
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("UPDATE products SET status = 'inactive' WHERE id = ?");
    $stmt->execute([$id]);
    set_flash_message("Product deleted (set to inactive).");
    redirect('products.php');
}

$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status != 'inactive' ORDER BY p.id DESC");
$products = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Products</h1>
    <a href="product_form.php" class="btn btn-primary">Add New Product</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td>
                        <?php if($p['image_url']): ?>
                            <span style="font-size: 1.5rem;">🥫</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo sanitize($p['name']); ?></td>
                    <td><?php echo sanitize($p['category_name']); ?></td>
                    <td><?php echo format_price($p['price']); ?></td>
                    <td><?php echo $p['stock']; ?></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="product_form.php?id=<?php echo $p['id']; ?>" class="btn btn-sm" style="background: #3B82F6; color: white;">Edit</a>
                            <form method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="delete_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn btn-sm" style="background: #EF4444; color: white; border: none;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
