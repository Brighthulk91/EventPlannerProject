<?php
require_once 'helpers.php';
include 'partials/header.php';
?>
<!-- Hero Section -->
<div class="hero">
    <div class="hero-content">
        <h1>Welcome to <span class="highlight">Event House</span></h1>
        
        <?php if (!is_logged_in()): ?>
            <p class="hero-btns">
                <a href="register.php" class="btn primary-btn">Register</a>
                <a href="login.php" class="btn secondary-btn">Login</a>
            </p>
        <?php else: ?>
            <p class="hero-btns">
                <a href="dashboard.php" class="btn primary-btn">Go to Dashboard</a>
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- About Section -->
<section class="about box">
    <h2>About <span class="highlight">Event House</span></h2>
    <p>At <strong>Event House</strong>, we believe every occasion deserves to be unforgettable. From elegant weddings and corporate gatherings to vibrant cultural festivals, our team transforms ideas into breathtaking experiences.</p>
    <p>Our motive is simple: <em>craft memories that last a lifetime.</em> With creativity, precision, and passion, we handle every detail — planning, management, décor, and execution — so you can focus on living the moment.</p>
    <p>Whether it’s <span class="highlight">pre-event planning</span> or <span class="highlight">post-event celebrations</span>, Event House is your trusted partner in making dreams come alive.</p>
</section>
<!-- Secondary CTA Box -->
<!-- Secondary CTA Box -->
<div class="homepage-cta box">
    <h3>Welcome to <span class="highlight">Event House</span></h3>
    <p>Your one-stop solution to plan, manage, and celebrate unforgettable events.</p>
    <?php if (is_logged_in()): ?>
        <a href="dashboard.php" class="home-btn">Go to Dashboard</a>
    <?php else: ?>
        <a href="login.php" class="home-btn">Login to Dashboard</a>
    <?php endif; ?>
</div>


<?php include 'partials/footer.php'; ?>
