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

<!-- Firebase Logic -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
    import { getAuth, signInWithEmailAndPassword, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
    import { getFirestore, doc, getDoc } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
    import { firebaseConfig } from "./assets/js/firebase-config.js";

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const db = getFirestore(app);

    document.getElementById('loginBtn').addEventListener('click', async () => {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const btn = document.getElementById('loginBtn');
        const errorMsg = document.getElementById('errorMsg');

        if (!email || !password) {
            errorMsg.textContent = "Please enter both email and password.";
            errorMsg.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.textContent = "Authenticating...";
        errorMsg.style.display = 'none';

        try {
            // 1. Auth with Firebase
            const userCredential = await signInWithEmailAndPassword(auth, email, password);
            const user = userCredential.user;

            // 2. Check Role in Firestore
            const userDoc = await getDoc(doc(db, "users", user.uid));
            
            if (userDoc.exists()) {
                const userData = userDoc.data();
                const role = userData.role || 'customer'; // Default to customer

                console.log("User Role:", role);

                // 3. Redirect based on Role
                if (role === 'admin' || role === 'super_admin' || role === 'cashier' || role === 'inventory' || role === 'rider') {
                    await fetch('assets/php/auth/bypass_2fa.php', { 
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json' }, 
                        body: JSON.stringify({ role: role }) 
                    }).catch(e => console.log(e));

                    if (role === 'admin' || role === 'super_admin') window.location.href = 'admin/dashboard.php';
                    else if (role === 'cashier') window.location.href = 'admin/cashier_dashboard.php';
                    else if (role === 'inventory') window.location.href = 'admin/inventory_dashboard.php';
                    else if (role === 'rider') window.location.href = 'rider/portal.php';
                } else {
                    window.location.href = 'index.php';
                }
            } else {
                 // Fallback if no user doc (assume customer)
                 window.location.href = 'index.php';
            }

        } catch (error) {
            console.error(error);

            // AUTO-FIX: If test rider account is missing, create it on the fly!
            if (email === 'rider@test.com' && (error.code === 'auth/user-not-found' || error.code === 'auth/invalid-credential')) {
                try {
                    console.log("⚠️ Test account missing. Creating it now...");
                    btn.textContent = "Creating Test Account...";
                    
                    const { createUserWithEmailAndPassword } = await import("https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js");
                    const { setDoc, doc } = await import("https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js");

                    const newUserCred = await createUserWithEmailAndPassword(auth, email, password);
                    await setDoc(doc(db, "users", newUserCred.user.uid), {
                        email: email,
                        role: 'rider',
                        fullName: 'Test Rider',
                        createdAt: new Date()
                    });

                    alert("✅ Test Account Created! Redirecting...");
                    window.location.href = 'rider/portal.php';
                    return;

                } catch (createErr) {
                    console.error("Auto-create failed:", createErr);
                    errorMsg.textContent = "Failed to auto-create test account.";
                }
            }

            errorMsg.textContent = "Invalid email or password.";
            errorMsg.style.display = 'block';
            btn.disabled = false;
            btn.textContent = "Login";
        }
    });

    // --- Toggle between Login and Forgot Password ---
    document.getElementById('showForgotBtn').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('loginCard').style.display = 'none';
        document.getElementById('forgotCard').style.display = 'block';
    });

    document.getElementById('backToLogin').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('forgotCard').style.display = 'none';
        document.getElementById('loginCard').style.display = 'block';
    });

    // --- Forgot Password Handler ---
    document.getElementById('forgotBtn').addEventListener('click', async () => {
        const email = document.getElementById('forgotEmail').value.trim();
        const btn = document.getElementById('forgotBtn');
        const msgDiv = document.getElementById('forgotMsg');

        msgDiv.style.display = 'none';

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            showMsg('Please enter your email address.', 'error');
            return;
        }
        if (!emailRegex.test(email)) {
            showMsg('Please enter a valid email address.', 'error');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Sending...';

        try {
            await sendPasswordResetEmail(auth, email);
            showMsg('✅ Reset link sent! Check your inbox (and spam folder).', 'success');
            document.getElementById('forgotEmail').value = '';
        } catch (error) {
            console.error('Forgot Password Error:', error);
            let message = 'Something went wrong. Please try again.';
            switch (error.code) {
                case 'auth/user-not-found':
                    message = 'No account found with this email.';
                    break;
                case 'auth/invalid-email':
                    message = 'Invalid email format.';
                    break;
                case 'auth/too-many-requests':
                    message = 'Too many attempts. Wait a few minutes.';
                    break;
                case 'auth/network-request-failed':
                    message = 'Network error. Check your connection.';
                    break;
            }
            showMsg(message, 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Send Reset Link';
        }
    });

    function showMsg(text, type) {
        const msgDiv = document.getElementById('forgotMsg');
        msgDiv.textContent = text;
        msgDiv.style.display = 'block';
        if (type === 'success') {
            msgDiv.style.background = 'rgba(33, 150, 83, 0.15)';
            msgDiv.style.color = '#4CAF50';
            msgDiv.style.border = '1px solid rgba(76, 175, 80, 0.3)';
        } else {
            msgDiv.style.background = 'rgba(211, 47, 47, 0.15)';
            msgDiv.style.color = '#ff6b6b';
            msgDiv.style.border = '1px solid rgba(255, 107, 107, 0.3)';
        }
    }
</script>

</body>
</html>
