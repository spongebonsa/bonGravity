<?php
define('PAGE_TITLE', 'About Us');
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Our Story</h1>
        <p>We believe in refreshment without compromise. Tasty as bon BON - beverages made with pure ingredients, zero sugar, and first-natural and organic artificial sweeteners.</p>
    </div>
</section>

<!-- Mission Section -->
<section class="content-section">
    <div class="container">
        <div class="grid-2">
            <div>
                <div class="label" style="color: var(--primary-blue); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">OUR MISSION</div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Better For You,<br><span style="color: var(--primary-blue);">Better For Tomorrow</span></h2>
                <p style="color: var(--text-muted); margin-bottom: 1rem;">We're on a mission to change the beverage industry. No more drinks full of sugar and artificial sweeteners and additives. Just clean, functional ingredients that actually do what they say.</p>
                <p style="color: var(--text-muted);">Our promise is simple: transparent ingredients, real benefits, and a commitment to sustainability. We believe you shouldn't have to choose between what tastes good and what's good for you.</p>
            </div>
            <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
                <div class="stat-card">
                    <div class="icon">💯</div>
                    <div class="value">100%</div>
                    <div class="label">Natural Ingredients</div>
                </div>
                <div class="stat-card">
                    <div class="icon">🍃</div>
                    <div class="value">0g</div>
                    <div class="label">Sugar Added</div>
                </div>
                <div class="stat-card">
                    <div class="icon">❤️</div>
                    <div class="value">10K+</div>
                    <div class="label">Happy Customers</div>
                </div>
                <div class="stat-card">
                    <div class="icon">🏪</div>
                    <div class="value">12+</div>
                    <div class="label">Store Locations</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="content-section alt">
    <div class="container">
        <div class="section-header">
            <div class="label">WHAT WE STAND FOR</div>
            <h2>Our Values</h2>
        </div>
        <div class="grid-3">
            <div class="card">
                <div class="card-icon">🌿</div>
                <h3>Pure Ingredients</h3>
                <p>We use only the finest natural and organic ingredients. No artificial flavors, colors, or preservatives. Just pure, clean nutrition.</p>
            </div>
            <div class="card">
                <div class="card-icon">🚫</div>
                <h3>No Compromise</h3>
                <p>We never cut corners. Our beverages are crafted without any added sugars, artificial sweeteners, or hidden ingredients. What you see is what you get.</p>
            </div>
            <div class="card">
                <div class="card-icon">🤝</div>
                <h3>Community First</h3>
                <p>We're more than a beverage company. We're a community of people who care about their health, the planet, and each other. Join the movement.</p>
            </div>
        </div>
    </div>
</section>

<!-- Journey Section -->
<section class="content-section">
    <div class="container">
        <div class="section-header">
            <div class="label">HOW IT STARTED</div>
            <h2>Our Journey</h2>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">2019</div>
                <div class="timeline-content">
                    <h3>The Idea</h3>
                    <p>We started with a simple idea: drinks that are healthy and taste amazing, without any of the junk that's usually in them.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2020</div>
                <div class="timeline-content">
                    <h3>First Recipe</h3>
                    <p>After months of perfecting our formulas, we launched our first line of natural energy drinks that people actually loved.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2021</div>
                <div class="timeline-content">
                    <h3>Launch</h3>
                    <p>BON officially launched in 10 local stores, and the feedback was incredible. People wanted more.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2024</div>
                <div class="timeline-content">
                    <h3>Going Global</h3>
                    <p>Now available in 12+ cities, we're on a mission to make clean beverages accessible to everyone, everywhere.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Try the Difference?</h2>
        <p>Get refreshed with healthy choices. Discover what the fuss is all about.</p>
        <a href="shop.php" class="btn">Shop Now</a>
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
