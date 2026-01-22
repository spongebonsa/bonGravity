<?php
define('PAGE_TITLE', 'Register');
require_once 'includes/header.php';

if (is_logged_in()) {
    redirect('my-account.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        set_flash_message("Passwords do not match", "danger");
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            set_flash_message("Email already registered", "danger");
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, 'customer')");
            
            if ($stmt->execute([$name, $email, $hash])) {
                set_flash_message("Account created! Please login.", "success");
                redirect('login.php');
            } else {
                set_flash_message("Registration failed", "danger");
            }
        }
    }
}
?>

<div class="container" style="padding: 4rem 1.5rem;">
    <div style="max-width: 400px; margin: 0 auto; background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
        <h1 class="text-center mb-2">Create Account</h1>
        <p class="text-center mb-4" style="color: var(--text-light);">Join the BON community</p>
        
        <form method="POST">
            <!-- name field moved to top per request -->
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
        </form>
        
        <div class="text-center mt-2" style="margin-top: 1.5rem;">
            <p>Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login here</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
