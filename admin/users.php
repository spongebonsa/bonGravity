<?php
$page_title = 'Users';
require_once 'includes/header.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];
        
        // Prevent self-deletion
        if ($delete_id == $_SESSION['user_id']) {
            set_flash_message("You cannot delete your own account.", "danger");
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$delete_id]);
            set_flash_message("User deleted successfully.");
        }
        redirect('users.php');
    }
}

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="mb-2">
    <h1>User Management</h1>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>#<?php echo $user['id']; ?></td>
                <td><?php echo sanitize($user['full_name']); ?></td>
                <td><?php echo sanitize($user['email']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'warning' : 'success'; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </td>
                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                <td>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-outline" style="color: #EF4444; border-color: #EF4444;">Delete</button>
                    </form>
                    <?php else: ?>
                    <span style="color: #94A3B8; font-size: 0.9rem;">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
