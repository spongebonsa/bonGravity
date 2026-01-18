<?php
$page_title = 'Categories';
require_once 'includes/header.php';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    set_flash_message("Category added.");
    redirect('categories.php');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();
?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <div>
        <div class="card">
            <h3 class="mb-1">Add Category</h3>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </div>
    </div>
    
    <div>
        <div class="card">
            <h3 class="mb-1">Categories</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Products</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): 
                        $count = $pdo->query("SELECT COUNT(*) FROM products WHERE category_id = " . $cat['id'])->fetchColumn();
                    ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><?php echo sanitize($cat['name']); ?></td>
                            <td><?php echo $count; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
