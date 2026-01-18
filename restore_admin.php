<?php
require_once 'includes/db.php';

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@bon.com']);
    $user = $stmt->fetch();

    if ($user) {
        echo "Admin user already exists.<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
    } else {
        // Insert Admin
        // Password is 'password'
        $password = password_hash('password', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, password_hash, full_name, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['admin@bon.com', $password, 'Admin User', 'admin']);
        echo "Admin user restored successfully.<br>";
    }

    // Check total users
    $stmt = $pdo->query("SELECT count(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Total users in database: " . $count . "<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
