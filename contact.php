<?php
define('PAGE_TITLE', 'Contact Us');
require_once 'includes/header.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    $stmt = $pdo->prepare("INSERT INTO enquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $subject, $message])) {
        set_flash_message("Message sent! We'll get back to you soon.");
    } else {
        set_flash_message("Error sending message.", "danger");
    }
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Get In Touch</h1>
        <p>Have a question or feedback? We'd love to hear from you</p>
    </div>
</section>

<!-- Contact Info -->
<section class="content-section">
    <div class="container">
        <div class="contact-info">
            <div class="contact-card">
                <div class="icon">📧</div>
                <h4>Email Us</h4>
                <p>We'll respond within 24 hours</p>
                <a href="mailto:hello@bon.com">admin@bon.com</a>
            </div>
            <div class="contact-card">
                <div class="icon">📞</div>
                <h4>Call Us</h4>
                <p>Mon-Fri 9am to 5pm</p>
                <a href="tel:+15551234567">+251 9-00-00-00-00</a>
            </div>
            <div class="contact-card">
                <div class="icon">📍</div>
                <h4>Visit Us</h4>
                <p>Ambo,awaro,hhcu</p>
                <p>Ethiopia</p>
            </div>
        </div>

        <div class="grid-2" style="gap: 4rem; align-items: flex-start;">
            <div>
                <div style="background: white; padding: 3rem; border-radius: 16px; border: 1px solid var(--border-light);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                        <div style="width: 50px; height: 50px; background: #EBF5FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">💬</div>
                        <h3>Send us a message</h3>
                    </div>
                    <p style="color: var(--text-muted); margin-bottom: 2rem;">Fill out the form and we'll be in touch.</p>
                    
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Your Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="How can we help?" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" placeholder="Tell us more about your inquiry..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            ✉️ Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <div>
                <h3 style="margin-bottom: 2rem;">Frequently Asked Questions</h3>
                <p style="color: var(--text-muted); margin-bottom: 3rem;">Quick answers to common questions</p>
                
                <div class="faq">
                    <div class="faq-item">
                        <h4>Where do you ship?</h4>
                        <p>We currently ship all accross ETHIOPIA states and select international locations. Check our shipping page for details.</p>
                    </div>
                    <div class="faq-item">
                        <h4>What is your return policy?</h4>
                        <p>We offer a 30-day satisfaction guarantee. If you're not happy with your purchase, contact us for a full refund.</p>
                    </div>
                    <div class="faq-item">
                        <h4>Are your products really sugar-free?</h4>
                        <p>Yes! We use zero sugar and zero artificial sweeteners. Our Soft-drink get their natural fruit taste.</p>
                    </div>
                    <div class="faq-item">
                        <h4>How should I store my BON?</h4>
                        <p>Store in a cool, dry place. For best taste, refrigerate before serving and consume within 12 months of purchase.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter">
    <div class="container">
        <h3>Stay Updated</h3>
        <p>Subscribe to get special offers, free giveaways, and new arrivals.</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email" required>
            <button type="submit">→</button>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
