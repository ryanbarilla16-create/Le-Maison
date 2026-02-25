<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Le Maison de Yelo Lane - Premium French Dining
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main>
    <?php include 'includes/hero.php'; ?>

    <?php include 'includes/about.php'; ?>

    <!-- Divider: Our Story → Our Menu -->
    <div class="section-divider">
        <div class="section-divider-icon">
            <i class="fas fa-utensils"></i>
            <span>Notre Menu</span>
            <i class="fas fa-utensils"></i>
        </div>
    </div>

    <?php include 'includes/menu.php'; ?>

    <!-- Divider: Our Menu → Guest Experiences -->
    <div class="section-divider">
        <div class="section-divider-icon">
            <i class="fas fa-star"></i>
            <span>Expériences</span>
            <i class="fas fa-star"></i>
        </div>
    </div>

    <?php include 'includes/reviews.php'; ?>
</main>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/chatbot.php'; ?>
<?php include 'includes/modals.php'; ?>

<!-- Scripts -->
<?php include 'includes/scripts.php'; ?>
</body>
</html>
