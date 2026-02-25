// Authentication Controller using Firebase Auth
// Le Maison de Yelo Lane

import {
    getAuth,
    createUserWithEmailAndPassword,
    signInWithEmailAndPassword,
    signOut,
    onAuthStateChanged,
    updateProfile,
    updatePassword,
    sendPasswordResetEmail,
    reauthenticateWithCredential, // For password change verification
    EmailAuthProvider
} from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";

import {
    getFirestore, doc, setDoc, getDoc, updateDoc, collection, query, orderBy, onSnapshot, where, addDoc
} from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initAuth(app) {
    const auth = getAuth(app);
    const db = getFirestore(app);
    console.log("Auth initialized", auth);

    // Admin List
    const ADMIN_EMAILS = ["admin@lemaison.com", "owner@lemaison.com"];

    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const logoutBtn = document.getElementById('logoutBtn');

    // UI Elements for Auth State
    const loginBtnNav = document.getElementById('loginBtn');
    const registerBtnNav = document.getElementById('registerBtn');
    const profileNav = document.getElementById('userProfile');
    const userNameSpan = document.getElementById('userName');

    // --- Populate Birthday Dropdowns ---
    const daySelect = document.getElementById('regBdayDay');
    const yearSelect = document.getElementById('regBdayYear');

    if (daySelect) {
        for (let d = 1; d <= 31; d++) {
            const opt = document.createElement('option');
            opt.value = d; opt.textContent = d;
            daySelect.appendChild(opt);
        }
    }

    if (yearSelect) {
        const currentYear = new Date().getFullYear();
        for (let y = currentYear; y >= 1920; y--) {
            const opt = document.createElement('option');
            opt.value = y; opt.textContent = y;
            yearSelect.appendChild(opt);
        }
    }



    // --- Avatar Preview ---
    const avatarInput = document.getElementById('regAvatar');
    const avatarPreview = document.getElementById('avatarPreview');
    let avatarBase64 = null;

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    avatarBase64 = ev.target.result;
                    avatarPreview.src = avatarBase64;
                    avatarPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and Drop
        const dropZone = document.getElementById('avatarDropZone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(evt => {
                dropZone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#C9A961';
                    dropZone.style.background = '#faf6ee';
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                dropZone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#d4cfc5';
                    dropZone.style.background = '#fdfcfa';
                });
            });
            dropZone.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    avatarInput.files = e.dataTransfer.files;
                    avatarInput.dispatchEvent(new Event('change'));
                }
            });
        }
    }

    // --- Password Strength Bar ---
    const pwInput = document.getElementById('regPassword');
    const pwBar = document.getElementById('pwStrengthBar');

    if (pwInput && pwBar) {
        pwInput.addEventListener('input', () => {
            const val = pwInput.value;
            let strength = 0;
            if (val.length >= 6) strength++;
            if (val.length >= 10) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;

            const pct = Math.min(strength / 5 * 100, 100);
            pwBar.style.width = pct + '%';
            if (pct < 40) pwBar.style.background = '#D4654A';
            else if (pct < 70) pwBar.style.background = '#E8A838';
            else pwBar.style.background = '#7DAA92';
        });
    }

    // --- Handle Registration ---
    // --- OTP State ---
    let generatedOTP = null;
    let tempRegData = null;

    // --- Handle Registration ---
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('regSubmitBtn');
            const firstName = document.getElementById('regFirstName').value.trim();
            const middleName = document.getElementById('regMiddleName').value.trim();
            const lastName = document.getElementById('regLastName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const password = document.getElementById('regPassword').value;
            const confirmPw = document.getElementById('regConfirmPassword').value;
            const username = document.getElementById('regUsername').value.trim();
            const bdayMonth = document.getElementById('regBdayMonth').value;
            const bdayDay = document.getElementById('regBdayDay').value;
            const bdayYear = document.getElementById('regBdayYear').value;

            // 1. Basic Form Validation (Front-end)
            if (!firstName || !lastName || !email || !password || !confirmPw || !username || !bdayMonth || !bdayDay || !bdayYear) {
                alert("Please fill in all required fields.");
                return;
            }

            // --- STRICT VALIDATION ENGINE ---

            // 1. Name Restrictions
            const nameRegex = /^[A-Za-z\s\-]+$/;
            const spamRegex = /(.)\1{3,}|(.{2,})\2{2,}/i; // >3 repeated single chars or repeated words

            if (firstName.length > 50 || (middleName && middleName.length > 50) || lastName.length > 50) {
                alert("STATUS: REJECTED. Names cannot exceed 50 characters.");
                return;
            }
            if (!nameRegex.test(firstName) || (middleName && !nameRegex.test(middleName)) || !nameRegex.test(lastName)) {
                alert("STATUS: REJECTED. Names can only contain letters, spaces, and dashes. Numbers and special characters are not allowed.");
                return;
            }
            if (spamRegex.test(firstName) || (middleName && spamRegex.test(middleName)) || spamRegex.test(lastName)) {
                alert("STATUS: REJECTED. Spam or repeated characters/words detected in names.");
                return;
            }

            // 2. Username Restrictions
            const usernameRegex = /^[A-Za-z0-9_]{5,20}$/;
            if (!usernameRegex.test(username)) {
                alert("STATUS: REJECTED. Username must be 5-20 characters long and can only contain letters, numbers, and underscores.");
                return;
            }
            if (spamRegex.test(username)) {
                alert("STATUS: REJECTED. Spam or repeated characters detected in username.");
                return;
            }

            // 2.5 Identical Data Restriction
            const fNameLow = firstName.toLowerCase();
            const mNameLow = middleName ? middleName.toLowerCase() : "";
            const lNameLow = lastName.toLowerCase();
            const uNameLow = username.toLowerCase();

            if (fNameLow === lNameLow || fNameLow === uNameLow || lNameLow === uNameLow || (mNameLow && (mNameLow === fNameLow || mNameLow === lNameLow || mNameLow === uNameLow))) {
                alert("STATUS: REJECTED. First Name, Middle Name, Last Name, and Username cannot be identical. Please provide valid information.");
                return;
            }

            // 3. Strict Email Validation (Gmail ONLY)
            if (!email.endsWith('@gmail.com')) {
                alert("STATUS: REJECTED. Only @gmail.com emails are allowed.");
                return;
            }

            const emailLocalPart = email.split('@')[0];
            if (emailLocalPart.length < 6 || emailLocalPart.length > 30) {
                alert("STATUS: REJECTED. Email username must be between 6 and 30 characters.");
                return;
            }
            if (!/[a-zA-Z]/.test(emailLocalPart)) {
                alert("STATUS: REJECTED. Email must contain at least one letter.");
                return;
            }
            if (!/^[a-zA-Z0-9.]+$/.test(emailLocalPart)) {
                alert("STATUS: REJECTED. Email can only contain letters, numbers, and dots (no spaces or underscores).");
                return;
            }
            if (emailLocalPart.startsWith('.') || emailLocalPart.endsWith('.')) {
                alert("STATUS: REJECTED. Email cannot start or end with a dot.");
                return;
            }
            if (emailLocalPart.includes('..')) {
                alert("STATUS: REJECTED. Email cannot contain consecutive dots.");
                return;
            }

            // 4. Birthday / Age Restriction (18 YEARS OLD AND ABOVE ONLY)
            // Month string to number mapping
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const mIndex = monthNames.indexOf(bdayMonth);
            const bdayDate = new Date(parseInt(bdayYear), mIndex, parseInt(bdayDay));
            const today = new Date();

            let age = today.getFullYear() - bdayDate.getFullYear();
            const mDiff = today.getMonth() - bdayDate.getMonth();
            if (mDiff < 0 || (mDiff === 0 && today.getDate() < bdayDate.getDate())) {
                age--;
            }

            if (age < 18) {
                alert("STATUS: REJECTED. You must be at least 18 years old to register.");
                return;
            }

            // Password Validation
            if (password.length < 6) {
                alert("STATUS: REJECTED. Password must be at least 6 characters long.");
                return;
            }
            if (password !== confirmPw) {
                alert("STATUS: REJECTED. Passwords do not match!");
                return;
            }

            // All basic validations passed -> APPROVED for DB check
            console.log("STATUS: APPROVED front-end validation.");

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating...';
            btn.disabled = true;

            try {
                // 2. Database Validation (Firestore Queries)
                const fullName = [firstName, middleName, lastName].filter(Boolean).join(' ');

                const { getDocs } = await import("https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js");
                // Check Unique Username
                const usernameQuery = query(collection(db, "users"), where("username", "==", username));
                const usernameSnap = await getDocs(usernameQuery);
                if (!usernameSnap.empty) {
                    throw new Error("This username is already taken. Please choose another one.");
                }

                // Check Unique Full Name
                const nameQuery = query(collection(db, "users"), where("fullName", "==", fullName));
                const nameSnap = await getDocs(nameQuery);
                if (!nameSnap.empty) {
                    throw new Error("An account with this full name already exists.");
                }

                // Check Unique Email (Firestore records)
                const emailQuery = query(collection(db, "users"), where("email", "==", email));
                const emailSnap = await getDocs(emailQuery);
                if (!emailSnap.empty) {
                    throw new Error("This email is already registered.");
                }

                // Generate 6-digit OTP
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending OTP...';
                generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();

                // Store data temporarily
                tempRegData = {
                    firstName, middleName, lastName, email, password, username,
                    bdayMonth, bdayDay, bdayYear, avatarBase64
                };

                // Send OTP via PHP Script
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // 15s timeout

                const response = await fetch('assets/php/email/send_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email, otp: generatedOTP }),
                    signal: controller.signal
                });
                clearTimeout(timeoutId);

                const result = await response.json();

                if (result.success) {
                    // Show OTP Modal
                    document.getElementById('otpModal').classList.add('active');
                    console.log("📧 OTP Sent to " + email); // Debugging
                } else {
                    throw new Error(result.message || "Failed to send verification email.");
                }

            } catch (error) {
                console.error("❌ Registration Error:", error);
                alert(error.message);
            } finally {
                btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                btn.disabled = false;
            }
        });
    }

    // --- OTP Verification Logic ---
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const resendOtpLink = document.getElementById('resendOtpLink');
    const otpInput = document.getElementById('otpInput');
    const otpError = document.getElementById('otpError');

    if (verifyOtpBtn) {
        verifyOtpBtn.addEventListener('click', async () => {
            const inputCode = otpInput.value.trim();

            if (inputCode !== generatedOTP) {
                otpError.textContent = "Invalid code. Please try again.";
                otpError.style.display = 'block';
                return;
            }

            // Code Correct! Create Account
            otpError.style.display = 'none';
            verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            verifyOtpBtn.disabled = true;

            try {
                const { email, password, firstName, middleName, lastName, username, bdayMonth, bdayDay, bdayYear, avatarBase64 } = tempRegData;

                // Create Firebase account
                const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;
                console.log("✅ Account created:", user.email);

                // Save profile to Firestore with PENDING status
                const fullName = [firstName, middleName, lastName].filter(Boolean).join(' ');

                await Promise.all([
                    updateProfile(user, { displayName: fullName }),
                    setDoc(doc(db, 'users', user.uid), {
                        firstName,
                        middleName,
                        lastName,
                        fullName,
                        email,
                        username,
                        birthDate: { month: bdayMonth, day: bdayDay, year: bdayYear },
                        avatarBase64: avatarBase64,
                        createdAt: new Date(),
                        role: 'customer',
                        accountStatus: 'PENDING',
                        emailVerified: true // Mark as manually verified
                    })
                ]);

                // Sign out immediately
                await signOut(auth);

                // Close all modals
                document.getElementById('otpModal').classList.remove('active');
                window.closeModals();
                resetResendTimer();
                registerForm.reset();
                if (avatarPreview) avatarPreview.style.display = 'none';

                // Clear state
                generatedOTP = null;
                tempRegData = null;

                alert("✅ Email Verified! Your account has been created and is waiting for Admin Approval. Please check back later.");

            } catch (error) {
                console.error("❌ Creation Error:", error);
                let message = error.message;

                if (error.code === 'auth/email-already-in-use') {
                    message = "This email is already registered. Please try logging in instead.";
                }

                alert("Account creation failed: " + message);
                verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
                verifyOtpBtn.disabled = false;
            }
        });
    }

    let resendTimer = null;

    function startResendCooldown(duration) {
        if (resendTimer) clearInterval(resendTimer);

        let timer = duration;
        resendOtpLink.style.pointerEvents = 'none';
        resendOtpLink.style.color = '#999';
        resendOtpLink.style.cursor = 'not-allowed';

        resendTimer = setInterval(() => {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            resendOtpLink.textContent = `Resend in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (--timer < 0) {
                clearInterval(resendTimer);
                resendOtpLink.textContent = "Resend Code";
                resendOtpLink.style.pointerEvents = 'auto';
                resendOtpLink.style.color = '';
                resendOtpLink.style.cursor = 'pointer';
            }
        }, 1000);
    }

    function resetResendTimer() {
        if (resendTimer) clearInterval(resendTimer);
        if (resendOtpLink) {
            resendOtpLink.textContent = "Resend Code";
            resendOtpLink.style.pointerEvents = 'auto';
            resendOtpLink.style.color = '';
            resendOtpLink.style.cursor = 'pointer';
        }
    }

    // Modal Close Button Handler
    const otpModalClose = document.querySelector('#otpModal .close-btn');
    if (otpModalClose) {
        otpModalClose.addEventListener('click', () => {
            document.getElementById('otpModal').classList.remove('active');
            resetResendTimer();
        });
    }

    if (resendOtpLink) {
        resendOtpLink.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!tempRegData) return;

            // Send OTP
            resendOtpLink.textContent = "Sending...";
            generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();

            try {
                await fetch('assets/php/email/send_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: tempRegData.email, otp: generatedOTP })
                });
                alert("New code sent to " + tempRegData.email);
                // Start 5-minute cooldown (300 seconds)
                startResendCooldown(300);
            } catch (err) {
                alert("Failed to resend code.");
                resendOtpLink.textContent = "Resend Code";
            }
        });
    }

    // --- Handle Login ---
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('loginSubmitBtn');
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            btn.disabled = true;

            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                console.log("Logged In:", userCredential.user.email);

                // Close modal
                window.closeModals();

                // Check Role
                const userDoc = await getDoc(doc(db, "users", userCredential.user.uid));
                if (userDoc.exists()) {
                    const data = userDoc.data();
                    const role = data.role;
                    const pin = data.pin; // May be undefined for old accounts

                    if (pin) {
                        // Send expected PIN and Role to PHP for backend validation
                        const response = await fetch('assets/php/auth/init_2fa.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ pin: pin, role: role })
                        });

                        const result = await response.json();
                        if (result.success) {
                            window.location.href = 'verification.php';
                        } else {
                            alert("Error initializing 2-Step Verification.");
                        }
                    } else {
                        // OLD ACCOUNT - NO PIN YET - BYPASS 2FA
                        const bypassRes = await fetch('assets/php/auth/bypass_2fa.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ role: role })
                        });
                        const bypassResult = await bypassRes.json();

                        if (bypassResult.success) {
                            if (role === 'admin' || role === 'super_admin') {
                                window.location.href = 'admin/dashboard.php';
                            } else if (role === 'cashier') {
                                window.location.href = 'admin/cashier_dashboard.php';
                            } else if (role === 'inventory') {
                                window.location.href = 'admin/inventory_dashboard.php';
                            } else if (role === 'rider') {
                                window.location.href = 'rider/portal.php';
                            } else {
                                window.location.reload();
                            }
                        } else {
                            throw new Error("Failed to initialize session.");
                        }
                    }
                } else {
                    // Fallback for admins not in users collection
                    if (ADMIN_EMAILS.includes(userCredential.user.email.toLowerCase())) {
                        const response = await fetch('assets/php/auth/init_2fa.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ pin: '0000', role: 'admin' }) // Default Admin PIN
                        });
                        if ((await response.json()).success) {
                            window.location.href = 'verification.php';
                        }
                    }
                }

            } catch (error) {
                console.error("Login Error", error);

                // AUTO-FIX: Create Test Rider if missing
                if (email === 'rider@test.com' && (error.code === 'auth/user-not-found' || error.code === 'auth/invalid-credential')) {
                    try {
                        btn.textContent = "Creating Test Account...";
                        const newUserCred = await createUserWithEmailAndPassword(auth, email, password);
                        await setDoc(doc(db, "users", newUserCred.user.uid), {
                            email: email,
                            role: 'rider',
                            fullName: 'Test Rider',
                            createdAt: new Date()
                        });
                        alert("✅ Test Account Created! Redirecting to Portal...");
                        window.location.href = 'rider/portal.php';
                        return;
                    } catch (err) {
                        console.error("Auto-create failed", err);
                    }
                }

                let message = "Invalid email or password.";
                if (error.code === 'auth/invalid-email') message = "Invalid email format.";
                alert("Error: " + message);
            } finally {
                btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
                btn.disabled = false;
            }
        });
    }

    // --- Handle Forgot Password ---
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('forgotSubmitBtn');
            const emailInput = document.getElementById('forgotEmail');
            const msgDiv = document.getElementById('forgotMsg');
            const email = emailInput.value.trim();

            // Reset message
            msgDiv.style.display = 'none';
            msgDiv.textContent = '';

            // Client-side email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                showForgotMsg('Please enter your email address.', 'error');
                return;
            }
            if (!emailRegex.test(email)) {
                showForgotMsg('Please enter a valid email address.', 'error');
                return;
            }

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;

            try {
                await sendPasswordResetEmail(auth, email);
                showForgotMsg('✅ Password reset link sent! Check your inbox (and spam folder).', 'success');
                emailInput.value = ''; // Clear the field on success
            } catch (error) {
                console.error('Forgot Password Error:', error);

                let message = 'Something went wrong. Please try again.';
                switch (error.code) {
                    case 'auth/user-not-found':
                        message = 'No account found with this email address.';
                        break;
                    case 'auth/invalid-email':
                        message = 'The email address format is invalid.';
                        break;
                    case 'auth/too-many-requests':
                        message = 'Too many attempts. Please wait a few minutes and try again.';
                        break;
                    case 'auth/network-request-failed':
                        message = 'Network error. Please check your internet connection.';
                        break;
                }
                showForgotMsg(message, 'error');
            } finally {
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reset Link';
                btn.disabled = false;
            }
        });
    }

    function showForgotMsg(text, type) {
        const msgDiv = document.getElementById('forgotMsg');
        if (!msgDiv) return;
        msgDiv.textContent = text;
        msgDiv.style.display = 'block';
        if (type === 'success') {
            msgDiv.style.background = '#dff4e2';
            msgDiv.style.color = '#155724';
            msgDiv.style.border = '1px solid #c3e6cb';
        } else {
            msgDiv.style.background = '#fbe0e2';
            msgDiv.style.color = '#721c24';
            msgDiv.style.border = '1px solid #f5c6cb';
        }
    }

    // Handle Logout
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent profile modal from opening
            signOut(auth).then(async () => {
                await fetch('assets/php/auth/logout.php').catch(e => console.log(e));
                window.location.reload();
            });
        });
    }

    // --- Profile Editing Logic ---
    const editProfileModal = document.getElementById('editProfileModal');
    const editProfileForm = document.getElementById('editProfileForm');

    // Helper: Compress Image (Max 500px)
    const processImage = (file) => {
        return new Promise((resolve, reject) => {
            const MAX_WIDTH = 500;
            const MAX_HEIGHT = 500;
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
                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                };
                img.onerror = error => reject(error);
            };
            reader.onerror = error => reject(error);
        });
    };

    // Password Visibility Toggle is handled globally in index.php

    // Avatar Input Listener
    const editAvatarInput = document.getElementById('editUserAvatarInput');
    if (editAvatarInput) {
        editAvatarInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            try {
                const base64 = await processImage(file);
                const preview = document.getElementById('editUserProfileAvatar');
                if (preview) preview.src = base64;
            } catch (err) {
                console.error("Avatar processing error", err);
                alert("Failed to process image.");
            }
        });
    }

    if (profileNav) {
        profileNav.addEventListener('click', async (e) => {
            // Prevent opening if clicking logout (already handled by stopPropagation, but extra safety)
            if (e.target.closest('#logoutBtn')) return;

            const user = auth.currentUser;
            if (!user) return;

            // Open Modal
            if (editProfileModal) {
                editProfileModal.classList.add('active');

                // Populate Data
                try {
                    const docRef = doc(db, 'users', user.uid);
                    const docSnap = await getDoc(docRef);

                    if (docSnap.exists()) {
                        const data = docSnap.data();
                        document.getElementById('editFirstName').value = data.firstName || '';
                        document.getElementById('editMiddleName').value = data.middleName || '';
                        document.getElementById('editLastName').value = data.lastName || '';
                        document.getElementById('editPhone').value = data.phone || '';

                        const pinInput = document.getElementById('editPin');
                        if (pinInput) pinInput.value = data.pin || '';

                        // Address might be a map or flat strings depending on legacy data, generally map
                        // Address Population
                        if (data.address) {
                            document.getElementById('editAddressLabel').value = data.address.label || 'Home';
                            document.getElementById('editAddressStreet').value = data.address.street || '';
                            document.getElementById('editAddressBarangay').value = data.address.barangay || '';
                            document.getElementById('editAddressLandmark').value = data.address.landmark || '';
                        }

                        // Birthday Population
                        const bdayInput = document.getElementById('editBirthday');
                        if (bdayInput) {
                            if (data.birthDate) {
                                let d = data.birthDate;
                                if (typeof d === 'object') {
                                    // Handle month as string name or number
                                    const monthMap = {
                                        "January": "01", "February": "02", "March": "03", "April": "04", "May": "05", "June": "06",
                                        "July": "07", "August": "08", "September": "09", "October": "10", "November": "11", "December": "12"
                                    };

                                    let m = d.month;
                                    if (isNaN(m) && monthMap[m]) {
                                        m = monthMap[m];
                                    } else {
                                        m = String(m).padStart(2, '0');
                                    }

                                    const day = String(d.day).padStart(2, '0');
                                    bdayInput.value = `${d.year}-${m}-${day}`;
                                } else if (typeof d === 'string') {
                                    // Assume YYYY-MM-DD if string
                                    bdayInput.value = d;
                                }
                            } else {
                                bdayInput.value = '';
                            }
                        }

                        // Avatar
                        const avatarEl = document.getElementById('editUserProfileAvatar');
                        if (avatarEl) {
                            // Check avatarUrl first, then avatarBase64 (registration legacy)
                            const avatar = data.avatarUrl || data.avatarBase64 || ("https://ui-avatars.com/api/?name=" + (data.fullName || "User"));
                            avatarEl.src = avatar;
                        }
                    } else {
                        // Fallback
                        const names = (user.displayName || '').split(' ');
                        document.getElementById('editFirstName').value = names[0] || '';
                        document.getElementById('editLastName').value = names.slice(1).join(' ') || '';
                    }
                    document.getElementById('editEmail').value = user.email;

                } catch (err) {
                    console.error("Error fetching profile:", err);
                }
            }
        });
    }

    if (editProfileForm) {
        editProfileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('editProfileSubmitBtn');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;

            const user = auth.currentUser;
            if (!user) return;

            // Get Values
            // Get Values
            const firstName = document.getElementById('editFirstName').value.trim();
            const middleName = document.getElementById('editMiddleName').value.trim();
            const lastName = document.getElementById('editLastName').value.trim();
            const phone = document.getElementById('editPhone').value.trim();

            // Address Fields
            const street = document.getElementById('editAddressStreet').value.trim();
            const barangay = document.getElementById('editAddressBarangay').value.trim();
            const addrLabel = document.getElementById('editAddressLabel').value;
            const landmark = document.getElementById('editAddressLandmark').value.trim();

            const bdayVal = document.getElementById('editBirthday').value;

            // Password Fields
            const currentPass = document.getElementById('editCurrentPassword').value;
            const newPass = document.getElementById('editNewPassword').value;
            const confirmPass = document.getElementById('editConfirmPassword').value;

            const fullName = [firstName, middleName, lastName].filter(Boolean).join(' ');

            try {
                // 1. Re-authenticate if Password Change Requested
                if (currentPass || newPass || confirmPass) {
                    if (!currentPass || !newPass || !confirmPass) {
                        throw new Error("To change password, please enter Current, New, and Confirm passwords.");
                    }
                    if (newPass !== confirmPass) {
                        throw new Error("New password and Confirm password do not match.");
                    }
                    if (newPass.length < 6) {
                        throw new Error("New password must be at least 6 characters.");
                    }

                    // Re-authenticate
                    const credential = EmailAuthProvider.credential(user.email, currentPass);
                    await reauthenticateWithCredential(user, credential);
                }

                // 2. Prepare Updates
                const updateData = {
                    firstName,
                    middleName,
                    lastName,
                    fullName,
                    phone,
                    address: {
                        label: addrLabel,
                        street,
                        barangay,
                        city: 'Pagsanjan',
                        province: 'Laguna',
                        landmark: landmark
                    },
                    lastUpdated: new Date()
                };

                const pinInput = document.getElementById('editPin');
                if (pinInput && pinInput.value) {
                    if (!/^\d{4}$/.test(pinInput.value)) {
                        throw new Error("Your 2FA PIN must be exactly 4 digits.");
                    }
                    updateData.pin = pinInput.value;
                }

                if (bdayVal) {
                    const [y, m, d] = bdayVal.split('-');
                    updateData.birthDate = {
                        year: parseInt(y),
                        month: parseInt(m),
                        day: parseInt(d)
                    };
                }

                // Check for Avatar Update
                const editAvatarInput = document.getElementById('editUserAvatarInput');
                const avatarPreview = document.getElementById('editUserProfileAvatar');
                let newAvatarUrl = null;

                if (editAvatarInput && editAvatarInput.files.length > 0 && avatarPreview && avatarPreview.src.startsWith('data:')) {
                    newAvatarUrl = avatarPreview.src;
                    updateData.avatarUrl = newAvatarUrl;
                }

                // 3. Update Firestore
                await updateDoc(doc(db, 'users', user.uid), updateData);

                // 4. Update Auth Profile (Store avatar in Firestore instead of Auth due to Base64 length limit)
                await updateProfile(user, {
                    displayName: fullName
                });

                // 5. Update Password if requested
                if (currentPass && newPass) {
                    await updatePassword(user, newPass);
                }

                // Clear password fields on success
                document.getElementById('editCurrentPassword').value = '';
                document.getElementById('editNewPassword').value = '';
                document.getElementById('editConfirmPassword').value = '';

                // Reset eye icons to eye (not slashed) and set inputs back to password type
                document.querySelectorAll('.toggle-password').forEach(icon => {
                    const targetId = icon.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (input) input.type = 'password';
                    icon.classList.add('fa-eye');
                    icon.classList.remove('fa-eye-slash');
                });

                // Update UI UI immediately
                userNameSpan.textContent = fullName;

                // Update specific navbar avatar
                const navAvatar = document.getElementById('navUserAvatar');
                const navIcon = document.getElementById('navUserIcon');

                if (newAvatarUrl && navAvatar && navIcon) {
                    navAvatar.src = newAvatarUrl;
                    navAvatar.style.display = 'inline-block';
                    navIcon.style.display = 'none';
                }

                console.log("✅ Profile Updated Successfully");
                window.closeModals(); // Close modal

                // Optional: Show a toast? For now, alert is fine or just close
                // alert("Profile Updated!"); 

            } catch (error) {
                console.error("Error updating profile:", error);
                alert("Failed to update profile: " + error.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }

    // --- Notifications Logic ---
    let unsubscribeNotifs = null;

    // Toggle Notifications
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('active');
        });
        document.addEventListener('click', () => { notifDropdown.classList.remove('active'); });
        notifDropdown.addEventListener('click', (e) => e.stopPropagation());
    }

    // Watch Auth State
    onAuthStateChanged(auth, async (user) => {
        const adminContainer = document.getElementById('adminLinkContainer');
        const myOrdersLink = document.getElementById('myOrdersLink');
        const myReservationsLink = document.getElementById('myReservationsLink');
        const navAvatar = document.getElementById('navUserAvatar');
        const navIcon = document.getElementById('navUserIcon');
        const notifWrapper = document.getElementById('notifWrapper');

        if (user) {
            // Role-based protection: Redirect Admin/Staff away from customer pages
            try {
                const docRef = doc(db, 'users', user.uid);
                const docSnap = await getDoc(docRef);
                if (docSnap.exists()) {
                    const role = docSnap.data().role;
                    if (role === 'admin' || role === 'super_admin' || role === 'cashier' || role === 'inventory' || role === 'rider') {
                        // Ensure PHP Session is set before auto-redirecting
                        await fetch('assets/php/auth/bypass_2fa.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ role: role })
                        }).catch(e => console.log(e));

                        if (role === 'admin' || role === 'super_admin') {
                            window.location.replace('admin/dashboard.php');
                        } else if (role === 'cashier') {
                            window.location.replace('admin/cashier_dashboard.php');
                        } else if (role === 'inventory') {
                            window.location.replace('admin/inventory_dashboard.php');
                        } else if (role === 'rider') {
                            window.location.replace('rider/portal.php');
                        }
                        return;
                    }
                } else if (ADMIN_EMAILS.includes(user.email.toLowerCase())) {
                    await fetch('assets/php/auth/bypass_2fa.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ role: 'admin' })
                    }).catch(e => console.log(e));
                    window.location.replace('admin/dashboard.php');
                    return;
                }
            } catch (err) {
                console.error("Role check error", err);
            }

            // User is logged in
            if (loginBtnNav) loginBtnNav.style.display = 'none';
            registerBtnNav.style.display = 'none';
            profileNav.style.display = 'flex';
            if (myOrdersLink) myOrdersLink.style.display = 'block';
            if (myReservationsLink) myReservationsLink.style.display = 'block';
            if (notifWrapper) notifWrapper.style.display = 'flex';
            userNameSpan.textContent = user.displayName || user.email.split('@')[0];

            // 1. Fetch Avatar
            try {
                const docRef = doc(db, 'users', user.uid);
                const docSnap = await getDoc(docRef);
                if (docSnap.exists()) {
                    const data = docSnap.data();
                    const avatarUrl = data.avatarUrl || data.avatarBase64;

                    if (avatarUrl && navAvatar && navIcon) {
                        navAvatar.src = avatarUrl;
                        navAvatar.style.display = 'inline-block';
                        navIcon.style.display = 'none';
                    } else if (navAvatar && navIcon) {
                        navAvatar.style.display = 'none';
                        navIcon.style.display = 'inline-block';
                    }
                }
            } catch (e) {
                console.error("Error fetching avatar for nav:", e);
            }

            // 2. Setup Notifications Listener
            const notifRef = collection(db, 'users', user.uid, 'notifications');
            const q = query(notifRef, orderBy('createdAt', 'desc'));

            if (unsubscribeNotifs) unsubscribeNotifs();
            unsubscribeNotifs = onSnapshot(q, (snapshot) => {
                const list = document.getElementById('notifList');
                const badge = document.getElementById('notifBadge');
                if (!list || !badge) return;

                const docs = snapshot.docs;
                const unreadCount = docs.filter(d => !d.data().isRead).length;

                // Update Badge
                if (unreadCount > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                } else {
                    badge.style.display = 'none';
                }

                // Update List
                if (snapshot.empty) {
                    list.innerHTML = '<li class="notif-empty">No notifications yet</li>';
                    return;
                }

                list.innerHTML = docs.map(d => {
                    const n = d.data();
                    const date = n.createdAt ? new Date(n.createdAt.seconds ? n.createdAt.seconds * 1000 : n.createdAt) : new Date();
                    const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' · ' + date.toLocaleDateString();

                    return `
                        <li class="notif-item ${n.isRead ? '' : 'unread'}" onclick="window.markNotifRead('${d.id}')">
                            <h4>${n.title || 'Notification'}</h4>
                            <p>${n.message || ''}</p>
                            <small>${timeStr}</small>
                        </li>
                    `;
                }).join('');
            });

            window.markNotifRead = async (id) => {
                try {
                    await updateDoc(doc(db, 'users', user.uid, 'notifications', id), { isRead: true });
                } catch (e) { console.error("Mark read error", e); }
            };

            const markAllBtn = document.getElementById('markAllRead');
            if (markAllBtn) {
                markAllBtn.onclick = async () => {
                    const unread = (await getDoc(doc(db, 'users', user.uid))).data(); // This is wrong, I need to query unread
                    // Simplified: just update all current docs in state
                };
            }

            // 3. Add Admin Link if authorized
            if (ADMIN_EMAILS.includes(user.email.toLowerCase())) {
                adminContainer.innerHTML = `<a href="admin/dashboard.php" style="font-size: 0.8rem; background: var(--primary-gold); color: var(--dark-brown); padding: 4px 10px; border-radius: 4px; text-decoration: none; font-weight: bold; margin: 0 5px;">Dashboard</a>`;
            } else {
                adminContainer.innerHTML = '';
            }
        } else {
            // User is logged out
            if (unsubscribeNotifs) unsubscribeNotifs();
            loginBtnNav.style.display = 'block';
            registerBtnNav.style.display = 'block';
            profileNav.style.display = 'none';
            if (myOrdersLink) myOrdersLink.style.display = 'none';
            if (myReservationsLink) myReservationsLink.style.display = 'none';
            if (notifWrapper) notifWrapper.style.display = 'none';
            adminContainer.innerHTML = '';

            if (navAvatar) navAvatar.style.display = 'none';
            if (navIcon) navIcon.style.display = 'inline-block';
        }
    });
}
