<?php
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
    
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
        }
        body {
            background-color: var(--dark-brown);
            color: white;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: linear-gradient(rgba(44, 24, 16, 0.9), rgba(44, 24, 16, 0.9)), url('assets/images/bg-login.jpg');
            background-size: cover;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 16px;
            border: 1px solid rgba(201, 169, 97, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { font-family: 'Playfair Display', serif; color: var(--primary-gold); margin-bottom: 2rem; }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 8px;
            outline: none;
        }
        input:focus { border-color: var(--primary-gold); }
        button {
            width: 100%;
            padding: 12px;
            background: var(--primary-gold);
            color: var(--dark-brown);
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }
        button:hover { background: #E8A838; }
        .error { color: #ff6b6b; font-size: 0.9rem; margin-top: 10px; display: none; }
    </style>
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
