<?php
session_start();

// Enforce Session Validation
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: login.php');
    exit();
}

// Enforce Role
if (!isset($_SESSION['2fa_role']) || !in_array($_SESSION['2fa_role'], ['cashier', 'admin', 'super_admin'])) {
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
    <title>Cashier Dashboard - Le Maison</title>
    

    


    <!-- Modals -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        :root {
            --primary-gold: #C9A961;
            --gold-light: #F5E6CC;
            --gold-glow: rgba(201, 169, 97, 0.15);
            --dark-brown: #2C1810;
            --light-bg: #F7F5F2;
            --white: #ffffff;
            --text-dark: #2D2A26;
            --text-muted: #8C8278;
            --border-color: #E8E2DA;
            --success: #2D9F5D;
            --warning: #E5A100;
            --danger: #D94052;
            --shadow-sm: 0 2px 8px rgba(44,24,16,0.04);
            --shadow-md: 0 4px 20px rgba(44,24,16,0.06);
            --shadow-lg: 0 12px 40px rgba(44,24,16,0.1);
            --radius: 14px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            /* Hidden by default, shown by JS after auth check */
            display: none; 
        }

        /* Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            /* Homepage Background Texture - More Visible */
            background: linear-gradient(rgba(26, 15, 10, 0.75), rgba(26, 15, 10, 0.85)), url('https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            color: var(--white);
            display: flex;
            flex-direction: column;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: var(--transition);
            overflow-y: auto;
            box-shadow: 4px 0 30px rgba(0,0,0,0.15);
        }

        /* Scrollbar */
        /* Scrollbar (Modern & sleek) */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { 
            background-color: #cfd8dc; 
            border-radius: 20px; 
            border: 3px solid transparent; 
            background-clip: content-box; 
        }
        ::-webkit-scrollbar-thumb:hover { background-color: #90a4ae; }

        .logo {
            padding: 0 1.5rem;
            margin-bottom: 2.5rem;
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: var(--primary-gold);
            text-decoration: none;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        .logo i {
            font-size: 1.3rem;
            background: linear-gradient(135deg, var(--primary-gold), #E8D5A8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Sidebar State */
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed .logo span, 
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .nav-section-label,
        .sidebar.collapsed .user-info { display: none; }
        .sidebar.collapsed .logo { justify-content: center; padding: 0; }
        .sidebar.collapsed .nav-item a { justify-content: center; padding: 1rem; }
        .sidebar.collapsed .nav-item i { margin: 0; font-size: 1.4rem; }
        
        .main-content.expanded { margin-left: 80px; }

        .nav-links {
            list-style: none;
            flex: 1;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.5rem;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
            cursor: pointer;
            margin: 2px 0;
        }

        .nav-item a:hover {
            background: rgba(201, 169, 97, 0.08);
            color: rgba(255,255,255,0.95);
            border-left-color: rgba(201, 169, 97, 0.4);
        }

        .nav-item a.active {
            background: rgba(201, 169, 97, 0.12);
            color: var(--primary-gold);
            border-left-color: var(--primary-gold);
            font-weight: 600;
        }

        .nav-item i { margin-right: 15px; width: 20px; text-align: center; font-size: 1.1rem; }

        .user-info {
            padding: 1rem 1.5rem; /* Reduced padding */
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.3px;
            cursor: pointer; /* Ensure clickable */
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem 2.5rem;
            transition: var(--transition);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        /* Kitchen Display System Styles */
        .kitchen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .kitchen-card {
            background: #2a2a2a;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #444;
            display: flex;
            flex-direction: column;
            color: #fff;
            transition: transform 0.2s;
        }
        .kitchen-card.priority-high {
            border-color: #dc3545;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.3);
        }
        .kitchen-card-header {
            padding: 15px;
            background: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #444;
        }
        .kitchen-card-body {
            padding: 15px;
            flex-grow: 1;
        }
        .kitchen-item-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 1.1rem;
            border-bottom: 1px solid #444;
            padding-bottom: 8px;
        }
        .kitchen-item-qty {
            font-weight: bold;
            color: var(--primary-gold);
            margin-right: 12px;
            font-size: 1.3rem;
        }
        .kitchen-item-name {
            flex-grow: 1;
        }
        .kitchen-card-footer {
            padding: 15px;
            background: #2a2a2a;
            border-top: 1px solid #444;
            display: flex;
            gap: 10px;
        }
        .kds-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            text-transform: uppercase;
        }
        .kds-btn-prep { background: #0d6efd; color: white; }
        .kds-btn-done { background: #198754; color: white; }
        .blink-animation { animation: blink 2s infinite; }
        @keyframes blink { 50% { opacity: 0.6; } }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--dark-brown);
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .date-display {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            background: var(--white);
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            box-shadow: var(--shadow-sm);
        }

        /* Sections */
        .section-view {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        
        .section-view.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        /* Vibrant Overview Cards */
        /* Vibrant Overview Cards - Enhanced SOLID Gradients */
        .stat-card {
            background: #fff;
            padding: 1.8rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-orders { border-left: 5px solid #FFD700; background: linear-gradient(145deg, #fff, #fffde7); }
        .stat-orders .stat-icon-wrapper { 
            background: linear-gradient(135deg, #FFD700, #FBC02D); 
            box-shadow: 0 4px 10px rgba(255, 215, 0, 0.4);
            color: #fff !important;
        }

        .stat-revenue { border-left: 5px solid #4CAF50; background: linear-gradient(145deg, #fff, #e8f5e9); }
        .stat-revenue .stat-icon-wrapper { 
            background: linear-gradient(135deg, #4CAF50, #2E7D32); 
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.4);
            color: #fff !important;
        }

        .stat-pending { border-left: 5px solid #FF5722; background: linear-gradient(145deg, #fff, #fbe9e7); }
        .stat-pending .stat-icon-wrapper { 
            background: linear-gradient(135deg, #FF5722, #D84315); 
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.4);
            color: #fff !important;
        }

        .stat-customers { border-left: 5px solid #2196F3; background: linear-gradient(145deg, #fff, #e3f2fd); }
        .stat-customers .stat-icon-wrapper { 
            background: linear-gradient(135deg, #2196F3, #1565C0); 
            box-shadow: 0 4px 10px rgba(33, 150, 243, 0.4);
            color: #fff !important;
        }

        /* Hover Effects */
        .stat-card:hover { transform: translateY(-5px); }
        .stat-orders:hover { box-shadow: 0 8px 25px rgba(255, 215, 0, 0.25); border-color: #FFD700; }
        .stat-revenue:hover { box-shadow: 0 8px 25px rgba(76, 175, 80, 0.25); border-color: #4CAF50; }
        .stat-pending:hover { box-shadow: 0 8px 25px rgba(255, 87, 34, 0.25); border-color: #FF5722; }
        .stat-customers:hover { box-shadow: 0 8px 25px rgba(33, 150, 243, 0.25); border-color: #2196F3; }

        .stat-info h3 { 
            font-size: 2.2rem; 
            color: var(--dark-brown);
            margin: 4px 0; 
            font-weight: 800;
        }
        
        .stat-info p { 
            color: var(--text-muted); 
            font-size: 0.85rem; 
            font-weight: 600; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon-wrapper {
            width: 65px; height: 65px;
            border-radius: 14px; /* Soft Square */
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
            font-size: 1.8rem;
        }
        
        .stat-icon { color: white !important; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1)); } 
        .stat-card:hover .stat-icon { transform: scale(1.1); } 
        .stat-card::before { display: none; }

        /* Chart Cards */
        .chart-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }
        .chart-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-color: var(--gold-light);
        }
        .chart-card h3 {
             margin-bottom: 1rem; font-size: 1.1rem; color: var(--dark-brown); font-weight: 700;
        }

        /* Tables & Lists */
        .content-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.8rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.15rem;
            color: var(--dark-brown);
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .btn-action {
            padding: 0.55rem 1.2rem;
            background: var(--dark-brown);
            color: var(--primary-gold);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.82rem;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }

        .btn-action:hover {
            background: var(--primary-gold);
            color: var(--dark-brown);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(201,169,97,0.3);
        }

        /* Premium Data Table - Level Up */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-top: 1rem;
        }

        .data-table thead {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            color: #2c3e50; /* Deep Blue-Gray Text */
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            border-bottom: 2px solid #eaeaea;
        }

        .data-table th {
            text-align: left;
            padding: 1.2rem 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #555;
            border-bottom: none; /* Removed heavy gold line */
        }

        .data-table td {
            text-align: left;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            font-size: 0.9rem;
            color: var(--text-dark);
        }
        
        .data-table tr:last-child td { border-bottom: none; }
        
        .data-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .data-table tbody tr:hover {
            background: #FFFDE7; /* Very light yellow hover */
            transform: scale(1.002);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            z-index: 2;
            position: relative;
        }
        
        /* Center Align Status and Payment Columns */
        .data-table th:last-child, .data-table td:last-child,
        .data-table th:nth-last-child(2), .data-table td:nth-last-child(2) {
            text-align: center;
        }

        /* Glowing Status Badges */
        .status-badge {
            padding: 0.6em 1.2em;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            min-width: 100px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Glow effect */
            border: 1px solid rgba(255,255,255,0.4);
            transition: 0.3s;
        }
        .status-badge:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
        
        /* Plain Colors */
        .status-pending { background: #fdf2c8; color: #856404; }  /* Light Amber */
        .status-preparing { background: #dcf1fe; color: #004085; } /* Light Blue */
        .status-ready { background: #dff4e2; color: #155724; }     /* Soft Green */
        .status-delivered { background: #e9e4f5; color: #311b92; } /* Light Purple */
        .status-cancelled { background: #fbe0e2; color: #721c24; } /* Light Red */
        .status-out-for-delivery { background: #ffe4cc; color: #bf360c; } /* Light Orange */
        .status-approved { background: #dff4e2; color: #155724; }

        /* Role Badges (User Management) */
        .role-admin { background: linear-gradient(135deg, #37474F, #263238); color: #FFD700; border: 1px solid #CFD8DC; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .role-customer { background: linear-gradient(135deg, #E0F2F1, #80CBC4); color: #00695C; border: 1px solid #B2DFDB; }
        .role-inventory { background: linear-gradient(135deg, #E3F2FD, #2196F3); color: #0D47A1; border: 1px solid #90CAF9; }
        .role-cashier { background: linear-gradient(135deg, #FFF3E0, #FF9800); color: #E65100; border: 1px solid #FFCC80; }


        /* Modern Action Buttons */
        .delete-btn {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: #FFEBEE;
            color: #D32F2F;
            border: none;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(211, 47, 47, 0.1);
        }
        .delete-btn:hover {
            background: #EF5350;
            color: white;
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 5px 15px rgba(239, 83, 80, 0.4);
        }

        /* Payment Badges */
        
        /* Inventory Level Up */
        .inventory-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .inventory-stats .stat-card {
            display: flex; flex-direction: row; align-items: center; gap: 1.5rem;
            padding: 1.8rem;
            border-radius: 16px;
            color: white !important; /* Force white text on gradients */
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            position: relative; overflow: hidden;
            background: white; /* Fallback */
        }
        .inventory-stats .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.12); }
        
        /* Stronger Specificity to override white background */
        .inventory-stats .stat-card.inv-total { background: linear-gradient(135deg, #29B6F6, #0288D1); } /* Blue */
        .inventory-stats .stat-card.inv-low { background: linear-gradient(135deg, #FFCA28, #F57C00); } /* Warning Orange */
        .inventory-stats .stat-card.inv-out { background: linear-gradient(135deg, #EF5350, #C62828); } /* Danger Red */
        
        .inventory-stats h3 { font-size: 2.2rem; font-weight: 800; margin:0; line-height:1.1; color: white !important; }
        .inventory-stats p { font-size: 0.85rem; margin:0; opacity: 0.95; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; color: white !important; }
        
        .stat-icon-wrapper {
            width: 60px; height: 60px;
            min-width: 60px;
            border-radius: 14px;
            background: rgba(255,255,255,0.25);
            display:flex; align-items:center; justify-content:center;
            font-size: 1.6rem;
            backdrop-filter: blur(5px);
            color: white !important;
        }

        /* Edit Button Style */
        .edit-btn {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: #E3F2FD;
            color: #1976D2;
            border: none;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(25, 118, 210, 0.1);
        }
        .edit-btn:hover {
            background: #2196F3;
            color: white;
            transform: scale(1.15) rotate(-5deg);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.4);
        }

        /* Reports Stats Gradients */
        .inventory-stats .stat-card.rep-revenue { background: linear-gradient(135deg, #66BB6A, #2E7D32); } /* Green */
        .inventory-stats .stat-card.rep-orders { background: linear-gradient(135deg, #7E57C2, #512DA8); } /* Deep Purple */
        .inventory-stats .stat-card.rep-menu { background: linear-gradient(135deg, #FF7043, #D84315); } /* Deep Orange */
        .inventory-stats .stat-card.rep-customers { background: linear-gradient(135deg, #26C6DA, #00838F); } /* Cyan */
        
        .inventory-stats .stat-card.rep-revenue h3, .inventory-stats .stat-card.rep-revenue p,
        .inventory-stats .stat-card.rep-orders h3, .inventory-stats .stat-card.rep-orders p,
        .inventory-stats .stat-card.rep-menu h3, .inventory-stats .stat-card.rep-menu p,
        .inventory-stats .stat-card.rep-customers h3, .inventory-stats .stat-card.rep-customers p {
            color: white !important;
        }
        .status-paid { background: #dff4e2; color: #155724; box-shadow: 0 2px 5px rgba(21, 87, 36, 0.1); } /* Light Green */
        .status-unpaid { background: #fbe0e2; color: #721c24; box-shadow: 0 2px 5px rgba(114, 28, 36, 0.1); } /* Light Red */

        /* Mini Stat Cards (Analytics) */
        .mini-stat {
            background: linear-gradient(135deg, #ffffff 40%, #E3F2FD 100%);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 1px solid rgba(227, 242, 253, 0.5);
            transition: var(--transition);
            min-height: 140px;
        }
        .mini-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: rgba(212, 175, 55, 0.3);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .icon-revenue { background: linear-gradient(135deg, #FFD700, #FFA000); } /* Gold/Amber */
        .icon-sales { background: linear-gradient(135deg, #4CAF50, #00C853); }   /* Green */
        .icon-loyalty { background: linear-gradient(135deg, #7C4DFF, #6200EA); } /* Purple */
        .icon-busy { background: linear-gradient(135deg, #00B0FF, #0091EA); }    /* Blue */
        
        .mini-stat h4 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 5px 0;
            color: var(--dark-brown);
        }
        .mini-stat p {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Critical Stock Alerts */
        .alerts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .alert-card {
            background: #fff;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); /* Soft shadow */
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #eee;
            transition: var(--transition);
        }
        .alert-card.critical {
            border-left: 4px solid #ff5252;
            background: #fff5f5;
        }
        .alert-card.healthy {
            border-left: 4px solid #4CAF50;
            background: #e8f5e9;
            color: #2E7D32;
        }
        .alert-info h4 {
            margin: 0;
            font-size: 1rem;
            color: var(--dark-brown);
        }
        .alert-info p {
            margin: 2px 0 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .btn-restock {
            background: #ff5252;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-restock:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }

        .menu-item-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .menu-item-card:hover { 
            transform: translateY(-4px); 
            box-shadow: var(--shadow-md);
            border-color: var(--gold-light);
        }
        
        .menu-item-img {
            width: 100%;
            height: 200px; /* Request: 200px */
            object-fit: cover;
            transition: transform 0.5s ease;
            flex-shrink: 0;
        }
        
        .menu-item-card:hover .menu-item-img {
            transform: scale(1.03);
        }
        
        .menu-item-details { 
            padding: 1.2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .menu-item-desc {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }
        
        .menu-item-title {
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: var(--dark-brown);
            font-size: 0.95rem;
        }
        
        .menu-item-price {
            color: var(--primary-gold);
            font-weight: 700;
            font-size: 1.05rem;
        }
        
        .menu-item-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 6px;
            z-index: 10;
        }
        
        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255,255,255,0.95);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: var(--transition);
            backdrop-filter: blur(4px);
        }
        
        .btn-icon:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-icon.edit { color: var(--primary-gold); }
        .btn-icon.delete { color: var(--danger); }

        /* Modals */
        .modal {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(44,24,16,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: 0.3s;
            backdrop-filter: blur(8px);
        }
        
        .modal.active { opacity: 1; pointer-events: all; }
        
        .modal-content {
            background: var(--white);
            width: 95%;
            max-width: 440px;
            border-radius: 16px;
            padding: 2.5rem;
            transform: scale(0.95) translateY(10px);
            transition: 0.3s;
            box-shadow: var(--shadow-lg);
            max-height: 85vh;
            overflow-y: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }
        
        .modal-content::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }
        
        .modal.active .modal-content { transform: scale(1) translateY(0); }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-title { 
            font-size: 1.4rem; 
            font-family: 'Playfair Display', serif; 
            color: var(--dark-brown);
            font-weight: 700;
        }
        
        .form-group { margin-bottom: 1.2rem; }
        
        .form-label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-size: 0.8rem; 
            font-weight: 600; 
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            outline: none;
            font-size: 0.9rem;
            transition: var(--transition);
            background: #FAFAF8;
        }
        
        .form-control:focus { 
            border-color: var(--primary-gold); 
            box-shadow: 0 0 0 3px var(--gold-glow);
            background: var(--white);
        }
        
        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--dark-brown), #1a0f0a);
            color: var(--primary-gold);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 1rem;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-gold), #E8D5A8);
            color: var(--dark-brown);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(201,169,97,0.3);
        }

        /* Loading */
        .loading-spinner {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
        }

        /* Sidebar Divider */
        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.06);
            margin: 0.6rem 1.5rem;
        }
        .nav-section-label {
            padding: 0.8rem 1.5rem 0.3rem;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.25);
            font-weight: 700;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        .chart-card h3 {
            font-size: 0.95rem;
            color: var(--dark-brown);
            margin-bottom: 1rem;
            font-weight: 700;
            letter-spacing: -0.2px;
        }
        .chart-card canvas {
            width: 100% !important;
            max-height: 280px;
        }

        /* Analytics Summary Cards */
        .analytics-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .mini-stat {
            background: var(--white);
            border-radius: 12px;
            padding: 1.3rem;
            box-shadow: var(--shadow-sm);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }
        .mini-stat:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--gold-light);
        }
        .mini-stat i {
            font-size: 1.3rem;
            color: var(--primary-gold);
            margin-bottom: 0.5rem;
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 10px;
            background: var(--gold-glow);
            display: inline-block;
        }
        .mini-stat h4 {
            font-size: 1.5rem;
            color: var(--dark-brown);
            margin: 0.3rem 0;
            font-weight: 700;
        }
        .mini-stat p {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Section placeholder cards */
        .placeholder-card {
            background: var(--white);
            border-radius: 12px;
            padding: 3rem 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            text-align: center;
        }
        .placeholder-card i {
            font-size: 3rem;
            color: var(--primary-gold);
            opacity: 0.4;
            margin-bottom: 1rem;
        }
        .placeholder-card h3 {
            color: var(--dark-brown);
            margin-bottom: 0.5rem;
        }
        .placeholder-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Settings form */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .settings-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }
        .settings-card h3 {
            font-size: 1.1rem;
            color: var(--dark-brown);
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar .logo, .sidebar span, .user-info, .nav-section-label { display: none; }
            .nav-item i { margin-right: 0; }
            .main-content { margin-left: 70px; padding: 1.5rem; }
            .charts-grid { grid-template-columns: 1fr; }
        }
        /* Overview Modal Specifics */
        .overview-modal-content {
            max-width: 900px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            animation: scaleUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* "nalaki sya" animation */
        }

        @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        .overview-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }
        .metric-card:hover { transform: translateY(-3px); border-color: var(--primary-gold); }

        .metric-card h3 { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; }
        .metric-card p { font-size: 1.8rem; font-weight: 700; color: var(--primary-gold); margin: 0; }
        .metric-card small { display: block; margin-top: 0.5rem; font-size: 0.75rem; color: var(--text-muted); }

        .overview-charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .overview-lists-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .overview-charts-grid, .overview-lists-grid { grid-template-columns: 1fr; }
        }

        .list-container {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        .list-container h4 { margin-bottom: 1rem; color: var(--dark-brown); font-size: 1.1rem; }

        /* Styled List */
        .styled-list { list-style: none; }
        .styled-list li {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        .styled-list li:last-child { border-bottom: none; }
        .rank-badge {
            background: var(--primary-gold); color: white;
            width: 20px; height: 20px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.7rem; margin-right: 10px;
        }

        /* Dark Mode Styles */
        body.dark-mode {
            --light-bg: #121212;
            --white: #1E1E1E;
            --text-dark: #E0E0E0;
            --text-muted: #B0B0B0;
            --border-color: #333333;
            --dark-brown: #F5E6CC;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.5);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.6);
            --shadow-lg: 0 12px 40px rgba(0,0,0,0.8);
        }

        /* Thermal Receipt Printing (58mm) */
        @media print {
            body * { visibility: hidden; }
            #receipt-container, #receipt-container * { visibility: visible; }
            #receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm; /* Standard thermal width */
                font-family: 'Courier New', monospace; /* Monospace for alignment */
                font-size: 12px;
                color: #000;
                background: #fff;
                padding: 5px;
            }
            /* Reset styles for print */
            .modal, .sidebar, .header { display: none !important; }
            @page {
                size: 58mm auto; /* Width, Height */
                margin: 0;
            }
        }

        /* Visual Table Map Styles */
        .table-map-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .table-box {
            background: #e8f5e9; /* Green/Available */
            border: 2px solid #c8e6c9;
            color: #2e7d32;
            padding: 15px 5px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .table-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-box.occupied {
            background: #ffebee; /* Red/Occupied */
            border-color: #ffcdd2;
            color: #c62828;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .table-box.selected {
            background: #e3f2fd; /* Blue/Selected */
            border-color: #2196f3;
            color: #1565c0;
            box-shadow: 0 0 0 2px #2196f3;
        }

        /* Kitchen Display System Styles (Modern Light Mode) */
        #kitchen-view-section {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100vh;
            background: #F5F7FA !important; /* Force light background */
            z-index: 9999;
            overflow-y: auto;
            display: none; /* Toggled by JS */
            padding: 20px;
        }
        
        .kds-header {
            display: flex; justify-content: space-between; align-items: center;
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-bottom: 3px solid #FF7043 !important; /* Kitchen Heat Line */
        }
        
        #kitchen-view-section h1 {
            color: #333 !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 800;
            font-size: 1.8rem;
            margin: 0;
            display: flex; align-items: center; gap: 10px;
            letter-spacing: normal !important;
            text-transform: uppercase;
        }
        
        .kitchen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            padding-bottom: 50px;
        }
        
        .kitchen-card {
            background: #ffffff !important;
            border-radius: 16px;
            overflow: hidden;
            border: none !important;
            display: flex;
            flex-direction: column;
            color: #333 !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
            transition: transform 0.2s;
            position: relative;
        }
        
        .kitchen-card::before {
            content:''; position:absolute; top:0; left:0; width:100%; height:4px;
            background: linear-gradient(90deg, #FF7043, #D84315);
        }
        
        .kitchen-card.priority-high::before {
            background: linear-gradient(90deg, #D32F2F, #B71C1C);
            height: 6px;
        }
        
        .kitchen-card-header {
            padding: 20px;
            background: #FAFAFA !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee !important;
        }
        
        .kitchen-card-body {
            padding: 20px;
            flex-grow: 1;
        }
        
        .kitchen-item-row {
            display: flex;
            margin-bottom: 12px;
            font-size: 1.05rem;
            border-bottom: 1px dashed #eee !important;
            padding-bottom: 10px;
            align-items: flex-start;
        }
        
        .kitchen-item-qty {
            font-weight: 900;
            color: #D84315 !important;
            margin-right: 15px;
            font-size: 1.2rem;
            min-width: 30px;
        }
        
        .kitchen-item-name {
            flex-grow: 1;
            font-weight: 600;
            color: #424242 !important;
        }
        
        .kitchen-card-footer {
            padding: 20px;
            background: #fff !important;
            border-top: none !important;
            display: flex;
            gap: 15px;
        }
        
        .kds-btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 10px !important;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .kds-btn-prep { background: linear-gradient(135deg, #42A5F5, #1E88E5) !important; color: white !important; }
        .kds-btn-done { background: linear-gradient(135deg, #66BB6A, #43A047) !important; color: white !important; }
        
        .kds-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        
        /* Exit Button Styled via ID or Class if present, assuming generic button styling or specific ID */
        #exitKdsBtn {
            background: linear-gradient(135deg, #EF5350, #C62828) !important;
            color: white !important;
            border: none;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 700;
        }

        /* Unified Button Styling (Global Override - No Brown/Gold) */
        .btn-action, .btn-primary, button[type="submit"]:not(.kds-btn):not(.theme-toggle):not(.btn-icon) {
            background: linear-gradient(135deg, #2196F3, #1565C0) !important; /* Material Blue */
            color: white !important;
            border: none !important;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(33, 150, 243, 0.3) !important;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        }
        
        .btn-action:hover, .btn-primary:hover, button[type="submit"]:not(.kds-btn):not(.theme-toggle):not(.btn-icon):hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(33, 150, 243, 0.4) !important;
            background: linear-gradient(135deg, #42A5F5, #1E88E5) !important;
        }
            border: none;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 700;
        }
        /* Cursor and hover effect for clickable avatars */
        .data-table img[onclick], .data-table div[onclick] {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .data-table img[onclick]:hover, .data-table div[onclick]:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(201,169,97,0.4) !important;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <a href="#" class="logo">
                <i class="fas fa-utensils"></i> <!-- Icon for logo -->
                <span>Le Maison</span>
            </a>
            <ul class="nav-links">
                <div class="nav-section-label">Main</div>
                <li class="nav-item">
                    <a href="#" class="nav-link active" data-target="overview">
                        <i class="fas fa-home"></i> <span>Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="analytics">
                        <i class="fas fa-chart-line"></i> <span>Analytics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="orders">
                        <i class="fas fa-receipt"></i> <span>Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="menu">
                        <i class="fas fa-utensils"></i> <span>Menu</span>
                    </a>
                </li>

                <div class="nav-divider"></div>
                <div class="nav-section-label">Management</div>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="approvals">
                        <i class="fas fa-user-check"></i> 
                        <span>Account Approvals</span>
                        <span id="sidebarApprovalCount" style="margin-left:auto;background:#dc3545;color:white;padding:2px 8px;border-radius:12px;font-size:0.75rem;font-weight:700;min-width:24px;text-align:center;display:none;">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="users">
                        <i class="fas fa-user-shield"></i> 
                        <span>User Management</span>
                        <span id="sidebarUserCount" style="margin-left:auto;background:var(--primary-gold);color:var(--dark-brown);padding:2px 8px;border-radius:12px;font-size:0.75rem;font-weight:700;min-width:24px;text-align:center;">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="customers">
                        <i class="fas fa-users"></i> <span>Customers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="inventory">
                        <i class="fas fa-boxes"></i> <span>Inventory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="reservations">
                        <i class="fas fa-calendar-check"></i> <span>Reservations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="delivery">
                        <i class="fas fa-motorcycle"></i> <span>Delivery</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="reviews">
                        <i class="fas fa-star"></i> <span>Reviews</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="promotions">
                        <i class="fas fa-tags"></i> <span>Promotions</span>
                    </a>
                </li>

                <div class="nav-divider"></div>
                <div class="nav-section-label">System</div>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="reports">
                        <i class="fas fa-file-invoice"></i> <span>Reports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-target="settings">
                        <i class="fas fa-cog"></i> <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i> <span>View Live Site</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" id="adminLogout">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
            <div class="user-info" style="display:flex; align-items:center; gap:12px; cursor:pointer; padding: 1.5rem; transition: background 0.2s;" onclick="window.openAdminProfileModal()" title="Edit Profile">
                <img id="sidebarAdminAvatar" src="https://ui-avatars.com/api/?name=Admin&background=C9A961&color=fff" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--primary-gold); box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                <div style="flex:1; overflow:hidden;">
                    <div id="sidebarAdminName" style="color:var(--white); font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Admin</div>
                    <div style="font-size:0.75rem; color:rgba(255,255,255,0.5); display:flex; align-items:center; gap:4px;">
                        <i class="fas fa-edit" style="font-size:0.65rem;"></i> Edit Profile
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Updated Header with Notifications -->
            <header class="header">
                <div style="display:flex; align-items:center; gap:15px;">
                    <button id="sidebarToggle" style="background:none; border:none; color:var(--dark-brown); font-size:1.5rem; cursor:pointer;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title" id="pageTitle">Overview</h1>
                    <button id="cashierShiftSummaryBtn" class="btn-action" style="display:none; margin-left: 15px; background: #2C1810; color: #C9A961;" onclick="window.showShiftSummary()">
                        <i class="fas fa-file-invoice-dollar"></i> Shift Summary
                    </button>
                </div>

                <div style="display:flex; align-items:center; gap:20px;">
                    <!-- Kitchen View Toggle (Leveled Up) -->
                    <button class="theme-toggle" id="kdsToggleBtn" title="Kitchen Display System" 
                        style="background: linear-gradient(135deg, #FF7043, #bf360c); color: white; border: none; height: 42px; padding: 0 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(191, 54, 12, 0.25); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; transition: all 0.2s ease; margin-right: 15px;">
                        <i class="fas fa-fire" style="margin-right:8px; font-size:1rem;"></i> Kitchen View
                    </button>

                     <!-- Theme Toggle -->
                    <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode" style="background:var(--white); border:none; width:40px; height:40px; border-radius:12px; box-shadow:var(--shadow-sm); cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--text-dark); transition:var(--transition); margin-right: 10px;">
                        <i class="fas fa-moon"></i>
                    </button>


                    
                    <div class="date-display"><?php echo date('F j, Y'); ?></div>
                    
                    <!-- Notification Bell -->
                    <div class="notification-wrapper" style="position:relative;">
                        <button id="notifBtn" style="background:white; border:none; width:40px; height:40px; border-radius:12px; box-shadow:var(--shadow-sm); cursor:pointer; position:relative; display:flex; align-items:center; justify-content:center; color:var(--dark-brown); transition:var(--transition);">
                            <i class="fas fa-bell" style="font-size:1.2rem;"></i>
                            <span id="notifBadge" style="position:absolute; top:-5px; right:-5px; background:var(--danger); color:white; font-size:0.7rem; font-weight:700; width:18px; height:18px; border-radius:50%; display:none; align-items:center; justify-content:center; border:2px solid white;">0</span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notifDropdown" style="position:absolute; top:50px; right:0; width:320px; background:white; border-radius:12px; box-shadow:var(--shadow-lg); z-index:1000; border:1px solid var(--border-color); display:none; overflow:hidden; transform-origin: top right; transition: all 0.2s ease;">
                            <div style="padding:15px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:center;">
                                <h4 style="margin:0; font-size:0.95rem; color:var(--dark-brown);">Notifications</h4>
                                <button onclick="window.clearNotifications()" style="background:none; border:none; color:var(--text-muted); font-size:0.75rem; cursor:pointer; font-weight:600;">Clear All</button>
                            </div>
                            <ul id="notifList" style="list-style:none; max-height:300px; overflow-y:auto; padding:0; margin:0;">
                                <li style="padding:20px; text-align:center; color:var(--text-muted); font-size:0.9rem;">No new notifications</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Overview Section -->
            <div id="overview-section" class="section-view active">
                <!-- 1. Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card stat-orders" onclick="openOverviewModal()" style="cursor: pointer;">
                        <div class="stat-info">
                            <p>Total Orders</p>
                            <h3 id="totalOrders">0</h3>
                        </div>
                        <!-- Icon with cleaner style -->
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-shopping-bag stat-icon"></i>
                        </div>
                    </div>
                    <div class="stat-card stat-revenue" onclick="openOverviewModal()" style="cursor: pointer;">
                        <div class="stat-info">
                            <p>Revenue</p>
                            <h3 id="totalRevenue">₱0</h3>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-coins stat-icon"></i>
                        </div>
                    </div>
                    <div class="stat-card stat-pending" onclick="openOverviewModal()" style="cursor: pointer;">
                        <div class="stat-info">
                            <p>Pending Orders</p>
                            <h3 id="pendingOrders">0</h3>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-clock stat-icon"></i>
                        </div>
                    </div>
                    <div class="stat-card stat-customers" onclick="openOverviewModal()" style="cursor: pointer;">
                        <div class="stat-info">
                            <p>Total Customers</p>
                            <h3 id="totalCustomers">0</h3>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-user-friends stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. Charts (Moved UP) -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#11998e,#38ef7d);margin-right:10px;box-shadow:0 3px 8px rgba(17,153,142,0.4);"><i class="fas fa-chart-area" style="color:#fff;font-size:0.85rem;"></i></span>Revenue Trend (Last 7 Days)</h3>
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#FF6B6B,#EE0979);margin-right:10px;box-shadow:0 3px 8px rgba(238,9,121,0.4);"><i class="fas fa-chart-pie" style="color:#fff;font-size:0.85rem;"></i></span>Order Status</h3>
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>

                <!-- Critical Stock Alerts -->
                <div id="stock-alerts-section" class="content-card" style="display:none; border-left: 5px solid #ff5252; margin-bottom: 20px;">
                    <div class="card-header">
                        <h2 class="card-title" style="color: #d32f2f;">
                            <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i> Critical Stock Alerts
                        </h2>
                    </div>
                    <div id="stockAlertsContainer" class="alerts-grid">
                        <!-- Alerts will appear here -->
                    </div>
                </div>

                <!-- 3. Recent Activity (Moved DOWN) -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Activity (Last 5 Orders)</h2>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Type</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersTable">
                            <tr><td colspan="7" class="loading-spinner">Loading data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="orders-section" class="section-view">
                <div class="content-card">
                    <div class="card-header" style="justify-content: flex-start; gap: 20px;">
                        <div style="display: flex; gap: 20px; flex: 1;">
                            <h2 class="card-title" style="cursor:pointer; border-bottom: 3px solid var(--primary-gold); padding-bottom: 5px;" id="tabActiveOrders" onclick="switchOrderTab('active')">Active Orders</h2>
                            <h2 class="card-title" style="cursor:pointer; color: var(--text-muted); border-bottom: 3px solid transparent; padding-bottom: 5px;" id="tabHistoryOrders" onclick="switchOrderTab('history')">Order History</h2>
                        </div>
                        <button id="cashierCreateOrderBtn" class="btn-action" style="display:none; background: linear-gradient(135deg, #11998e, #38ef7d); color: white; border: none; box-shadow: 0 4px 15px rgba(17,153,142,0.3);" onclick="window.openPOSModal()">
                            <i class="fas fa-plus"></i> Create New Order
                        </button>
                    </div>

                    <!-- ACTIVE ORDERS TABLE -->
                    <div id="activeOrdersContainer">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Type</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTable">
                                <tr><td colspan="8" class="loading-spinner">Loading active orders...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- HISTORY ORDERS TABLE -->
                    <div id="historyOrdersContainer" style="display:none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Type</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="historyOrdersTable">
                                <tr><td colspan="7" class="loading-spinner">Loading history...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    function switchOrderTab(tab) {
                        const activeTab = document.getElementById('tabActiveOrders');
                        const historyTab = document.getElementById('tabHistoryOrders');
                        const activeContainer = document.getElementById('activeOrdersContainer');
                        const historyContainer = document.getElementById('historyOrdersContainer');

                        if (tab === 'active') {
                            activeTab.style.color = 'var(--dark-brown)';
                            activeTab.style.borderBottomColor = 'var(--primary-gold)';
                            historyTab.style.color = 'var(--text-muted)';
                            historyTab.style.borderBottomColor = 'transparent';
                            
                            activeContainer.style.display = 'block';
                            historyContainer.style.display = 'none';
                        } else {
                            historyTab.style.color = 'var(--dark-brown)';
                            historyTab.style.borderBottomColor = 'var(--primary-gold)';
                            activeTab.style.color = 'var(--text-muted)';
                            activeTab.style.borderBottomColor = 'transparent';
                            
                            historyContainer.style.display = 'block';
                            activeContainer.style.display = 'none';
                        }
                    }
                </script>
            </div>

            <!-- Menu Section -->
            <div id="menu-section" class="section-view">
                <div class="content-card" style="background: transparent; box-shadow: none; padding: 0;">
                    <div class="card-header">
                        <h2 class="card-title">Menu Management</h2>
                        <button class="btn-action" onclick="window.openAddMenuModal()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    
                    <div class="menu-grid" id="menuGrid">
                         <div class="loading-spinner" style="grid-column: 1/-1;">Loading menu...</div>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div id="users-section" class="section-view">
                <div class="content-card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 class="card-title">All Registered Users</h2>
                            <span id="userCount" style="color:var(--text-muted); font-size:0.9rem;"></span>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button id="broadcastBtn" class="btn-action" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe) !important; box-shadow: 0 4px 15px rgba(108, 92, 231, 0.25) !important;" onclick="window.openBroadcastModal()">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAllUsers" style="cursor: pointer; width: 18px; height: 18px;">
                                </th>
                                <th>Avatar</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Birthday</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable">
                            <tr><td colspan="7" class="loading-spinner">Loading users...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Customers Section -->
            <div id="customers-section" class="section-view">
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Registered Customers</h2>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>UID</th>
                                <th>Email</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody id="customersTable">
                            <tr><td colspan="3" class="loading-spinner">Loading customers...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analytics Section -->
            <div id="analytics-section" class="section-view">
                <div class="analytics-summary">
                    <div class="mini-stat">
                        <div class="stat-icon icon-revenue"><i class="fas fa-coins"></i></div>
                        <h4 id="avgOrderValue">₱0</h4>
                        <p>Avg Order Value</p>
                    </div>
                    <div class="mini-stat">
                        <div class="stat-icon icon-sales"><i class="fas fa-fire"></i></div>
                        <h4 id="topSellingCount">0</h4>
                        <p>Items Sold Today</p>
                    </div>
                    <div class="mini-stat">
                        <div class="stat-icon icon-loyalty"><i class="fas fa-redo"></i></div>
                        <h4 id="repeatCustomers">0%</h4>
                        <p>Repeat Customers</p>
                    </div>
                    <div class="mini-stat">
                        <div class="stat-icon icon-busy"><i class="fas fa-bolt"></i></div>
                        <h4 id="peakHour">--</h4>
                        <p>Peak Hour</p>
                    </div>
                </div>
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#7C4DFF,#651FFF);margin-right:10px;box-shadow:0 3px 8px rgba(124,77,255,0.4);"><i class="fas fa-chart-bar" style="color:#fff;font-size:0.85rem;"></i></span>Daily Orders (Last 7 Days)</h3>
                        <canvas id="dailyOrdersChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#FF6B6B,#EE0979);margin-right:10px;box-shadow:0 3px 8px rgba(238,9,121,0.4);"><i class="fas fa-chart-pie" style="color:#fff;font-size:0.85rem;"></i></span>Revenue by Category</h3>
                        <canvas id="categoryRevenueChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#00B4DB,#0083B0);margin-right:10px;box-shadow:0 3px 8px rgba(0,131,176,0.4);"><i class="fas fa-clock" style="color:#fff;font-size:0.85rem;"></i></span>Busy Times</h3>
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#F7971E,#FFD200);margin-right:10px;box-shadow:0 3px 8px rgba(247,151,30,0.4);"><i class="fas fa-trophy" style="color:#fff;font-size:0.85rem;"></i></span>Top 5 Best Selling Dishes</h3>
                        <canvas id="topSellingChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#11998e,#38ef7d);margin-right:10px;box-shadow:0 3px 8px rgba(17,153,142,0.4);"><i class="fas fa-chart-line" style="color:#fff;font-size:0.85rem;"></i></span>Monthly Revenue</h3>
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#f953c6,#b91d73);margin-right:10px;box-shadow:0 3px 8px rgba(185,29,115,0.4);"><i class="fas fa-users" style="color:#fff;font-size:0.85rem;"></i></span>Customer Loyalty</h3>
                        <div style="position: relative; height: 100%;">
                            <canvas id="loyaltyChart"></canvas>
                            <div id="loyaltyCenterText" style="position: absolute; top: 60%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                                <div style="font-size: 1.8rem; font-weight: 800; color: var(--dark-brown);" id="totalUniqueCustomers">0</div>
                                <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Customers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservations Section -->
            <div id="reservations-section" class="section-view">
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Table Reservations</h2>
                        <button class="btn-action" onclick="openModal('reservationModal')"><i class="fas fa-plus"></i> New Reservation</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Guest Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Guests</th>
                                <th>Table</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reservationsTable">
                            <tr><td colspan="7" class="loading-spinner">Loading reservations...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivery Section -->
            <div id="delivery-section" class="section-view">
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Live Delivery Tracking</h2>
                    </div>
                    
                    <!-- Live Map -->
                    <div id="deliveryMap" style="height: 450px; width: 100%; border-radius: 12px; margin-bottom: 2rem; border: 1px solid var(--border-color); z-index: 1;"></div>

                    <div class="card-header">
                        <h2 class="card-title">Active Deliveries</h2>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Rider</th>
                                <th>Customer</th>
                                <th>Address</th>
                                <th>ETA</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="deliveryTable">
                            <tr><td colspan="7" class="loading-spinner">Loading deliveries...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Account Approvals Section -->
            <div id="approvals-section" class="section-view">
                <div class="content-card">
                    <div class="card-header" style="background: linear-gradient(to right, var(--dark-brown), #3d2217); color: white; padding: 1.5rem; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--primary-gold);">
                        <h2 class="card-title" style="margin:0; color:var(--primary-gold); font-family:'Playfair Display', serif;">Pending Account Approvals</h2>
                        <div style="background: var(--gold-glow); color: var(--primary-gold); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--primary-gold);">
                            <i class="fas fa-user-clock"></i> <span id="approvalCountLabel">0</span> Pending
                        </div>
                    </div>
                    <div class="table-responsive" style="padding: 0;">
                        <table class="data-table">
                            <thead>
                                <tr style="background: #fafafa; border-bottom: 2px solid #eee;">
                                    <th style="padding: 1.2rem; cursor: default;">User</th>
                                    <th>Information</th>
                                    <th>Registered Date</th>
                                    <th style="text-align: right; padding-right: 2rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="approvalsTable">
                                <tr><td colspan="4" class="loading-spinner">Loading pending approvals...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews-section" class="section-view">
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Customer Reviews</h2>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reviewsTable">
                            <tr><td colspan="6" class="loading-spinner">Loading reviews...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Promotions Section -->
            <div id="promotions-section" class="section-view">
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Promotions & Discounts</h2>
                        <button class="btn-action" onclick="openModal('promoModal')"><i class="fas fa-plus"></i> Create Promo</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Type</th>
                                <th>Valid Until</th>
                                <th>Uses</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="promotionsTable">
                            <tr><td colspan="7" class="loading-spinner">Loading promotions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reports Section -->
            <div id="reports-section" class="section-view">
                <div class="stats-grid inventory-stats report-stats">
                    <div class="stat-card rep-revenue">
                        <div class="stat-icon-wrapper"><i class="fas fa-coins"></i></div>
                        <div class="stat-info">
                            <h3 id="reportTotalRevenue">₱0</h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                    <div class="stat-card rep-orders">
                        <div class="stat-icon-wrapper"><i class="fas fa-shopping-cart"></i></div>
                        <div class="stat-info">
                            <h3 id="reportTotalOrders">0</h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-card rep-menu">
                        <div class="stat-icon-wrapper"><i class="fas fa-utensils"></i></div>
                        <div class="stat-info">
                            <h3 id="reportMenuItems">0</h3>
                            <p>Menu Items</p>
                        </div>
                    </div>
                    <div class="stat-card rep-customers">
                        <div class="stat-icon-wrapper"><i class="fas fa-users"></i></div>
                        <div class="stat-info">
                            <h3 id="reportCustomers">0</h3>
                            <p>Customers</p>
                        </div>
                    </div>
                </div>
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Sales Reports</h2>
                        <button class="btn-action" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>Avg Order</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTable">
                            <tr><td colspan="4" class="loading-spinner">Loading reports...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

    <!-- Inventory Section -->
    <section id="inventory-section" class="section-view">
        <div class="header">
            <div>
                <h1 class="page-title">Inventory Management</h1>
                <p class="date-display">Track stock levels and manage ingredients</p>
            </div>
            <button class="btn-action" onclick="window.openInventoryModal()">
                <i class="fas fa-plus"></i> Add Item
            </button>
        </div>

        <!-- Inventory Stats -->
        <div class="stats-grid inventory-stats">
            <div class="stat-card inv-total">
                <div class="stat-icon-wrapper"><i class="fas fa-cubes"></i></div>
                <div class="stat-info">
                    <h3 id="invTotalItems">0</h3>
                    <p>Total Items</p>
                </div>
            </div>
            <div class="stat-card inv-low">
                <div class="stat-icon-wrapper"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <h3 id="invLowStock">0</h3>
                    <p>Low Stock Alerts</p>
                </div>
            </div>
            <div class="stat-card inv-out">
                <div class="stat-icon-wrapper"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <h3 id="invOutOfStock">0</h3>
                    <p>Out of Stock</p>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Stock Levels</h3>
                <div class="search-box">
                    <input type="text" id="inventorySearch" placeholder="Search items..." style="padding:0.5rem 1rem; border:1px solid #ddd; border-radius:6px;">
                </div>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Stock Level</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <tr><td colspan="6" style="text-align:center;">Loading inventory...</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Inventory Modal -->
    <div id="inventoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="invModalTitle">Add Inventory Item</h3>
                <button class="btn-icon" onclick="closeModal('inventoryModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="inventoryForm">
                <input type="hidden" id="invItemId">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="invName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select id="invCategory" class="form-control">
                        <option value="Ingredients">Ingredients</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Packaging">Packaging</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" id="invQuantity" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit</label>
                    <input type="text" id="invUnit" class="form-control" placeholder="e.g., kg, pcs, box" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Min. Stock Level (Alert)</label>
                    <input type="number" id="invMinLevel" class="form-control" min="0" value="10">
                </div>
                <button type="submit" class="btn-submit" id="saveInvBtn">Save Item</button>
            </form>
        </div>
    </div>

            <!-- Settings Section -->
            <div id="settings-section" class="section-view">
                <div class="settings-grid">
                    <div class="settings-card">
                        <h3><i class="fas fa-store" style="color:var(--primary-gold);margin-right:8px;"></i>Restaurant Info</h3>
                        <div class="form-group">
                            <label class="form-label">Restaurant Name</label>
                            <input type="text" class="form-control" value="Le Maison de Yelo Lane" id="settingName">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" placeholder="Enter address" id="settingAddress">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" placeholder="+63 XXX XXX XXXX" id="settingPhone">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="email@address.com" id="settingEmail">
                        </div>
                        <button class="btn-action" style="margin-top:0.5rem;"><i class="fas fa-save"></i> Save</button>
                    </div>

                    <div class="settings-card">
                         <h3><i class="fas fa-share-alt" style="color:var(--primary-gold);margin-right:8px;"></i>Social Media</h3>
                         <div class="form-group">
                             <label class="form-label">Facebook Link</label>
                             <input type="url" class="form-control" placeholder="https://facebook.com/..." id="settingFacebook">
                         </div>
                         <div class="form-group">
                             <label class="form-label">Instagram Link</label>
                             <input type="url" class="form-control" placeholder="https://instagram.com/..." id="settingInstagram">
                         </div>
                         <button class="btn-action" style="margin-top:0.5rem;"><i class="fas fa-save"></i> Save</button>
                    </div>

                    <div class="settings-card">
                        <h3><i class="fas fa-clock" style="color:var(--primary-gold);margin-right:8px;"></i>Operating Hours</h3>
                        <div class="form-group">
                            <label class="form-label">Opening Time</label>
                            <input type="time" class="form-control" value="08:00" id="settingOpen">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Closing Time</label>
                            <input type="time" class="form-control" value="22:00" id="settingClose">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Delivery Fee (₱)</label>
                            <input type="number" class="form-control" value="50" id="settingDeliveryFee">
                        </div>
                        <button class="btn-action" style="margin-top:0.5rem;"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>

                <!-- Audit Logs Section (New) -->
                <div class="content-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-shield-alt" style="color:var(--primary-gold); margin-right:10px;"></i>Security & Audit Logs</h2>
                        <button class="btn-action" onclick="window.fetchAuditLogs()"><i class="fas fa-sync-alt"></i> Refresh</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="auditLogsTable">
                            <tr><td colspan="4" class="loading-spinner">Loading logs...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- User Details Modal -->
    <div class="modal" id="userDetailsModal">
        <div class="modal-content" style="max-width: 440px; text-align: center;">
            <div class="modal-header">
                <h3 class="modal-title">User Profile</h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('userDetailsModal')"></i>
            </div>
            
            <div class="modal-body" style="padding-top: 1rem;">
                <div style="margin-bottom: 2rem; position: relative; display: inline-block;">
                    <div id="userDetailAvatarContainer" style="width: 120px; height: 120px; border-radius: 50%; padding: 4px; background: linear-gradient(135deg, var(--primary-gold), #fff); box-shadow: 0 10px 25px rgba(201,169,97,0.3);">
                        <img id="userDetailAvatar" src="" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: #fff;">
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h2 id="userDetailName" style="color: var(--dark-brown); font-family: 'Playfair Display', serif; margin-bottom: 5px;">—</h2>
                    <p id="userDetailEmail" style="color: var(--text-muted); font-size: 0.95rem;"></p>
                    <div id="userDetailRoleBadge" style="margin-top: 10px;"></div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
                    <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                        <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Username</small>
                        <span id="userDetailUsername" style="font-weight: 600; color: var(--dark-brown);">—</span>
                    </div>
                    <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                        <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Birthday</small>
                        <span id="userDetailBirthday" style="font-weight: 600; color: var(--dark-brown);">—</span>
                    </div>
                    <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                        <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Contact</small>
                        <span id="userDetailPhone" style="font-weight: 600; color: var(--dark-brown);">—</span>
                    </div>
                    <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                        <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Member Since</small>
                        <span id="userDetailJoined" style="font-weight: 600; color: var(--dark-brown);">—</span>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button class="btn-action" id="editUserBtn" style="background: var(--white); color: var(--dark-brown); border: 1px solid var(--border-color); padding: 10px 20px;">
                            <i class="fas fa-edit"></i> Edit User
                        </button>
                        <button class="btn-action" id="deleteUserModalBtn" style="background: #fff5f5; color: #d00; border: 1px solid #fed7d7; padding: 10px 20px;">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Modal -->
    <div class="modal" id="menuModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="menuModalTitle">Add Menu Item</h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('menuModal')"></i>
            </div>
            <form id="menuForm">
                <input type="hidden" id="menuItemId">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="menuName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea id="menuDesc" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" id="menuPrice" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <input type="url" id="menuImage" class="form-control" placeholder="https://..." required>
                </div>
                <!-- Category Support (Optional enhancement) -->
                <div class="form-group">
                     <label class="form-label">Category</label>
                     <select id="menuCategory" class="form-control">
                         <option value="All Day Breakfast">All Day Breakfast</option>
                         <option value="Best Seller">Best Seller</option>
                         <option value="Cakes & Pastries">Cakes & Pastries</option>
                         <option value="Cocktails">Cocktails</option>
                         <option value="Desserts">Desserts</option>
                         <option value="Frappes">Frappes</option>
                         <option value="Hand-Tossed Pizza">Hand-Tossed Pizza</option>
                         <option value="Home Page">Home Page</option>
                         <option value="Hot Coffee">Hot Coffee</option>
                         <option value="Ice Beverages">Ice Beverages</option>
                         <option value="Ice Coffee">Ice Coffee</option>
                         <option value="Milk Tea">Milk Tea</option>
                         <option value="Milkshakes & Smoothies">Milkshakes & Smoothies</option>
                         <option value="Pasta & Salad">Pasta & Salad</option>
                         <option value="Rice Plates">Rice Plates</option>
                         <option value="Starters & Sandwiches">Starters & Sandwiches</option>
                         <option value="Steaks">Steaks</option>
                         <option value="Sweet Breakfast">Sweet Breakfast</option>
                         <option value="Thin Crust Pizza">Thin Crust Pizza</option>
                     </select>
                </div>

                <div class="form-group">
                     <label class="form-label">Recipe / Ingredients</label>
                     <div style="background:var(--light-bg); padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                         <table style="width:100%; font-size:0.85rem;" id="recipeTable">
                             <thead>
                                 <tr style="text-align:left; color:var(--text-muted);">
                                     <th style="padding-bottom:5px;">Ingredient</th>
                                     <th style="padding-bottom:5px; width:70px;">Qty</th>
                                     <th style="padding-bottom:5px; width:40px;">Unit</th>
                                     <th style="width:30px;"></th>
                                 </tr>
                             </thead>
                             <tbody id="recipeList">
                                 <!-- Rows will be added here -->
                             </tbody>
                         </table>
                         <button type="button" id="addIngredientBtn" style="margin-top:10px; background:none; border:1px dashed var(--primary-gold); color:var(--primary-gold); padding:5px 10px; border-radius:4px; font-size:0.8rem; cursor:pointer; width:100%;">
                             <i class="fas fa-plus"></i> Add Ingredient
                         </button>
                     </div>
                </div>

                <button type="submit" class="btn-submit">Save Item</button>
            </form>
        </div>
    </div>

    <!-- Reservation Modal -->
    <div class="modal" id="reservationModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="reservationModalTitle">New Reservation</h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('reservationModal')"></i>
            </div>
            <form id="reservationForm">
                <div class="form-group">
                    <label class="form-label">Guest Name</label>
                    <input type="text" id="resGuestName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" id="resDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <input type="time" id="resTime" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Number of Guests</label>
                    <input type="number" id="resGuests" class="form-control" min="1" max="50" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Table (Visual Map)</label>
                    <input type="hidden" id="resTable"> <!-- Hidden Input for value -->
                    <div id="resTableMap" class="table-map-grid">
                        <!-- Tables rendered by JS -->
                    </div>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-top:5px;">
                        <span style="display:inline-block; width:10px; height:10px; background:#e8f5e9; border:1px solid #c8e6c9; margin-right:5px;"></span>Available
                        <span style="display:inline-block; width:10px; height:10px; background:#ffebee; border:1px solid #ffcdd2; margin-left:10px; margin-right:5px;"></span>Occupied
                    </p>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="resStatus" class="form-control">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Save Reservation</button>
            </form>
        </div>
    </div>

    <!-- Table Assignment Modal (Approve Reservation) -->
    <div class="modal" id="tableAssignModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Assign Table</h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('tableAssignModal')"></i>
            </div>
            <form id="tableAssignForm">
                <input type="hidden" id="assignResId">
                <div class="form-group">
                    <label class="form-label">Guest Name</label>
                    <input type="text" id="assignGuestName" class="form-control" readonly style="background:#f5f5f5;">
                </div>
                <div class="form-group">
                    <label class="form-label">Select Table</label>
                    <select id="assignTableNumber" class="form-control" style="display:none;"> <!-- Hidden select -->
                        <option value="">-- Choose a Table --</option>
                    </select>
                    <div id="assignTableMap" class="table-map-grid"></div>
                </div>
                <button type="submit" class="btn-submit" id="assignTableBtn">
                    <i class="fas fa-check"></i> Confirm & Approve
                </button>
            </form>
        </div>
    </div>

    <!-- Promo Modal -->
    <div class="modal" id="promoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="promoModalTitle">Create Promotion</h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('promoModal')"></i>
            </div>
            <form id="promoForm">
                <div class="form-group">
                    <label class="form-label">Promo Code</label>
                    <input type="text" id="promoCode" class="form-control" placeholder="e.g. SAVE20" required style="text-transform:uppercase;">
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Amount</label>
                    <input type="number" id="promoDiscount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Type</label>
                    <select id="promoType" class="form-control">
                        <option value="Percentage">Percentage (%)</option>
                        <option value="Fixed">Fixed Amount (₱)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Valid Until</label>
                    <input type="date" id="promoExpiry" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Max Uses (0 = unlimited)</label>
                    <input type="number" id="promoMaxUses" class="form-control" value="0" min="0">
                </div>
                <button type="submit" class="btn-submit">Save Promotion</button>
            </form>
        </div>
    </div>

    <!-- Menu Modal -->
    <div id="menuModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="menuModalTitle">Add Menu Item</h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('menuModal')"></i>
            </div>
            <form id="menuForm">
                <input type="hidden" id="menuItemId">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="menuName" class="form-control" required maxlength="50" placeholder="e.g. Beef Teriyaki (Max 50 chars)">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea id="menuDesc" class="form-control" rows="3" required maxlength="200" placeholder="Ingredients, taste, etc. (Max 200 chars)"></textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" id="menuPrice" class="form-control" required min="0" max="99999" step="0.01" oninput="window.limitDigits(this, 5)">
                    </div>
                    <div class="form-group">
                         <label class="form-label">Category</label>
                         <select id="menuCategory" class="form-control">
                             <option value="All Day Breakfast">All Day Breakfast</option>
                             <option value="Rice Bowls">Rice Bowls</option>
                             <option value="Pasta">Pasta</option>
                             <option value="Sandwiches">Sandwiches</option>
                             <option value="Beverages">Beverages</option>
                             <option value="Desserts">Desserts</option>
                             <option value="Sides">Sides</option>
                         </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <input type="url" id="menuImage" class="form-control" placeholder="https://...">
                     <small style="color:var(--text-muted);">Use a valid image URL.</small>
                </div>

                <!-- Recipe / Ingredients Section -->
                <div style="margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <label class="form-label" style="margin:0;">Recipe (Inventory Deduction)</label>
                        <button type="button" id="addIngredientBtn" class="btn-action" style="padding: 4px 10px; font-size: 0.75rem;">
                            <i class="fas fa-plus"></i> Add Ingr.
                        </button>
                    </div>
                    <table style="width:100%; font-size: 0.85rem;">
                        <thead>
                            <tr style="text-align:left; color:var(--text-muted);">
                                <th style="width:50%;">Ingredient</th>
                                <th style="width:20%;">Qty</th>
                                <th style="width:15%;">Unit</th>
                                <th style="width:15%;"></th>
                            </tr>
                        </thead>
                        <tbody id="recipeList">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn-submit">Save Item</button>
            </form>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="orderDetailsModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 class="modal-title">Order Details <span id="modalOrderId" style="font-weight:400; color:var(--text-muted); font-size:0.9rem;"></span></h3>
                <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('orderDetailsModal')"></i>
            </div>
            <div style="margin-bottom: 20px;">
                <h4 style="color:var(--primary-gold); margin-bottom: 5px;">Customer Info</h4>
                <p id="modalCustomerName" style="font-weight:600; font-size:1.1rem; color:var(--dark-brown);"></p>
                <p id="modalCustomerContact" style="font-size:0.9rem; color:var(--text-muted);"></p>
                <p id="modalDeliveryAddress" style="font-size:0.9rem; color:var(--text-muted); margin-top:4px;"></p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h4 style="color:var(--primary-gold); margin-bottom: 10px;">Order Items</h4>
                <table style="width:100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 8px 0; color:var(--text-muted);">Item</th>
                            <th style="padding: 8px 0; color:var(--text-muted); text-align: center;">Qty</th>
                            <th style="padding: 8px 0; color:var(--text-muted); text-align: right;">Price</th>
                            <th style="padding: 8px 0; color:var(--text-muted); text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody id="modalOrderItems">
                        <!-- Items will be populated here -->
                    </tbody>
                    <tfoot style="border-top: 2px solid #eee;">
                        <tr>
                            <td colspan="3" style="padding: 15px 0; text-align: right; font-weight: 700; color:var(--dark-brown);">Grand Total</td>
                            <td style="padding: 15px 0; text-align: right; font-weight: 700; color:var(--primary-gold); font-size: 1.1rem;" id="modalOrderTotal">₱0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div style="text-align: right;">
                 <span id="modalPaymentStatus" class="status-badge" style="margin-right: 10px;"></span>
                 <span id="modalOrderStatus" class="status-badge"></span>
                 <button class="btn-action" id="btnPrintReceipt" style="margin-left: 10px; background: var(--dark-brown); color: white;">
                    <i class="fas fa-print"></i> Print Receipt
                 </button>
            </div>
        </div>
    </div>

    <!-- Hidden Receipt Container (For Print) -->
    <div id="receipt-container" style="display:none;">
        <div style="text-align:center; margin-bottom:10px;">
            <h3 style="margin:0; font-size:16px; font-weight:bold;">Le Maison</h3>
            <p style="margin:0; font-size:10px;">de Yelo Lane</p>
            <p style="margin:5px 0 0; font-size:10px;">--------------------------------</p>
        </div>
        
        <div style="margin-bottom:10px;">
            <p style="margin:2px 0;"><strong>Order:</strong> <span id="receiptOrderId">#123456</span></p>
            <p style="margin:2px 0;"><strong>Date:</strong> <span id="receiptDate">2023-10-25</span></p>
            <p style="margin:2px 0;"><strong>Customer:</strong> <span id="receiptCustomer">John Doe</span></p>
            <p style="margin:2px 0;"><strong>Type:</strong> <span id="receiptType">Delivery</span></p>
        </div>

        <p style="margin:5px 0 0; font-size:10px;">--------------------------------</p>

        <table style="width:100%; font-size:11px; margin-bottom:10px;">
            <tbody id="receiptItems">
                <!-- Items populated here -->
            </tbody>
        </table>

        <p style="margin:5px 0 0; font-size:10px;">--------------------------------</p>

        <div style="text-align:right; margin-bottom:10px;">
            <p style="margin:2px 0;"><strong>Total:</strong> <span id="receiptTotal" style="font-size:14px; font-weight:bold;">₱0.00</span></p>
            <p style="margin:2px 0; font-size:10px;">Payment: <span id="receiptPayment">Cash</span></p>
        </div>

        <div style="text-align:center; margin-top:15px;">
            <p style="margin:0; font-size:10px;">Thank you for ordering!</p>
            <p style="margin:0; font-size:10px;">Please come again.</p>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="paymentModalTitle">Process Payment</h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;" id="paymentModalOrderInfo">Order # - Total: ₱0.00</p>
                </div>
                <button class="btn-icon" onclick="closeModal('paymentModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="paymentForm">
                <input type="hidden" id="payOrderId">
                <input type="hidden" id="payTotalAmount">
                
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select id="paymentMethod" class="form-control" onchange="window.togglePaymentView()" required>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash / Online</option>
                    </select>
                </div>

                <!-- Cash Section -->
                <div id="cashSection">
                    <div class="form-group">
                        <label class="form-label">Amount Received (₱)</label>
                        <input type="number" id="amountReceived" class="form-control" step="0.01" min="0" oninput="window.calculateChange()" placeholder="Enter cash given">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Change (Sukli)</label>
                        <div id="changeLabel" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-gold); padding: 0.5rem; background: rgba(201,169,97,0.1); border-radius: 8px; text-align: center;">₱0.00</div>
                    </div>
                </div>

                <!-- Online Section -->
                <div id="onlineSection" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">Reference Number</label>
                        <input type="text" id="referenceNumber" class="form-control" placeholder="GCash reference number">
                    </div>
                    <div id="proofContainer" style="margin-top:1rem; text-align:center; display:none;">
                        <button type="button" class="btn-action" id="viewProofBtn" style="width:100%; justify-content:center; background:#e3f2fd; color:#1e88e5;">
                            <i class="fas fa-image"></i> View Customer Proof
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" style="margin-top:1rem;">
                    <i class="fas fa-check-circle" style="margin-right:8px;"></i> Confirm Payment
                </button>
            </form>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="imageViewerModal" class="modal">
        <div class="modal-content" style="max-width:90%; text-align:center; background:transparent; border:none; box-shadow:none; padding:0;">
            <div style="position:relative; display:inline-block;">
                <button class="btn-icon" onclick="closeModal('imageViewerModal')" style="position:absolute; top:-40px; right:0; background:white; border-radius:50%; z-index:100;"><i class="fas fa-times"></i></button>
                <img id="viewerImage" src="" style="max-width:100%; max-height:85vh; border-radius:8px; border:4px solid white; box-shadow:0 10px 40px rgba(0,0,0,0.8);">
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Form digit and negative value limiter
        window.limitDigits = function(input, maxDigits) {
            if (input.value < 0) {
                input.value = Math.abs(input.value);
            }
            const parts = input.value.toString().split('.');
            if (parts[0].length > maxDigits) {
                parts[0] = parts[0].slice(0, maxDigits);
                input.value = parts.join('.');
            }
            if (parseFloat(input.value) > 99999) {
                input.value = 99999;
            }
        };

        // UI Helpers
        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { 
            // Safety check: Don't allow closing while saving
            if (id === 'menuModal' && window.isSaveInProgress && window.isSaveInProgress()) {
                alert("Please wait until saving is finished.");
                return;
            }
            document.getElementById(id).classList.remove('active'); 
        }
        window.openModal = openModal;
        window.closeModal = closeModal;
        
        // Navigation Logic
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                // Allow external/new tab links to work normally
                if (link.getAttribute('target') === '_blank') return;

                e.preventDefault();
                
                // Active State
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                // Show Section
                const target = link.dataset.target;
                document.querySelectorAll('.section-view').forEach(s => s.classList.remove('active'));
                document.getElementById(`${target}-section`).classList.add('active');
                
                // Update Title
                const titleMap = {
                    'overview': 'Dashboard Overview',
                    'analytics': 'Analytics & Insights',
                    'orders': 'Order Management',
                    'menu': 'Menu Management',
                    'inventory': 'Inventory Management',
                    'users': 'User Management',
                    'customers': 'Customer Database',
                    'reservations': 'Table Reservations',
                    'delivery': 'Delivery Tracking',
                    'reviews': 'Customer Reviews',
                    'promotions': 'Promotions & Discounts',
                    'reports': 'Sales Reports',
                    'settings': 'Settings'
                };
                document.getElementById('pageTitle').textContent = titleMap[target];
            });
        });
    </script>

    <!-- Auth Guard & Dashboard Logic -->
    <!-- Auth Guard & Dashboard Logic -->
    <!-- Detailed Dashboard Overview Modal -->
    <div id="overviewModal" class="modal">
        <div class="modal-content overview-modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Business Overview</h2>
                    <p style="font-size:0.8rem; color:var(--text-muted);">Detailed financial and operational breakdown</p>
                </div>
                <span class="close-btn" onclick="closeModal('overviewModal')" style="cursor:pointer; font-size:1.5rem;">&times;</span>
            </div>
            <div class="modal-body">
                <!-- Top Metrics Row -->
                <div class="overview-metrics-grid">
                    <div class="metric-card">
                        <h3>Total Revenue</h3>
                        <p id="modalRevenue">₱0</p>
                    </div>
                     <div class="metric-card">
                        <h3>Total Expenses</h3>
                        <p id="modalExpenses">₱0</p>
                        <small>(Estimated 60% of Revenue)</small>
                    </div>
                     <div class="metric-card">
                        <h3>Net Income</h3>
                        <p id="modalNetIncome">₱0</p>
                        <small style="color:var(--success);">+40% Margin</small>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="overview-charts-grid">
                    <div class="chart-container">
                        <h4 style="margin-bottom:1rem; color:var(--dark-brown);">Revenue Trend</h4>
                        <canvas id="modalRevenueChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h4 style="margin-bottom:1rem; color:var(--dark-brown);">Order Status Breakdown</h4>
                        <canvas id="modalOrderBreakdownChart"></canvas>
                    </div>
                </div>

                 <!-- Lists Row -->
                <div class="overview-lists-grid">
                    <div class="list-container">
                         <h4>Top Selling Items</h4>
                         <ul id="topSellingList" class="styled-list">
                             <li style="justify-content:center; color:#ccc;">Loading...</li>
                         </ul>
                    </div>
                     <div class="list-container">
                         <h4>Recent Activity</h4>
                         <table class="data-table" style="font-size:0.85rem;">
                             <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                             </thead>
                             <tbody id="modalRecentOrders">
                                 <!-- Populated by JS -->
                             </tbody>
                         </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kitchen Display System (Hidden Section) -->
    <div id="kitchen-view-section" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#1a1a1a; z-index:9999; overflow-y:auto; padding:20px;">
        <div class="kitchen-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid #333; padding-bottom:15px;">
            <div style="display:flex; align-items:center; gap:15px;">
                <h1 style="color:var(--primary-gold); font-family:'Playfair Display', serif; font-size:2rem;"><i class="fas fa-utensils"></i> KITCHEN DISPLAY</h1>
                <span id="kdsClock" style="color:#888; font-size:1.2rem; font-family:monospace;">--:--:--</span>
            </div>
            <button id="exitKdsBtn" style="background:#dc3545; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer;">
                <i class="fas fa-times"></i> EXIT KDS
            </button>
        </div>
        
        <div id="kdsGrid" class="kitchen-grid">
            <!-- Orders Rendered Here -->
            <div style="color:#666; text-align:center; grid-column:1/-1; padding:50px; font-size:1.5rem;">Waiting for orders...</div>
        </div>
    </div>

    <script type="module" src="../assets/js/admin-auth.js"></script>
    <script type="module" src="../assets/js/admin-dashboard.js?v=2.3"></script>

    <script>
        // Modal Helpers (Global)
        window.openModal = (id) => {
            const m = document.getElementById(id);
            if (m) m.classList.add('active');
        };
        window.closeModal = (id) => {
            const m = document.getElementById(id);
            if (m) m.classList.remove('active');
        };
        
        // Open Payment Modal
        window.openPaymentModal = (id) => {
            const orderData = window.ordersData && window.ordersData[id];
            if (!orderData) return alert('Order data not found!');

            const total = orderData.totalAmount;
            const proofImage = orderData.paymentDetails?.proofImage || '';

            document.getElementById('payOrderId').value = id;
            document.getElementById('payTotalAmount').value = total;
            document.getElementById('paymentModalOrderInfo').textContent = `Order #${id.slice(0,6)} - Total: ₱${total.toLocaleString()}`;
            
            // Reset Defaults
            document.getElementById('paymentMethod').value = 'Cash';
            document.getElementById('amountReceived').value = '';
            document.getElementById('referenceNumber').value = '';
            
            // Handle Proof Image & Auto-select GCash
            const proofContainer = document.getElementById('proofContainer');
            const viewProofBtn = document.getElementById('viewProofBtn');
            if (proofImage && proofImage !== '') {
                document.getElementById('paymentMethod').value = 'GCash';
                if (orderData.paymentDetails?.referenceNumber) {
                    document.getElementById('referenceNumber').value = orderData.paymentDetails.referenceNumber;
                }
                proofContainer.style.display = 'block';
                viewProofBtn.onclick = () => window.viewProof(proofImage);
            } else {
                proofContainer.style.display = 'none';
            }

            window.togglePaymentView();
            window.calculateChange();
            
            window.openModal('paymentModal');
        };

        // Toggle Payment View
        window.togglePaymentView = () => {
            const method = document.getElementById('paymentMethod').value;
            document.getElementById('cashSection').style.display = method === 'Cash' ? 'block' : 'none';
            document.getElementById('onlineSection').style.display = method === 'GCash' ? 'block' : 'none';
        };

        // Calculate Change
        window.calculateChange = () => {
            const total = parseFloat(document.getElementById('payTotalAmount').value) || 0;
            const amountInput = document.getElementById('amountReceived');
            let received = parseFloat(amountInput.value) || 0;
            
            // Limit characters and value (e.g. max 1,000,000 PHP)
            if (amountInput.value.length > 7) {
                amountInput.value = amountInput.value.slice(0, 7);
                received = parseFloat(amountInput.value) || 0;
            }
            if (received > 1000000) {
                amountInput.value = 1000000;
                received = 1000000;
            }

            const change = received - total;
            const label = document.getElementById('changeLabel');
            
            if (received >= total) {
                label.textContent = '₱' + change.toFixed(2);
                label.style.color = 'var(--primary-gold)';
            } else {
                label.textContent = 'Insufficient';
                label.style.color = '#dc3545';
            }
        };
    </script>

    <script>
        // Sidebar Toggle Logic
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const main = document.querySelector('.main-content');
        
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    </script>
    <!-- Admin Profile Modal -->
    <div class="modal" id="adminProfileModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Profile</h3>
                <button class="btn-icon" onclick="closeModal('adminProfileModal')"><i class="fas fa-times"></i></button>
            </div>
            
            <div style="display:flex; justify-content:center; margin-bottom: 2rem;">
                <div style="position:relative; width:100px; height:100px;">
                    <img id="editProfileAvatar" src="" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid var(--primary-gold); background:#eee;">
                    <button onclick="document.getElementById('profileAvatarInput').click()" style="position:absolute; bottom:0; right:0; background:var(--dark-brown); color:white; border:none; width:30px; height:30px; border-radius:50%; box-shadow:0 3px 6px rgba(0,0,0,0.2); cursor:pointer; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-camera" style="font-size:0.8rem;"></i>
                    </button>
                    <!-- Simulated Upload (URL based for now, or future expansion) -->
                    <input type="text" id="profileAvatarInput" placeholder="Enter Image URL" style="display:none;" onchange="document.getElementById('editProfileAvatar').src = this.value">
                </div>
            </div>

            <form id="adminProfileForm">
                <div class="row" style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">First Name</label>
                        <input type="text" id="editProfileFirstName" class="form-control" required placeholder="First">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Middle Name</label>
                        <input type="text" id="editProfileMiddleName" class="form-control" placeholder="Middle">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Last Name</label>
                        <input type="text" id="editProfileLastName" class="form-control" required placeholder="Last">
                    </div>
                </div>
                <div class="row" style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Birthday</label>
                        <input type="date" id="editProfileBirthday" class="form-control">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Username</label>
                        <input type="text" id="editProfileUsername" class="form-control" required placeholder="User">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email (Read Only)</label>
                    <input type="email" id="editProfileEmail" class="form-control" readonly style="background:#f5f5f5; color:#888;">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" id="editProfilePhone" class="form-control" placeholder="09XX...">
                </div>
                <div class="form-group">
                    <label class="form-label">Avatar URL (Optional)</label>
                    <input type="url" id="editProfileAvatarUrl" class="form-control" placeholder="https://image-url.com/avatar.jpg" oninput="document.getElementById('editProfileAvatar').src = this.value || 'https://ui-avatars.com/api/?name=Admin'">
                </div>

                <div class="form-group" style="border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem;">
                    <label class="form-label" style="font-weight: 700; color: var(--dark-brown);">Account Security (Password & 2FA PIN)</label>
                    <p style="font-size: 0.8rem; color: #666; margin-bottom: 0.8rem;">Leave blank if you don't want to change them.</p>

                    <div style="position: relative; margin-bottom: 0.8rem;">
                        <input type="password" id="editAdminPin" class="form-control" placeholder="Setup / Change 4-Digit PIN" maxlength="4" pattern="[0-9]{4}" style="padding-right: 40px; letter-spacing: 5px; text-align: center; font-size: 1.2rem; font-weight: 600;" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        <i class="fas fa-eye toggle-admin-password" data-target="editAdminPin" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999;"></i>
                    </div>
                    
                    <div style="position: relative; margin-bottom: 0.8rem;">
                        <input type="password" id="editAdminCurrentPassword" class="form-control" placeholder="Current Password (Required for changes)" style="padding-right: 40px;">
                        <i class="fas fa-eye toggle-admin-password" data-target="editAdminCurrentPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999;"></i>
                    </div>

                    <div style="position: relative; margin-bottom: 0.8rem;">
                        <input type="password" id="editAdminNewPassword" class="form-control" placeholder="New Password" style="padding-right: 40px;">
                        <i class="fas fa-eye toggle-admin-password" data-target="editAdminNewPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999;"></i>
                    </div>

                    <div style="position: relative;">
                        <input type="password" id="editAdminConfirmPassword" class="form-control" placeholder="Confirm New Password" style="padding-right: 40px;">
                        <i class="fas fa-eye toggle-admin-password" data-target="editAdminConfirmPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999;"></i>
                    </div>
                </div>
                
                <div style="margin-top:20px; text-align:right;">
                    <button type="button" class="btn-cancel" onclick="closeModal('adminProfileModal')" style="margin-right:10px; background:#ddd; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">Cancel</button>
                    <button type="submit" class="btn-submit" id="saveProfileBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Broadcast/Message Modal -->
    <div class="modal" id="broadcastModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Send Message</h3>
                    <p id="broadcastRecipientCount" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">0 recipients selected</p>
                </div>
                <button class="btn-icon" onclick="closeModal('broadcastModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="broadcastForm">
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" id="broadcastSubject" class="form-control" placeholder="Notification Subject" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea id="broadcastMessage" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-cancel" onclick="closeModal('broadcastModal')" style="padding: 10px 20px; border: none; background: #eee; border-radius: 8px; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-submit" id="sendBroadcastBtn" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe) !important; color: white !important;">
                        <i class="fas fa-paper-plane"></i> Send Now
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Account Approval Details Modal (Premium View) -->
    <div id="approvalInfoModal" class="modal">
        <div class="modal-content" style="max-width: 500px; border-radius: 20px; overflow: hidden; border: none; box-shadow: var(--shadow-lg); background: white;">
            <div style="background: linear-gradient(135deg, var(--dark-brown), #3d2217); padding: 2.5rem 2rem; text-align: center; position: relative;">
                <div class="modal-close" onclick="closeModal('approvalInfoModal')" style="position: absolute; right: 20px; top: 20px; color: rgba(255,255,255,0.7); cursor: pointer; font-size: 1.2rem; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                    <i class="fas fa-times"></i>
                </div>
                <div style="font-size: 0.7rem; color: var(--primary-gold); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; font-weight: 800;">Pending Approval Info</div>
                <div style="width: 130px; height: 130px; margin: 0 auto 1.2rem; position: relative;">
                    <img id="approvalUserAvatar" src="" style="width: 100%; height: 100%; border-radius: 50%; border: 4px solid var(--primary-gold); object-fit: cover; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div id="approvalUserBadge" style="position: absolute; bottom: 5px; right: 5px; background: var(--primary-gold); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0,0,0,0.2); border: 2px solid white;">
                        PENDING
                    </div>
                </div>
                <h2 id="approvalUserFullName" style="color: var(--primary-gold); margin: 0; font-family: 'Playfair Display', serif; font-size: 1.8rem; letter-spacing: 0.5px;">User Name</h2>
                <p id="approvalUserUsername" style="color: #c0b7af; margin: 8px 0 0; font-size: 1rem; font-weight: 500;">@username</p>
            </div>
            
            <div style="padding: 2.5rem; background: white;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2.5rem;">
                    <div class="info-item">
                        <label style="display: block; font-size: 0.7rem; color: #aaa; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700;">Email Address</label>
                        <div id="approvalUserEmail" style="font-weight: 600; color: var(--dark-brown); font-size: 0.95rem; word-break: break-all;">-</div>
                    </div>
                    <div class="info-item">
                        <label style="display: block; font-size: 0.7rem; color: #aaa; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700;">Role Designation</label>
                        <div id="approvalUserRole" style="font-weight: 600; color: var(--dark-brown); font-size: 0.95rem;">Customer</div>
                    </div>
                    <div class="info-item">
                        <label style="display: block; font-size: 0.7rem; color: #aaa; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700;">Date of Birth</label>
                        <div id="approvalUserBday" style="font-weight: 600; color: var(--dark-brown); font-size: 0.95rem;">-</div>
                    </div>
                    <div class="info-item">
                        <label style="display: block; font-size: 0.7rem; color: #aaa; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700;">Member Since</label>
                        <div id="approvalUserJoined" style="font-weight: 600; color: var(--dark-brown); font-size: 0.95rem;">-</div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button class="btn-action" id="approvalApproveBtn" style="flex: 2; background: var(--success); color: white; border: none; padding: 14px; border-radius: 12px; cursor: pointer; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 1rem; box-shadow: 0 4px 15px rgba(45, 159, 93, 0.2); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(45, 159, 93, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(45, 159, 93, 0.2)'">
                        <i class="fas fa-check-circle"></i> Approve Account
                    </button>
                    <button class="btn-action" id="approvalRejectBtn" style="flex: 1; background: #fff; color: #dc3545; border: 1.5px solid #dc3545; padding: 14px; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 0.95rem; transition: all 0.2s;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                        Reject
                    </button>
                </div>
                
                <button class="btn-action" onclick="closeModal('approvalInfoModal')" style="width: 100%; margin-top: 15px; background: #fafafa; color: #999; border: 1px solid #eee; padding: 12px; border-radius: 12px; cursor: pointer; font-weight: 500; font-size: 0.9rem; transition: all 0.2s;" onmouseover="this.style.background='#f5f5f5'; this.style.color='#666'">
                    Close Details
                </button>
            </div>
        </div>
    </div>
    <!-- POS Modal (Cashier) -->
    <div id="posModal" class="modal">
        <div class="modal-content" style="max-width: 900px; display: flex; flex-direction: column; height: 85vh; padding: 0; border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="padding: 1rem 1.5rem; background: var(--dark-brown); color: var(--white); border-bottom: 3px solid var(--primary-gold);">
                <h3 class="modal-title" style="margin:0; font-family:'Playfair Display', serif; color: var(--primary-gold);">Walk-in POS</h3>
                <i class="fas fa-times modal-close" style="cursor:pointer; font-size: 1.2rem;" onclick="closeModal('posModal')"></i>
            </div>
            
            <div style="display: flex; flex: 1; overflow: hidden; background: var(--light-bg);">
                <!-- Products Side -->
                <div style="flex: 2; padding: 1.5rem; overflow-y: auto; border-right: 1px solid var(--border-color);">
                    <div style="margin-bottom: 1rem; display: flex; gap: 10px;">
                        <input type="text" id="posSearch" placeholder="Search menu..." class="form-control" style="flex:1;" onkeyup="window.filterPOSItems(this.value)">
                        <select id="posCategory" class="form-control" style="width: 150px;" onchange="window.filterPOSItems()">
                            <option value="All">All Categories</option>
                            <option value="All Day Breakfast">Breakfast</option>
                            <option value="Hot Coffee">Hot Coffee</option>
                            <option value="Ice Beverages">Ice Beverages</option>
                            <option value="Desserts">Desserts</option>
                        </select>
                    </div>
                    <div id="posItemsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px;">
                        <!-- JS populated -->
                    </div>
                </div>

                <!-- Cart Side -->
                <div style="flex: 1.2; display: flex; flex-direction: column; background: white; padding: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #555;">Order Type</label>
                        <select id="posOrderType" class="form-control" onchange="window.toggleTableInput()">
                            <option value="Dine-in">Dine-in</option>
                            <option value="Take-out">Take-out</option>
                            <option value="Delivery">Delivery (Manual)</option>
                        </select>
                    </div>
                    
                    <div id="posTableNumberGroup" style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #555;">Table Number</label>
                        <input type="number" id="posTableNumber" class="form-control" placeholder="e.g. 5">
                    </div>

                    <div style="flex: 1; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 1rem; padding: 10px;">
                        <ul id="posCartList" style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;">
                            <!-- JS populated -->
                            <li style="text-align: center; color: #999; margin-top: 2rem;">Cart is empty</li>
                        </ul>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; border: 1px dashed #ccc;">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                            <span>Subtotal:</span>
                            <strong id="posSubtotal">₱0.00</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom: 15px; font-size: 1.2rem; color: var(--dark-brown);">
                            <strong>Total:</strong>
                            <strong id="posTotal">₱0.00</strong>
                        </div>
                        <button id="posCheckoutBtn" class="btn-submit" style="width: 100%; font-size: 1.1rem; padding: 12px; border-radius: 8px; font-weight: 800;" onclick="window.posCheckout()">
                            Create Order & Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Summary Modal -->
    <div id="shiftSummaryModal" class="modal">
        <div class="modal-content" style="max-width: 400px; padding: 0; border-radius: 12px; overflow: hidden;">
             <div class="modal-header" style="background: var(--dark-brown); color: var(--primary-gold); padding: 1.5rem; text-align: center;">
                 <h2 style="margin:0; font-family: 'Playfair Display', serif;">Shift Summary</h2>
                 <p style="margin: 5px 0 0; font-size: 0.85rem; color: #ccc;">End of day or shift calculation</p>
                 <i class="fas fa-times modal-close" style="position: absolute; right: 20px; top: 20px; cursor:pointer;" onclick="closeModal('shiftSummaryModal')"></i>
             </div>
             <div style="padding: 2rem; background: var(--light-bg);">
                 <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                     <span style="color: #666;">Total Cash Sales:</span>
                     <strong id="shiftCashTotal" style="color: #2D9F5D;">₱0.00</strong>
                 </div>
                 <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 10px;">
                     <span style="color: #666;">Total Online (GCash, etc):</span>
                     <strong id="shiftOnlineTotal" style="color: #004085;">₱0.00</strong>
                 </div>
                 <div style="display: flex; justify-content: space-between; font-size: 1.2rem; margin-top: 20px;">
                     <strong>Total Shift Sales:</strong>
                     <strong id="shiftGrandTotal" style="color: var(--dark-brown);">₱0.00</strong>
                 </div>
                 <button class="btn-submit" style="width: 100%; margin-top: 20px; border-radius: 8px; background: var(--dark-brown); color: var(--primary-gold);" onclick="closeModal('shiftSummaryModal'); alert('Shift report saved/printed!');">
                     Close Shift
                 </button>
             </div>
        </div>
    </div>

</body>
</html>
