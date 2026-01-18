<?php
define('PAGE_TITLE', 'Enquiries');
require_once 'includes/header.php';

// Handle Delete/Mark as Read interactions if needed
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM enquiries WHERE id = ?");
    if ($stmt->execute([$id])) {
        set_flash_message("Message deleted successfully.");
        redirect('enquiries.php');
    }
}

// Fetch Messages
$stmt = $pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>

<div class="card">
    <h3 class="font-bold text-lg mb-4">Customer Enquiries</h3>
    
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td class="whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                        </td>
                        <td class="font-medium"><?php echo sanitize($msg['name']); ?></td>
                        <td>
                            <a href="mailto:<?php echo sanitize($msg['email']); ?>" class="text-blue-600 hover:underline">
                                <?php echo sanitize($msg['email']); ?>
                            </a>
                        </td>
                        <td><?php echo sanitize($msg['subject']); ?></td>
                        <td>
                            <div class="max-w-xs truncate" title="<?php echo sanitize($msg['message']); ?>">
                                <?php echo sanitize($msg['message']); ?>
                            </div>
                        </td>
                        <td>
                            <a href="enquiries.php?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">No enquiries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
