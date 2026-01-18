<?php
$page_title = 'Product Form';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$product = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $image_url = $product ? $product['image_url'] : ''; // Keep old image
    
    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_url = $filename;
        }
    }
    
    if ($id) {
        // Update
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, stock=?, image_url=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $category_id, $stock, $image_url, $id]);
        set_flash_message("Product updated.");
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $category_id, $stock, $image_url]);
        set_flash_message("Product created.");
    }
    
    redirect('products.php');
}
?>

<div style="max-width: 800px; margin: 0 auto;">
    <h1 class="mb-2"><?php echo $id ? 'Edit Product' : 'Add Product'; ?></h1>
    
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $product ? sanitize($product['name']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="5" required><?php echo $product ? sanitize($product['description']) : ''; ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product ? $product['price'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" value="<?php echo $product ? $product['stock'] : '100'; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control">
                <?php if ($product && $product['image_url']): ?>
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: #64748B;">
                        Current: <?php echo $product['image_url']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update Product' : 'Create Product'; ?></button>
                <a href="products.php" class="btn" style="margin-left: 1rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
