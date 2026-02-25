<?php
// Le Maison - Premium Rider Portal
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Portal - Le Maison</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
            --off-white: #FFF8E7;
            --white: #FFFFFF;
            --success: #2D9F5D;
            --danger: #D94052;
            --shadow: 0 10px 30px rgba(44, 24, 16, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-brown);
            color: var(--white);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Premium Background Glows */
        .bg-glow {
            position: fixed;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(201, 169, 97, 0.15) 0%, transparent 70%);
            z-index: -1;
            filter: blur(50px);
        }

        header {
            padding: 2.5rem 1.5rem 1.5rem;
            text-align: center;
            background: linear-gradient(180deg, rgba(44, 24, 16, 0.95) 0%, transparent 100%);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary-gold);
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
        }

        .portal-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: rgba(255, 255, 255, 0.5);
        }

        main {
            flex: 1;
            padding: 1.5rem;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }

        /* Status Card */
        .status-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .rider-status {
            font-size: 0.9rem;
            color: var(--primary-gold);
            margin-bottom: 1rem;
            display: block;
        }

        .switch-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 70px;
            height: 34px;
        }

        .switch input { opacity: 0; width: 0; height: 0; }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #444;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider { background-color: var(--primary-gold); }
        input:checked + .slider:before { transform: translateX(36px); }

        .status-text {
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }

        /* Active Deliveries List */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0 0.5rem;
        }

        .section-header h2 {
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary-gold);
        }

        .order-card {
            background: var(--white);
            color: var(--dark-brown);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.5s ease forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.8rem;
        }

        .order-id { font-weight: 700; font-size: 1rem; }
        .order-time { font-size: 0.8rem; color: #999; }

        .customer-info { margin-bottom: 1.5rem; }
        .customer-name { font-weight: 600; font-size: 1.1rem; margin-bottom: 0.3rem; display: block; }
        .customer-address { font-size: 0.9rem; color: #666; display: flex; gap: 8px; }
        .customer-address i { color: var(--primary-gold); margin-top: 3px; }

        .btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .btn {
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-gold);
            color: var(--primary-gold);
        }

        .btn-gold {
            background: var(--primary-gold);
            color: var(--dark-brown);
        }

        .btn-delivered {
            grid-column: 1 / -1;
            background: var(--dark-brown);
            color: var(--primary-gold);
            margin-top: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.1; }

        /* Login Modal Simulation for Demo */
        .login-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: var(--dark-brown);
            z-index: 2000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            transition: var(--transition);
        }

        .login-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .login-card {
            width: 100%;
            max-width: 350px;
            text-align: center;
        }

        .login-card .logo { font-size: 2.5rem; margin-bottom: 3rem; }

        .input-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary-gold);
            margin-bottom: 0.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            outline: none;
            transition: var(--transition);
        }

        .input-group input:focus {
            border-color: var(--primary-gold);
            background: rgba(255, 255, 255, 0.1);
        }

        #loginError {
            color: var(--danger);
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: none;
        }
    </style>
</head>
<body>
    <div class="bg-glow" style="top: -100px; right: -100px;"></div>
    <div class="bg-glow" style="bottom: -100px; left: -100px;"></div>

    <!-- Login Overlay -->
    <div class="login-overlay" id="riderLogin">
        <div class="login-card">
            <h1 class="logo">Le Maison</h1>
            <p class="portal-title" style="margin-bottom: 3rem;">Rider Access</p>
            
            <div id="loginError">Invalid rider credentials. Please try again.</div>
            
            <div class="input-group">
                <label>Rider Email</label>
                <input type="email" id="email" placeholder="rider@lemaison.ph">
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" id="password" placeholder="••••••••">
            </div>
            
            <button class="btn btn-gold" style="width: 100%; padding: 1.2rem;" id="loginBtn">
                Enter Portal <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Portal Header -->
    <header>
        <h1 class="logo">Le Maison</h1>
        <p class="portal-title">Delivery Partner</p>
    </header>

    <main>
        <!-- Status & Tracking -->
        <div class="status-card">
            <span class="rider-status">Tracking System</span>
            <div class="switch-container">
                <span class="status-text" id="statusLabel" style="color: #666;">OFFLINE</span>
                <label class="switch">
                    <input type="checkbox" id="onlineSwitch">
                    <span class="slider"></span>
                </label>
            </div>
            <p style="font-size: 0.8rem; color: rgba(255,255,255,0.4);" id="locationStatus">
                GPS Tracking is currently disabled.
            </p>
        </div>

        <!-- Section Header -->
        <div class="section-header">
            <h2>Active Tasks</h2>
            <span id="taskCount" style="background: var(--primary-gold); color: var(--dark-brown); padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.8rem;">0</span>
        </div>

        <!-- Deliveries List -->
        <div id="deliveryList">
            <div class="empty-state">
                <i class="fas fa-motorcycle"></i>
                <p>No active deliveries assigned to you.</p>
            </div>
        </div>
    </main>

    <!-- Footer Nav (Mobile) -->
    <nav style="padding: 1rem; border-top: 1px solid rgba(255,255,255,0.05); text-align: center; font-size: 0.75rem; color: rgba(255,255,255,0.3);">
        &copy; 2026 Le Maison de Yelo Lane. Premium Delivery Partner Portal.
    </nav>
</body>
</html>
