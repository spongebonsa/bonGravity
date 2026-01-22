<?php
define('PAGE_TITLE', 'Login');
require_once 'includes/header.php';

if (is_logged_in()) {
    redirect('my-account.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        set_flash_message("Welcome back, " . $user['full_name']);
        
        if ($user['role'] === 'superadmin') {
            redirect('admin/superadmin.php');
        } elseif ($user['role'] === 'admin') {
            redirect('admin/index.php');
        } else {
            redirect('my-account.php');
        }
    } else {
        set_flash_message("Invalid email or password", "danger");
    }
}
?>

<div class="container" style="padding: 4rem 1.5rem;">
    <div style="max-width: 400px; margin: 0 auto; background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
        <h1 class="text-center mb-2">Welcome Back</h1>
        <p class="text-center mb-4" style="color: var(--text-light);">Sign in to your account</p>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
        </form>
        
        <div class="text-center mt-2" style="margin-top: 1.5rem;">
            <p>Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight: 600;">Register here</a></p>
        </div>
        
        <div class="text-center mt-2" style="margin-top: 1.5rem;">
            <p><a href="forgot_password.php" style="color: var(--primary-color); font-weight: 600;">Forgot your password?</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
// Prevent navigating back into protected pages after logout.
// Push a new state and trap popstate so Back stays on (or returns to) the login page.
try {
    history.replaceState(null, null, location.href);
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function () {
        // When user presses Back, force a replace to the login URL so protected pages are not shown.
        location.replace('<?php echo APP_URL; ?>/login.php');
    });
} catch (e) {
    // ignore if history API not available
}
</script>
