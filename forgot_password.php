<?php
define('PAGE_TITLE', 'Forgot Password');
require_once 'includes/header.php';

if (is_logged_in()) {
    redirect('my-account.php');
}

$step = 'request';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'verify') {
        $email = sanitize($_POST['email']);
        $verify_name = sanitize($_POST['verify_name']);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && strtolower(trim($verify_name)) === strtolower(trim($user['full_name']))) {
            $_SESSION['reset_user_id'] = $user['id'];
            $step = 'reset';
        } else {
            set_flash_message("Could not verify user. Please check the email and name you provided.", "danger");
        }

    } elseif ($action === 'reset') {
        if (!isset($_SESSION['reset_user_id'])) {
            set_flash_message("Session expired. Please start the password reset again.", "danger");
            redirect('forgot_password.php');
        }

        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if ($password !== $confirm) {
            set_flash_message("Passwords do not match", "danger");
            $step = 'reset';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            if ($stmt->execute([$hash, $_SESSION['reset_user_id']])) {
                unset($_SESSION['reset_user_id']);
                set_flash_message("Password updated. You can now login.", "success");
                redirect('login.php');
            } else {
                set_flash_message("Failed to update password", "danger");
            }
        }
    }
}
?>

<div class="container" style="padding: 4rem 1.5rem;">
    <div style="max-width: 500px; margin: 0 auto; background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
        <h1 class="text-center mb-2">Reset Your Password</h1>
        <p class="text-center mb-4" style="color: var(--text-light);">We'll ask a quick verification before letting you change your password.</p>

        <?php if ($step === 'request'): ?>
            <form method="POST">
                <input type="hidden" name="action" value="verify">

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">What's your full name or nickname? (as registered)</label>
                    <input type="text" name="verify_name" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Verify</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 'reset'): ?>
            <form method="POST">
                <input type="hidden" name="action" value="reset">

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Password</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-2" style="margin-top: 1.5rem;">
            <p>Remembered your password? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
