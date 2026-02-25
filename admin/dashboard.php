<?php
session_start();

// Enforce Session Validation
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: login.php');
    exit();
}

// Enforce Role
if (!isset($_SESSION['2fa_role']) || !in_array($_SESSION['2fa_role'], ['admin', 'super_admin'])) {
    header('Location: login.php');
    exit();
}

// Le Maison de Yelo Lane - Admin Dashboard
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Le Maison</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- External CSS -->
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    
    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    

</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header/Topbar -->
            <?php include 'includes/topbar.php'; ?>

            <!-- Sections -->
            <?php include 'includes/overview.php'; ?>
            <?php include 'includes/analytics.php'; ?>
            <?php include 'includes/orders.php'; ?>
            <?php include 'includes/menu.php'; ?>
            <?php include 'includes/approvals.php'; ?>
            <?php include 'includes/users.php'; ?>
            <?php include 'includes/customers.php'; ?>
            <?php include 'includes/inventory.php'; ?>
            <?php include 'includes/reservations.php'; ?>
            <?php include 'includes/delivery.php'; ?>
            <?php include 'includes/reviews.php'; ?>
            <?php include 'includes/promotions.php'; ?>
            <?php include 'includes/reports.php'; ?>
            <?php include 'includes/settings.php'; ?>
        </main>
    </div>

    <!-- Modals -->
    <?php include 'includes/modals.php'; ?>

    <!-- Scripts -->
    <?php include 'includes/scripts.php'; ?>

</body>
</html>
