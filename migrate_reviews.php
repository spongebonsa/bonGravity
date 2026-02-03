<?php
// Simple migration to create `reviews` table if it doesn't exist.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'reviews'");
    $chk->execute([DB_NAME]);
    $exists = $chk->fetchColumn() > 0;

    if ($exists) {
        echo "Reviews table already exists.\n";
        exit;
    }

    $sql = "CREATE TABLE `reviews` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `product_id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL,
      `rating` tinyint(1) NOT NULL,
      `review` text NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `product_id` (`product_id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Reviews table created successfully.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}

?>
