<?php
session_start();

// Enforce Session Validation
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: ../login.php'); // Assuming public login page for rider
    exit();
}

// Enforce Role
if (!isset($_SESSION['2fa_role']) || !in_array($_SESSION['2fa_role'], ['rider', 'admin', 'super_admin'])) {
    header('Location: ../login.php');
    exit();
}

// Le Maison - Rider Portal (SPA Delivery Dashboard)
// Theme: Admin Dashboard Clone (Sidebar + Hamburger Menu)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rider Dashboard - Le Maison</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brown: '#2C1810',
                        gold: '#C9A961',
                        'gold-dark': '#b08d4b',
                        'light-bg': '#F7F5F2'
                    }
                }
            }
        }
    </script>

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
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        /* Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (Admin Clone) */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #2C1810 0%, #1a0f0a 100%);
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
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--primary-gold); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #b08d4b; }

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

        /* Sidebar Collapsed State */
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed .logo span, 
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .nav-section-label,
        .sidebar.collapsed .user-info { display: none; }
        .sidebar.collapsed .logo { justify-content: center; padding: 0; }
        .sidebar.collapsed .nav-item a { justify-content: center; padding: 1rem; }
        .sidebar.collapsed .nav-item i { margin: 0; font-size: 1.4rem; }
        .sidebar.collapsed .badge { position: absolute; top: 5px; right: 5px; } /* Adjust badge for collapsed */

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
            position: relative;
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
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            letter-spacing: 0.3px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem 2.5rem;
            transition: var(--transition);
            min-height: 100vh;
        }
        .main-content.expanded { margin-left: 80px; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--dark-brown);
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        /* New Header Buttons */
        #sidebarToggle {
            background: none; border: none; 
            color: var(--dark-brown); font-size: 1.5rem; 
            cursor: pointer; margin-right: 15px;
            transition: var(--transition);
        }
        #sidebarToggle:hover { color: var(--primary-gold); }

        /* Status Badge */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-offline { background: #f0f0f0; color: #999; }
        .status-online { background: rgba(45, 159, 93, 0.1); color: var(--success); }

        /* SPA Views */
        .view-section { display: none; opacity: 0; transition: opacity 0.3s ease-in-out; }
        .view-section.active { display: block; opacity: 1; }

        /* Cards & Components */
        .glass-panel {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }
        .glass-panel:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--gold-light);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-brown) 100%);
            color: var(--white);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(201, 169, 97, 0.4);
            transition: transform 0.2s;
        }
        .btn-primary:active { transform: scale(0.98); }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar { width: 0; padding: 0; overflow: hidden; } /* Hide completely on mobile initially */
            .sidebar.mobile-open { width: 260px; padding: 2rem 0; } /* Slide in */
            .main-content { margin-left: 0 !important; padding: 1.5rem; }
            .header { margin-bottom: 2rem; }
            .page-title { font-size: 1.5rem; }
            
            /* Overlay for mobile sidebar */
            #sidebarOverlay {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); z-index: 90;
                display: none;
            }
            #sidebarOverlay.active { display: block; }
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <a href="#" class="logo">
                <i class="fas fa-motorcycle"></i>
                <span>Le Maison</span>
            </a>
            
            <ul class="nav-links">
                <div class="nav-section-label">Menu</div>
                <li class="nav-item">
                    <a href="#" onclick="showView('dashboard')" id="nav-dashboard" class="active">
                        <i class="fas fa-home"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="showView('marketplace')" id="nav-marketplace">
                        <i class="fas fa-store"></i> <span>Available Orders</span>
                        <span id="badge-avail" class="hidden bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-auto font-bold">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="showView('active')" id="nav-active">
                        <i class="fas fa-shipping-fast"></i> <span>Current Delivery</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" onclick="showView('history')" id="nav-history">
                        <i class="fas fa-history"></i> <span>History</span>
                    </a>
                </li>

                <div class="nav-divider" style="height:1px; background:rgba(255,255,255,0.1); margin:1rem 1.5rem;"></div>
                
                <li class="nav-item">
                    <a href="#" onclick="window.logoutRider()">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>

            <div class="user-info">
                <p id="riderNameSide" class="font-bold text-gold">Rider</p>
                <p class="text-xs opacity-60">Rider Portal v2.0</p>
            </div>
        </nav>

        <!-- Mobile Overlay -->
        <div id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            
            <!-- Header -->
            <header class="header">
                <div style="display:flex; align-items:center;">
                    <button id="sidebarToggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title" id="pageTitle">Dashboard</h1>
                </div>
                
                <div id="statusHeader" class="status-badge status-offline">
                    <div id="statusDot" class="w-2 h-2 rounded-full bg-gray-400"></div>
                    <span id="statusText">Offline</span>
                </div>
            </header>

            <!-- 1. DASHBOARD VIEW -->
            <section id="view-dashboard" class="view-section active">
                <div class="mb-8 p-6 glass-panel flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-gold/10 rounded-full flex items-center justify-center text-gold text-2xl mb-3">
                        <i class="fas fa-user-astronaut"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-brown mb-1">Welcome Back, <span id="riderNameDash">Rider</span></h2>
                    <p class="text-gray-500 mb-6">Ready to hit the road based on your schedule?</p>
                    
                    <button id="mainToggleBtn" class="px-8 py-3 rounded-xl bg-gray-800 text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all flex items-center gap-3">
                        <i class="fas fa-power-off"></i> <span>GO ONLINE</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="glass-panel">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-gold/10 flex items-center justify-center text-gold text-xl">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase font-bold text-gray-400 tracking-wider">Today's Earnings</p>
                                <h3 class="text-2xl font-bold text-brown">₱<span id="stat-earnings">0.00</span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="glass-panel">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-brown/10 flex items-center justify-center text-brown text-xl">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase font-bold text-gray-400 tracking-wider">Cash on Hand</p>
                                <h3 class="text-2xl font-bold text-brown">₱<span id="stat-cod">0.00</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <button id="remit-btn" disabled class="px-6 py-2 bg-brown text-gold border border-gold rounded-lg shadow hover:bg-brown/90 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-bold uppercase tracking-wider">
                        Request Remittance <i class="fas fa-hand-holding-usd ml-2"></i>
                    </button>
                    <p class="text-[10px] text-gray-400 mt-2">Remit cash when you return to base.</p>
                </div>
            </section>

            <!-- 2. MARKETPLACE VIEW -->
            <section id="view-marketplace" class="view-section">
                <div id="marketplace-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Orders Injected Here -->
                    <div class="col-span-full py-20 text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin text-3xl mb-4 text-gold"></i>
                        <p>Scanning for orders...</p>
                    </div>
                </div>
            </section>

            <!-- 3. ACTIVE DELIVERY VIEW -->
            <section id="view-active" class="view-section">
                <div id="no-active-order" class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-400 text-4xl">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brown mb-2">No Active Mission</h3>
                    <p class="text-gray-500 mb-6">Check 'Available Orders' to start earning.</p>
                    <button onclick="showView('marketplace')" class="px-6 py-2 bg-gold text-white font-bold rounded-lg shadow hover:bg-gold-dark transition">Find Orders</button>
                </div>

                <div id="active-order-content" class="hidden max-w-2xl mx-auto">
                    <!-- Header Card -->
                    <div class="bg-brown text-gold p-6 rounded-t-2xl shadow-lg flex justify-between items-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gold/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                        <div class="relative z-10">
                            <p class="text-xs uppercase tracking-widest opacity-70 font-bold">Status</p>
                            <h2 class="text-2xl font-bold mt-1" id="active-status-text">Preparing</h2>
                        </div>
                        <div class="relative z-10 w-12 h-12 bg-gold/20 rounded-xl flex items-center justify-center text-xl border border-gold/30">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>

                    <!-- Details Card -->
                    <div class="bg-white p-8 rounded-b-2xl shadow-md border border-gray-100 mb-6">
                        <div class="text-center mb-8 pb-8 border-b border-dashed border-gray-200">
                            <h2 class="text-3xl font-serif font-bold text-brown mb-1" id="active-customer">Guest</h2>
                            <p class="text-sm text-gray-400 font-mono mb-4" id="active-id">#ORDER-ID</p>
                            <a href="#" id="active-call" class="inline-flex items-center gap-2 text-gold-dark font-bold hover:text-brown transition">
                                <i class="fas fa-phone-alt"></i> Call Customer
                            </a>
                        </div>

                        <div class="flex items-start gap-4 mb-6">
                            <div class="mt-1"><i class="fas fa-map-pin text-red-500"></i></div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase">Delivery Address</label>
                                <p class="text-lg text-brown font-medium leading-relaxed" id="active-address">---</p>
                                <button id="active-map-btn" class="mt-2 text-sm text-blue-600 font-bold hover:underline">
                                    <i class="fas fa-external-link-alt"></i> Open in Maps
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 transition hover:border-gold/30">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Order Amount</p>
                                <h3 class="text-xl font-bold text-brown" id="active-total">₱0.00</h3>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs font-bold text-gray-500 mb-1" id="active-method">COD</span>
                                <span id="active-payment-status" class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded">UNPAID</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Items</label>
                            <ul id="active-items" class="space-y-3 text-gray-600 text-sm"></ul>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button id="workflow-action-btn" class="w-full py-4 bg-gold text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl hover:bg-gold-dark transition transform active:scale-95 uppercase tracking-wide">
                        LOADING ACTION...
                    </button>
                    
                    <button onclick="cancelDelivery()" class="w-full mt-4 py-3 text-red-400 text-sm font-bold hover:text-red-600 transition">
                        Report Issue / Cancel
                    </button>
                </div>
            </section>

            <!-- 4. HISTORY VIEW -->
            <section id="view-history" class="view-section">
                <div class="glass-panel p-0 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="font-bold text-brown">Delivery History</h2>
                    </div>
                    <div id="history-list" class="divide-y divide-gray-100">
                        <div class="p-8 text-center text-gray-400">Loading history...</div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- TOAST -->
    <div id="toast" class="fixed top-6 right-6 bg-brown text-white px-6 py-4 rounded-xl shadow-2xl transition-all duration-300 translate-x-full opacity-0 z-50 flex items-center gap-4 border-l-4 border-gold min-w-[300px]">
        <i class="fas fa-bell text-gold text-xl"></i>
        <div>
            <h4 class="font-bold text-sm">Notification</h4>
            <p id="toast-msg" class="text-xs text-gray-300 mt-1">Message here</p>
        </div>
    </div>

    <!-- FIREBASE & LOGIC -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
        import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
        import { getFirestore, doc, setDoc, updateDoc, arrayUnion, collection, query, where, onSnapshot, serverTimestamp, getDocs, orderBy, limit } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
        import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-storage.js";
        import { firebaseConfig } from "../assets/js/firebase-config.js";

        // INIT
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);
        const storage = getStorage(app);

        let currentUser = null;
        let watchId = null;
        let isOnline = false;
        let activeOrder = null;

        // AUTH CHECK
        onAuthStateChanged(auth, (user) => {
            if (user) {
                currentUser = user;
                // Update names
                const name = user.displayName || user.email;
                if(document.getElementById('riderNameSide')) document.getElementById('riderNameSide').textContent = name;
                if(document.getElementById('riderNameDash')) document.getElementById('riderNameDash').textContent = name;
                
                initApp();
            } else {
                window.location.href = '../login.php';
            }
        });

        function initApp() {
            console.log("App Init: Rider", currentUser.uid);
            loadDashboardStats();
            listenForAvailableOrders();
            listenForActiveDelivery();
            loadHistory();
        }

        // --- NAVIGATION & SIDEBAR LOGIC ---
        window.toggleSidebar = function() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Check if mobile (simple check based on window width)
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('expanded');
            }
        };

        window.showView = function(viewName) {
            // Hide all views
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
            // Show target
            document.getElementById(`view-${viewName}`).classList.add('active');

            // Update Nav State
            document.querySelectorAll('.nav-item a').forEach(el => el.classList.remove('active'));
            const navLink = document.getElementById(`nav-${viewName}`);
            if(navLink) navLink.classList.add('active');
            
            // Update Page Title
            const titles = {
                'dashboard': 'Dashboard',
                'marketplace': 'Available Orders',
                'active': 'Current Delivery',
                'history': 'History'
            };
            document.getElementById('pageTitle').textContent = titles[viewName] || 'Dashboard';

            // Close mobile sidebar if open
            if(window.innerWidth <= 768) {
                 document.getElementById('sidebar').classList.remove('mobile-open');
                 document.getElementById('sidebarOverlay').classList.remove('active');
            }
        };

        // --- GPS & DASHBOARD ---
        const toggleBtn = document.getElementById('mainToggleBtn');
        toggleBtn.addEventListener('click', () => isOnline ? goOffline() : goOnline());

        function goOnline() {
            if (!navigator.geolocation) return showToast("GPS not supported on this device.");
            
            toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>CONNECTING...</span>';
            
            watchId = navigator.geolocation.watchPosition(
                (pos) => {
                    updateLocation(pos.coords);
                    if(!isOnline) {
                        isOnline = true;
                        updateUIOnline(true);
                        showToast("You are now ONLINE 🟢");
                    }
                },
                (err) => {
                    console.error(err);
                    showToast("GPS Error: " + err.message);
                    goOffline();
                },
                { enableHighAccuracy: true, maximumAge: 0 }
            );
        }

        function goOffline() {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            watchId = null;
            isOnline = false;
            updateUIOnline(false);
            if (currentUser) updateDoc(doc(db, "riders", currentUser.uid), { isOnline: false });
            showToast("You are now OFFLINE 🔴");
        }

        async function updateLocation(coords) {
            if (!currentUser) return;
            await setDoc(doc(db, "riders", currentUser.uid), {
                name: currentUser.displayName,
                email: currentUser.email,
                location: { 
                    lat: coords.latitude, 
                    lng: coords.longitude, 
                    heading: coords.heading || 0,
                    speed: coords.speed || 0 
                },
                lastUpdated: serverTimestamp(),
                isOnline: true
            }, { merge: true });
        }

        function updateUIOnline(online) {
            const statusBadge = document.getElementById('statusHeader');
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');
            
            if (online) {
                toggleBtn.className = "px-8 py-3 rounded-xl bg-green-600 text-white font-bold shadow-lg hover:bg-green-700 transition flex items-center gap-3";
                toggleBtn.innerHTML = '<i class="fas fa-satellite-dish animate-pulse"></i> <span>ONLINE</span>';
                
                statusBadge.className = "status-badge status-online";
                statusDot.className = "w-2 h-2 rounded-full bg-green-500 animate-pulse";
                statusText.textContent = "Online";
            } else {
                toggleBtn.className = "px-8 py-3 rounded-xl bg-gray-800 text-white font-bold shadow-lg hover:bg-gray-900 transition flex items-center gap-3";
                toggleBtn.innerHTML = '<i class="fas fa-power-off"></i> <span>GO ONLINE</span>';
                
                statusBadge.className = "status-badge status-offline";
                statusDot.className = "w-2 h-2 rounded-full bg-gray-400";
                statusText.textContent = "Offline";
            }
        }

        async function loadDashboardStats() {
            // Placeholder logic for stats
            const q = query(
                collection(db, "orders"), 
                where("rider_id", "==", currentUser.uid), 
                where("status", "==", "completed")
            );
            // Fetch once (not listener) for optimization
            const snaps = await getDocs(q);
            let earnings = 0;
            let cashOnHand = 0;
            
            snaps.forEach(doc => {
                const d = doc.data();
                const total = parseFloat(d.totalAmount || d.total || 0);
                earnings += total * 0.10; // 10% commission logic
                if (d.paymentMethod === 'Cash' || d.paymentMethod === 'COD') cashOnHand += total;
            });

            document.getElementById('stat-earnings').textContent = earnings.toFixed(2);
            document.getElementById('stat-cod').textContent = cashOnHand.toFixed(2);
            
            // Remittance Button Logic
            const remitBtn = document.getElementById('remit-btn');
            if(remitBtn) {
                remitBtn.disabled = cashOnHand <= 0;
                remitBtn.onclick = () => requestRemittance(cashOnHand);
            }
        }

        async function requestRemittance(amount) {
            if(!confirm(`Request to remit ₱${amount.toFixed(2)} to Admin?`)) return;
            
            try {
                const remitRef = collection(db, "remittances");
                await setDoc(doc(remitRef), {
                    rider_id: currentUser.uid,
                    rider_name: currentUser.displayName || currentUser.email,
                    amount: amount,
                    status: 'pending',
                    createdAt: serverTimestamp()
                });
                
                showToast("Remittance Requested! 💸");
                // Optional: You might want to update local state or disable button until processed
            } catch (err) {
                console.error("Remittance Error:", err);
                showToast("Error: " + err.message);
            }
        }

        // --- MARKETPLACE ---
        function listenForAvailableOrders() {
            // Audio Element
            if(!document.getElementById('order-alert')) {
                const audio = document.createElement('audio');
                audio.id = 'order-alert';
                audio.src = 'assets/alert.mp3';
                audio.preload = 'auto';
                document.body.appendChild(audio);
            }

            // Listen for orders with status 'Pending' OR 'ready_for_pickup'
            // Firestore 'in' query supports up to 10 values
            const q = query(
                collection(db, "orders"),
                where("status", "in", ["Pending", "ready_for_pickup", "preparing"]), 
                // We'll filter for delivery type in the client-side callback 
                // because we can't have multiple 'array-contains' or complex logical ORs easily in one query with 'in'
            );

            let isFirstLoad = true;

            onSnapshot(q, (snapshot) => {
                const listEl = document.getElementById('marketplace-list');
                const badge = document.getElementById('badge-avail');
                
                // Play notification sound if new order added (after initial load)
                if (!isFirstLoad) {
                    snapshot.docChanges().forEach((change) => {
                        if (change.type === "added") {
                            const data = change.doc.data();
                            // Only ring if not rejected/assigned
                            if (!data.rider_id && (!data.rejectedBy || !data.rejectedBy.includes(currentUser.uid))) {
                                const audio = document.getElementById('order-alert');
                                if(audio) {
                                    audio.currentTime = 0;
                                    audio.play().catch(e => console.log("Audio play blocked:", e));
                                }
                                showToast("🔔 New Order Available!");
                            }
                        }
                    });
                }
                isFirstLoad = false;

                let orders = [];
                snapshot.forEach(doc => {
                    const data = doc.data();
                    
                    // Client-side Filter for Delivery Type & Rejections
                    const isDelivery = (data.delivery_type === 'delivery') || (data.orderType === 'Delivery');
                    const isRejected = data.rejectedBy && data.rejectedBy.includes(currentUser.uid);
                    const isAssigned = data.rider_id;

                    if (isDelivery && !isRejected && !isAssigned) {
                        orders.push({ id: doc.id, ...data });
                    }
                });

                // Sort by creation time (newest first)
                orders.sort((a,b) => (b.createdAt?.seconds || 0) - (a.createdAt?.seconds || 0));

                badge.textContent = orders.length;
                orders.length > 0 ? badge.classList.remove('hidden') : badge.classList.add('hidden');

                if (orders.length === 0) {
                    listEl.innerHTML = `
                    <div class="col-span-full py-20 text-center text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-4 text-green-100"></i>
                        <p>No new orders ready for pickup.</p>
                    </div>`;
                    return;
                }

                listEl.innerHTML = orders.map(o => {
                    const total = o.totalAmount || o.total || 0;
                    const address = formatAddress(o.address);
                    
                    return `
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-bold text-brown text-lg font-serif">${o.customerName || 'Guest'}</h4>
                            <span class="bg-brown/5 text-brown px-2 py-1 rounded text-xs font-bold">₱${total}</span>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-map-marker-alt text-red-500 mt-1 text-xs"></i>
                                <p class="text-sm text-gray-500 line-clamp-2">${address}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gold text-xs"></i>
                                <p class="text-xs text-gray-400 uppercase tracking-wide font-bold">Ready for Pickup</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="window.ignoreOrder('${o.id}')" class="flex-1 py-2.5 rounded-lg border border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition">
                                PASS
                            </button>
                            <button onclick="window.acceptOrder('${o.id}')" class="flex-[2] py-2.5 rounded-lg bg-gold text-white font-bold text-sm shadow hover:bg-gold-dark transition">
                                ACCEPT ORDER
                            </button>
                        </div>
                    </div>`;
                }).join('');
            });
        }

        // --- GLOBAL ACTIONS ---
        window.ignoreOrder = async function(orderId) {
            if(!confirm("Pass this order? It will be hidden from your list.")) return;
            try {
                await updateDoc(doc(db, "orders", orderId), {
                    rejectedBy: arrayUnion(currentUser.uid)
                });
                showToast("Order Passed 👋");
            } catch (err) {
                console.error(err);
                showToast("Error: " + err.message);
            }
        };

        // --- ACTIVE DELIVERY ---
        function listenForActiveDelivery() {
            const q = query(
                collection(db, "orders"),
                where("rider_id", "==", currentUser.uid),
                where("status", "in", ["accepted", "on_the_way", "arrived_at_location"]) 
            );

            onSnapshot(q, (snapshot) => {
                const noOrder = document.getElementById('no-active-order');
                const content = document.getElementById('active-order-content');
                
                if (!snapshot.empty) {
                    const docSnap = snapshot.docs[0];
                    activeOrder = { id: docSnap.id, ...docSnap.data() };
                    renderActiveOrder(activeOrder);
                    noOrder.classList.add('hidden');
                    content.classList.remove('hidden');
                } else {
                    activeOrder = null;
                    noOrder.classList.remove('hidden');
                    content.classList.add('hidden');
                }
            });
        }

        function renderActiveOrder(order) {
            document.getElementById('active-customer').textContent = order.customerName || 'Guest';
            document.getElementById('active-id').textContent = `#${order.id.slice(0, 8)}`;
            document.getElementById('active-status-text').textContent = formatStatus(order.status);
            document.getElementById('active-address').textContent = formatAddress(order.address);
            document.getElementById('active-total').textContent = `₱${order.totalAmount || order.total}`;
            document.getElementById('active-method').textContent = order.paymentMethod || 'COD';
            document.getElementById('active-call').href = `tel:${order.contact || ''}`;
            
            const isPaid = order.paymentStatus === 'paid';
            const payStat = document.getElementById('active-payment-status');
            payStat.textContent = isPaid ? 'PAID' : 'COLLECT PAYMENT';
            payStat.className = isPaid ? "px-2 py-1 bg-green-100 text-green-600 text-xs font-bold rounded" : "px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded animate-pulse";

            const itemsList = document.getElementById('active-items');
            itemsList.innerHTML = (Array.isArray(order.items) ? order.items : [])
                .map(i => `<li class="flex justify-between border-b border-gray-50 pb-2 last:border-0"><span>${i.name}</span> <span class="font-bold text-brown">x${i.quantity}</span></li>`)
                .join('');

            // Inject File Input for POD
            const existingInput = document.getElementById('pod-input-container');
            if(existingInput) existingInput.remove();

            if (order.status === 'arrived_at_location') {
                 const podHtml = `
                    <div id="pod-input-container" class="mb-4 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Proof of Delivery (Photo)</label>
                        <input type="file" id="pod-file" accept="image/*" capture="camera" class="w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-xs file:font-semibold
                            file:bg-gold/10 file:text-gold
                            hover:file:bg-gold/20
                        "/>
                    </div>
                `;
                document.getElementById('workflow-action-btn').insertAdjacentHTML('beforebegin', podHtml);
            }

            const addrStr = encodeURIComponent(formatAddress(order.address));
            document.getElementById('active-map-btn').onclick = () => window.open(`https://www.google.com/maps/search/?api=1&query=${addrStr}`, '_blank');

            // Workflow Button
            const btn = document.getElementById('workflow-action-btn');
            btn.onclick = () => advanceWorkflow(order.id, order.status);
            
            switch(order.status) {
                case 'accepted':
                    btn.textContent = "CONFIRM PICKUP";
                    btn.className = "w-full py-4 bg-gold text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl hover:bg-gold-dark transition uppercase tracking-wide";
                    break;
                case 'on_the_way':
                    btn.textContent = "ARRIVED AT DROP-OFF";
                    btn.className = "w-full py-4 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-lg hover:bg-blue-700 transition uppercase tracking-wide";
                    break;
                case 'arrived_at_location':
                    btn.textContent = "COMPLETE & COLLECT";
                    btn.className = "w-full py-4 bg-green-600 text-white font-bold text-lg rounded-xl shadow-lg hover:bg-green-700 transition uppercase tracking-wide animate-pulse";
                    break;
            }
        }

        async function advanceWorkflow(orderId, currentStatus) {
            let nextStatus = '';
            let confirmMsg = '';

            if (currentStatus === 'accepted') { nextStatus = 'on_the_way'; confirmMsg = "Has the order been picked up?"; }
            else if (currentStatus === 'on_the_way') { nextStatus = 'arrived_at_location'; confirmMsg = "Have you arrived at the customer's location?"; }
            else if (currentStatus === 'arrived_at_location') { nextStatus = 'completed'; confirmMsg = "Confirm delivery complete and payment received?"; }

            // POD Validation
            // POD Validation & Upload
            let podUrl = null;
            if (currentStatus === 'arrived_at_location') {
                const fileInput = document.getElementById('pod-file');
                if (!fileInput || fileInput.files.length === 0) {
                    return showToast("⚠️ Photo Proof Required!");
                }
                if (!confirm(confirmMsg)) return;

                // Upload
                const fileRaw = fileInput.files[0];
                
                try {
                    showToast("Compressing Image...");
                    const file = await compressImage(fileRaw);
                    
                    showToast("Uploading Proof...");
                    const btn = document.getElementById('workflow-action-btn');
                    btn.disabled = true;
                    btn.textContent = "UPLOADING...";
                    
                    const storageRef = ref(storage, `proof_of_delivery/${orderId}_${Date.now()}.jpg`);

                    // Upload with explicit snapshot handling and timeout
                    // Create a timeout promise (60s)
                    const timeout = new Promise((_, reject) => 
                        setTimeout(() => reject(new Error("Upload timed out (60s). Check connection.")), 60000)
                    );

                    // Race strictly between upload and timeout
                    const snapshot = await Promise.race([
                        uploadBytes(storageRef, file),
                        timeout
                    ]);
                    
                    podUrl = await getDownloadURL(snapshot.ref);
                    
                } catch (uErr) {
                    console.error("Upload failed", uErr);
                    const btn = document.getElementById('workflow-action-btn');
                    btn.disabled = false;
                    btn.textContent = "COMPLETE & COLLECT";
                    return showToast("Upload Failed: " + uErr.message);
                }
            } else {
                 if (!confirm(confirmMsg)) return;
            }

            try {
                const updates = { status: nextStatus, updatedAt: serverTimestamp() };
                if (nextStatus === 'completed') {
                    updates.paymentStatus = 'paid';
                    updates.delivered_at = serverTimestamp();
                    if(podUrl) updates.proofOfDelivery = podUrl;
                }
                
                // Optimistic UI update or wait for server
                await updateDoc(doc(db, "orders", orderId), updates);
                showToast("Status Updated!");
                
                // Force refresh active order view if completed
                if (nextStatus === 'completed') {
                    document.getElementById('active-order-content').classList.add('hidden');
                    document.getElementById('no-active-order').classList.remove('hidden');
                    setTimeout(loadHistory, 1000); // Reload history
                }

            } catch (err) {
                console.error(err);
                const btn = document.getElementById('workflow-action-btn');
                if(btn) {
                    btn.disabled = false;
                    btn.textContent = "COMPLETE & COLLECT";
                }
                showToast("Error update: " + err.message);
            }
        }

        // --- HISTORY ---
        async function loadHistory() {
            // Retrieve history without orderBy to prevent missing index error
            const q = query(
                collection(db, "orders"),
                where("rider_id", "==", currentUser.uid),
                where("status", "==", "completed"),
                limit(50) 
            );
            
            try {
                const snap = await getDocs(q);
                const listEl = document.getElementById('history-list');
                
                if (snap.empty) {
                    listEl.innerHTML = '<div class="p-8 text-center text-gray-400">No completed deliveries found.</div>';
                    return;
                }

                // Client-side sort
                const docs = snap.docs.map(d => d.data());
                docs.sort((a, b) => {
                    const tA = a.delivered_at?.seconds || a.updatedAt?.seconds || 0;
                    const tB = b.delivered_at?.seconds || b.updatedAt?.seconds || 0;
                    return tB - tA;
                });

                listEl.innerHTML = docs.map(d => {
                    // Safe date formatting
                    let dateStr = 'N/A';
                    if (d.delivered_at?.seconds) {
                        dateStr = new Date(d.delivered_at.seconds * 1000).toLocaleDateString();
                    } else if (d.updatedAt?.seconds) {
                        dateStr = new Date(d.updatedAt.seconds * 1000).toLocaleDateString();
                    }

                    const total = d.totalAmount || d.total || 0;
                    return `
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                        <div>
                            <h4 class="font-bold text-brown text-sm">${d.customerName || 'Guest'}</h4>
                            <p class="text-xs text-gray-400">${dateStr}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-brown font-bold text-sm">₱${total}</span>
                            <span class="text-[10px] text-green-600 uppercase font-bold tracking-wide">Delivered</span>
                        </div>
                    </div>`;
                }).join('');

            } catch (err) {
                console.warn(err);
                const listEl = document.getElementById('history-list');
                if(err.code === 'failed-precondition') {
                     listEl.innerHTML = `<div class="p-4 text-center text-red-400 text-xs">Error: Missing Index. Check Console for link.</div>`;
                } else {
                     listEl.innerHTML = `<div class="p-4 text-center text-red-400 text-xs">Error loading history: ${err.message}</div>`;
                }
            }
        }

        // GLOBALS
        window.acceptOrder = async function(orderId) {
            if (!confirm("Accept this mission?")) return;
            try {
                await updateDoc(doc(db, "orders", orderId), {
                    rider_id: currentUser.uid,
                    rider_name: currentUser.displayName || currentUser.email,
                    status: 'accepted',
                    accepted_at: serverTimestamp()
                });
                showToast("Mission Accepted! 🚀");
            } catch (err) {
                console.error(err);
                showToast("Error: " + err.message);
            }
        };

        window.cancelDelivery = async function() {
            if(!activeOrder) return;
            const reason = prompt("Enter cancellation reason:");
            if(!reason) return;
            await updateDoc(doc(db, "orders", activeOrder.id), { status: 'issue_reported', issue_reason: reason });
            showToast("Issue Reported.");
        };

        function formatAddress(addr) {
            if (typeof addr === 'object') return `${addr.street || ''}, ${addr.barangay || ''}, ${addr.city || ''}`;
            return addr || 'No Address';
        }
        function formatStatus(s) { return s.replace(/_/g, ' ').toUpperCase(); }
        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.remove('translate-x-full', 'opacity-0');
            setTimeout(() => t.classList.add('translate-x-full', 'opacity-0'), 3000);
        }

        window.auth = auth;

        // Compression Helper
        function compressImage(file) {
            return new Promise((resolve, reject) => {
                const MAX_WIDTH = 1024;
                const MAX_HEIGHT = 1024;
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (e) => {
                    const img = new Image();
                    img.src = e.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height *= MAX_WIDTH / width;
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width *= MAX_HEIGHT / height;
                                height = MAX_HEIGHT;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        canvas.toBlob((blob) => {
                            if(blob) resolve(blob);
                            else reject(new Error("Compression failed"));
                        }, 'image/jpeg', 0.7); // 70% Quality
                    };
                    img.onerror = error => reject(error);
                };
                reader.onerror = error => reject(error);
            });
        }

        window.logoutRider = async () => {
            if(confirm('Are you sure you want to log out?')) {
                try {
                    await auth.signOut();
                    await fetch('../assets/php/auth/logout.php');
                    window.location.href = '../login.php';
                } catch(e) {
                    console.error('Logout error', e);
                    window.location.href = '../login.php';
                }
            }
        };
    </script>
</body>
</html>
