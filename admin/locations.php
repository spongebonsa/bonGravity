<?php
ob_start(); // Prevent headers already sent issues
// Handle logical operations before any output
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle Delete Location
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get image URL to delete file
    $stmt = $pdo->prepare("SELECT image_url FROM store_locations WHERE id = ?");
    $stmt->execute([$id]);
    $loc = $stmt->fetch();
    
    if ($loc && $loc['image_url']) {
        $file_path = '../uploads/' . $loc['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM store_locations WHERE id = ?");
    if ($stmt->execute([$id])) {
        if ($stmt->rowCount() > 0) {
            set_flash_message("Location deleted successfully.");
        } else {
            set_flash_message("Location not found.", "warning");
        }
    } else {
        set_flash_message("Failed to delete location.", "danger");
    }
    
    header("Location: locations.php");
    exit;
}

// Handle Add Location
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_location'])) {
    $name = sanitize($_POST['name']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $phone = sanitize($_POST['phone']);
    $hours = sanitize($_POST['hours']);
    $image_url = null;

    // Handle Image Upload
    if (isset($_FILES['location_image']) && $_FILES['location_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/locations/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['location_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('loc_') . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['location_image']['tmp_name'], $target_path)) {
            $image_url = 'locations/' . $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO store_locations (name, address, city, phone, hours, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $address, $city, $phone, $hours, $image_url])) {
        set_flash_message("Location added successfully.");
        header("Location: locations.php");
        exit;
    } else {
        set_flash_message("Failed to add location.", "danger");
    }
}

$stmt = $pdo->query("SELECT * FROM store_locations ORDER BY id DESC");
$locations = $stmt->fetchAll();

define('PAGE_TITLE', 'Manage Locations');
require_once 'includes/header.php';
?>

<div class="card">
    <h3 class="font-bold text-lg mb-4">Add New Location</h3>
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="add_location" value="1">
        <div class="form-group">
            <label class="form-label">Store Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. BON Downtown">
        </div>
        <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" required placeholder="e.g. New York">
        </div>
        <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" required placeholder="e.g. 123 Main St">
        </div>
        <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="e.g. (555) 123-4567">
        </div>
        <div class="form-group">
            <label class="form-label">Opening Hours</label>
            <input type="text" name="hours" class="form-control" required placeholder="e.g. Mon-Fri: 9am-8pm">
        </div>
        <div class="form-group">
            <label class="form-label">Location Image</label>
            <input type="file" name="location_image" class="form-control" accept="image/*">
        </div>
        <div class="md:col-span-2">
            <button type="submit" class="btn btn-primary">Add Location</button>
        </div>
    </form>
</div>

<div class="card">
    <h3 class="font-bold text-lg mb-4">Existing Locations</h3>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>City/Address</th>
                    <th>Hours</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locations as $loc): ?>
                    <tr>
                        <td>
                            <?php if ($loc['image_url']): ?>
                                <img src="<?php echo APP_URL . '/uploads/' . $loc['image_url']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">🏪</div>
                            <?php endif; ?>
                        </td>
                        <td class="font-medium"><?php echo sanitize($loc['name']); ?></td>
                        <td>
                            <?php echo sanitize($loc['city']); ?><br>
                            <small class="text-gray-500"><?php echo sanitize($loc['address']); ?></small>
                        </td>
                        <td><?php echo sanitize($loc['hours']); ?></td>
                        <td>
                            <a href="locations.php?delete=<?php echo $loc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($locations)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-4">No locations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
