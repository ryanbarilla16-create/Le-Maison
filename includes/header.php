<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Maison de Yelo Lane - Fine French Dining</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&family=Pinyon+Script&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main Style (Appended with time() to prevent browser caching issues) -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <style>
        body { margin-top: 40px !important; }
        .admin-top-bar {
            position: fixed; top: 0; left: 0; width: 100%; height: 40px;
            background: #1a0f0a; color: #D4AF37; display: flex; align-items: center;
            justify-content: space-between; padding: 0 2rem; z-index: 99999;
            font-family: 'Inter', sans-serif; font-weight: 600; border-bottom: 1px solid #D4AF37;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        .btn-add-cart, .btn-reserve, .btn-checkout, #reservationForm button, #resSubmitBtn {
            pointer-events: none !important; opacity: 0.5 !important; filter: grayscale(100%); cursor: not-allowed !important;
        }
        .cart-icon-wrapper { display: none !important; }
        #reservationForm input, #reservationForm select, #reservationForm textarea {
            pointer-events: none !important; background: #eee !important; opacity: 0.7 !important;
        }
        /* Add badges */
        .btn-add-cart::after { content: " (View Only)"; font-size: 0.8em; }
    </style>
    <div class="admin-top-bar">
        <span><i class="fas fa-lock"></i> ADMIN PREVIEW MODE - Ordering is Disabled</span>
        <a href="admin/dashboard.php" style="color:#D4AF37; text-decoration:none; border:1px solid #D4AF37; padding:4px 12px; border-radius:4px; font-size:0.8rem; transition:0.3s;">
            <i class="fas fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
<?php endif; ?>
