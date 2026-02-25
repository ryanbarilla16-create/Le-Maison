<?php
require_once __DIR__ . '/config/bootstrap.php';
// Le Maison de Yelo Lane - Unified Login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Le Maison</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

<div class="login-card" id="loginCard">
    <h1>Le Maison</h1>
    <p style="margin-bottom: 20px; color: #ccc;">Sign in to your account</p>
    
    <input type="email" id="email" placeholder="Email Address">
    <input type="password" id="password" placeholder="Password">
    <div style="text-align: right; margin: 5px 0 10px;">
        <a href="#" id="showForgotBtn" style="font-size: 0.8rem; color: var(--primary-gold); text-decoration: none; font-weight: 600; transition: 0.3s;">Forgot Password?</a>
    </div>
    <button id="loginBtn">Login</button>
    <div class="error" id="errorMsg"></div>
</div>

<!-- Forgot Password Card -->
<div class="login-card" id="forgotCard" style="display: none;">
    <h1>Le Maison</h1>
    <p style="margin-bottom: 5px; color: #ccc;">Reset Your Password</p>
    <p style="margin-bottom: 20px; color: #888; font-size: 0.82rem;">Enter your email and we'll send a reset link</p>
    
    <input type="email" id="forgotEmail" placeholder="Email Address">
    <div id="forgotMsg" style="display:none; padding: 0.7rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-align: center; margin-top: 10px;"></div>
    <button id="forgotBtn">Send Reset Link</button>
    <div style="margin-top: 15px;">
        <a href="#" id="backToLogin" style="font-size: 0.85rem; color: var(--primary-gold); text-decoration: none; font-weight: 600;"><i class="fas fa-arrow-left" style="margin-right: 5px;"></i>Back to Login</a>
    </div>
</div>
</body>
</html>
