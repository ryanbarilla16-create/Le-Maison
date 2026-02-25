// Admin Authentication Controller
import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import {
    getAuth,
    signInWithEmailAndPassword,
    signOut,
    onAuthStateChanged
} from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
import {
    getFirestore,
    doc,
    getDoc
} from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
import { firebaseConfig } from "./firebase-config.js";

// Initialize Firebase (avoid duplicate init)
const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

// 1. Handle Login Form (for login.php)
const loginForm = document.getElementById('adminLoginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const btn = document.getElementById('loginBtn');
        const errorMsg = document.getElementById('errorMessage');

        // Loading state
        btn.textContent = "Verifying...";
        btn.classList.add('loading');
        errorMsg.style.display = 'none';

        try {
            // Sign in with Firebase Auth
            const userCredential = await signInWithEmailAndPassword(auth, email, password);
            const user = userCredential.user;

            console.log("User Authenticated, Checking Role for:", user.email);

            // Fetch User Role from Firestore
            const docRef = doc(db, "users", user.uid);
            const docSnap = await getDoc(docRef);

            if (docSnap.exists()) {
                const userData = docSnap.data();
                const adminRoles = ['admin', 'super_admin', 'inventory', 'cashier'];

                if (adminRoles.includes(userData.role)) {
                    console.log("Staff Logged In:", user.email, "Role:", userData.role);

                    const pin = userData.pin; // May be undefined for old accounts

                    if (pin) {
                        // Send expected PIN and Role to PHP for backend validation
                        const response = await fetch('../assets/php/auth/init_2fa.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ pin: pin, role: userData.role })
                        });

                        const result = await response.json();
                        if (result.success) {
                            window.location.href = '../verification.php';
                        } else {
                            throw new Error("Error initializing 2-Step Verification.");
                        }
                    } else {
                        // OLD ADMIN ACCOUNT - NO PIN YET - BYPASS 2FA
                        const bypassRes = await fetch('../assets/php/auth/bypass_2fa.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ role: userData.role })
                        });
                        const bypassResult = await bypassRes.json();

                        if (bypassResult.success) {
                            if (userData.role === 'admin' || userData.role === 'super_admin') {
                                window.location.href = 'dashboard.php';
                            } else if (userData.role === 'cashier') {
                                window.location.href = 'cashier_dashboard.php';
                            } else if (userData.role === 'inventory') {
                                window.location.href = 'inventory_dashboard.php';
                            }
                        } else {
                            throw new Error("Failed to initialize session.");
                        }
                    }
                } else {
                    // Not an admin
                    throw new Error("Access Denied: You are not authorized as an administrator.");
                }
            } else {
                // No user record found
                throw new Error("Access Denied: User record not found.");
            }

        } catch (error) {
            console.error(error);

            // If we signed in but failed role check, sign out immediately
            if (auth.currentUser) {
                await signOut(auth);
            }

            // Display error
            let message = error.message.replace('Firebase: ', '');
            if (error.code === 'auth/invalid-credential' || error.code === 'auth/user-not-found') {
                message = "Invalid email or password.";
            }

            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
            btn.textContent = "Access Portal";
            btn.classList.remove('loading');
        }
    });
}

// 2. Auth Guard (for dashboard.php)
// If we are NOT on the login page, we need to enforce auth
if (!location.pathname.includes('login.php')) {
    onAuthStateChanged(auth, async (user) => {
        if (user) {
            try {
                // Check Role in Firestore
                const docRef = doc(db, "users", user.uid);
                const docSnap = await getDoc(docRef);
                const adminRoles = ['admin', 'super_admin', 'inventory', 'cashier'];

                if (docSnap.exists() && adminRoles.includes(docSnap.data().role)) {
                    const role = docSnap.data().role;
                    const path = window.location.pathname;

                    // Enforce correct dashboard for specific staff roles
                    if (role === 'cashier' && !path.includes('cashier_dashboard.php')) {
                        window.location.href = 'cashier_dashboard.php';
                        return;
                    }
                    if (role === 'inventory' && !path.includes('inventory_dashboard.php')) {
                        window.location.href = 'inventory_dashboard.php';
                        return;
                    }

                    // User is logged in and is staff
                    // Safe to show dashboard content
                    window.userRole = role;
                    document.body.style.display = 'block'; // Unhide body logic
                    console.log("Welcome Staff (" + role + "): " + user.email);

                    // Dispatch a custom event so other scripts know auth is ready
                    window.dispatchEvent(new Event('authReady'));
                } else {
                    // User logged in but NOT admin
                    console.warn("Unauthorized access attempt by:", user.email);
                    alert("Unauthorized access. Admin role required.");
                    await signOut(auth);
                    await fetch('../assets/php/auth/logout.php').catch(e => console.log(e));
                    window.location.href = 'login.php';
                }
            } catch (error) {
                console.error("Error verifying admin privs:", error);

                // Avoid infinite loops if something is really broken, but sign out to be safe
                if (auth.currentUser) await signOut(auth);

                alert("Error verifying permissions. Please login again.");
                window.location.href = 'login.php';
            }
        } else {
            // No user logged in
            window.location.href = 'login.php';
        }
    });
}

// 3. Handle Logout
const logoutBtn = document.getElementById('adminLogout');
if (logoutBtn) {
    logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        signOut(auth).then(async () => {
            await fetch('../assets/php/auth/logout.php').catch(e => console.log(e));
            window.location.href = '../index.php';
        });
    });
}
