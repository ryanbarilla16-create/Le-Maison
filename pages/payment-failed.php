<?php
// Payment Failed Page - Le Maison de Yelo Lane
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Le Maison</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f6f0, #fff);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }
        .fail-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }
        .fail-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .fail-icon i { font-size: 2rem; color: white; }
        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--dark-brown);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .subtitle { color: #888; font-size: 0.95rem; margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            padding: 0.9rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            margin: 0.3rem;
        }
        .btn-primary {
            background: var(--dark-brown);
            color: var(--primary-gold);
        }
        .btn-primary:hover {
            background: var(--primary-gold);
            color: var(--dark-brown);
        }
        .btn-secondary {
            background: #f5f5f5;
            color: var(--dark-brown);
        }
        .btn-secondary:hover { background: #e8e8e8; }
    </style>
</head>
<body>

<div class="fail-card">
    <div class="fail-icon">
        <i class="fas fa-times"></i>
    </div>
    <h1>Payment Failed</h1>
    <p class="subtitle">Your payment was not completed. Don't worry, your order has not been charged.</p>
    
    <p style="margin-bottom: 2rem; color: #666; font-size: 0.9rem;">
        You can try again or choose a different payment method.
    </p>

    <a href="../index.php#menu" class="btn btn-primary"><i class="fas fa-redo"></i> Try Again</a>
    <a href="../index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Back to Home</a>
</div>

</body>
</html>
