<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Le Maison de Yelo Lane - Premium French Dining
?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<main>
    <?php include 'templates/hero.php'; ?>

    <?php include 'templates/about.php'; ?>

    <!-- Divider: Our Story → Our Menu -->
    <div class="section-divider">
        <div class="section-divider-icon">
            <i class="fas fa-utensils"></i>
            <span>Notre Menu</span>
            <i class="fas fa-utensils"></i>
        </div>
    </div>

    <?php include 'templates/menu.php'; ?>

    <!-- Divider: Our Menu → Guest Experiences -->
    <div class="section-divider">
        <div class="section-divider-icon">
            <i class="fas fa-star"></i>
            <span>Expériences</span>
            <i class="fas fa-star"></i>
        </div>
    </div>

    <?php include 'templates/reviews.php'; ?>
</main>

<?php include 'templates/footer.php'; ?>
<?php include 'templates/chatbot.php'; ?>
<?php include 'templates/modals.php'; ?>

<!-- Scripts -->
<?php include 'templates/scripts.php'; ?>
</body>
</html>
