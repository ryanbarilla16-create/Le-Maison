<!-- LOGIN MODAL -->
<div class="modal-overlay" id="loginModal">
    <div class="auth-modal">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="auth-icon"><i class="fas fa-sign-in-alt"></i></div>
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>
        <form class="auth-form" id="loginForm">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="loginEmail" placeholder="your@email.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" id="loginPassword" placeholder="••••••••" required>
                    <i class="fas fa-eye toggle-password" data-target="loginPassword" style="position: absolute; right: 15px; top: 18px; cursor: pointer; color: #888; font-size: 0.9rem;"></i>
                </div>
            </div>
            <div style="text-align: right; margin-bottom: 1.5rem;">
                <a href="#" onclick="event.preventDefault(); switchModal('forgotPasswordModal')" style="font-size: 0.8rem; color: var(--primary-gold); text-decoration: none; font-weight: 600;">Forgot Password?</a>
            </div>
            <button type="submit" id="loginSubmitBtn">Login</button>
        </form>
        <div class="auth-switch">New to Le Maison? <a href="#" onclick="switchModal('registerModal')">Sign Up</a></div>
    </div>
</div>

<!-- FORGOT PASSWORD MODAL -->
<div class="modal-overlay" id="forgotPasswordModal">
    <div class="auth-modal">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="auth-icon"><i class="fas fa-key"></i></div>
            <h2>Reset Password</h2>
            <p>Enter your email and we'll send you a reset link</p>
        </div>
        <form class="auth-form" id="forgotPasswordForm">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="forgotEmail" placeholder="your@email.com" required>
            </div>
            <div id="forgotMsg" style="display:none; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 500; text-align: center;"></div>
            <button type="submit" id="forgotSubmitBtn"><i class="fas fa-paper-plane"></i> Send Reset Link</button>
        </form>
        <div class="auth-switch" style="margin-top: 1.5rem;">
            <a href="#" onclick="event.preventDefault(); closeModals(); setTimeout(() => document.getElementById('loginModal').classList.add('active'), 300);" style="color: var(--primary-gold); text-decoration: none; font-weight: 600;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>Back to Login
            </a>
        </div>
    </div>
</div>

<!-- TERMS MODAL -->
<div class="modal-overlay" id="termsModal">
    <div class="auth-modal">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="auth-icon"><i class="fas fa-file-contract"></i></div>
            <h2>Terms and Conditions</h2>
            <p>Please review our terms before registering</p>
        </div>
        <div style="padding: 0 2rem 2rem; max-height: 300px; overflow-y: auto; text-align: left; font-size: 0.9rem; color: #555; line-height: 1.6;">
            <p style="margin-bottom: 0.5rem;"><strong>1. Acceptance of Terms</strong><br>By accessing and using this service, you accept and agree to be bound by the terms and provision of this agreement.</p>
            <p style="margin-bottom: 0.5rem;"><strong>2. User Accounts</strong><br>You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer.</p>
            <p style="margin-bottom: 0.5rem;"><strong>3. Privacy</strong><br>Your use of the service is subject to Le Maison de Yelo Lane's Privacy Policy. Please review our Privacy Policy, which also governs the site and informs users of our data collection practices.</p>
            <p style="margin-bottom: 0.5rem;"><strong>4. Orders & Payment</strong><br>All orders placed through our platform are subject to availability and acceptance by us. Prices are subject to change without notice.</p>
        </div>
        <button class="btn-checkout" style="width: 80%; margin: 0 auto 2.5rem; display: block;" onclick="document.getElementById('termsModal').classList.remove('active'); document.getElementById('registerModal').classList.add('active');">I Agree & Continue</button>
    </div>
</div>

<!-- REGISTER MODAL -->
<div class="modal-overlay" id="registerModal">
    <div class="auth-modal register-modal">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
            <h2>Create Account</h2>
            <p>Join our distinguished community</p>
        </div>
        <form class="auth-form" id="registerForm">
            <div class="form-group">
                <label class="form-label">Legal Name</label>
                <div class="name-row">
                    <input type="text" id="regFirstName" placeholder="First" required>
                    <input type="text" id="regMiddleName" placeholder="Middle">
                    <input type="text" id="regLastName" placeholder="Last" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="regEmail" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Birth Date</label>
                <div class="bday-row">
                    <select id="regBdayMonth" required>
                        <option value="" disabled selected>Month</option>
                        <option value="01">January</option><option value="02">February</option><option value="03">March</option>
                        <option value="04">April</option><option value="05">May</option><option value="06">June</option>
                        <option value="07">July</option><option value="08">August</option><option value="09">September</option>
                        <option value="10">October</option><option value="11">November</option><option value="12">December</option>
                    </select>
                    <select id="regBdayDay" required>
                        <option value="" disabled selected>Day</option>
                    </select>
                    <select id="regBdayYear" required>
                        <option value="" disabled selected>Year</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Preferred Username</label>
                <input type="text" id="regUsername" placeholder="e.g. JeanValjean" required>
            </div>

            <div class="form-group">
                <label class="form-label">Guest Profile Image</label>
                <div class="avatar-upload" id="avatarDropZone" onclick="document.getElementById('regAvatar').click()">
                    <i class="fas fa-camera"></i>
                    <p style="font-size: 0.8rem; color: #555; margin: 0.5rem 0;">Drag and drop or click to upload</p>
                    <img class="avatar-preview" id="avatarPreview" alt="Preview">
                </div>
                <input type="file" id="regAvatar" accept="image/*" style="display:none">
            </div>

            <div class="auth-divider"><span>Security</span></div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="regPassword" placeholder="Min. 6 chars" required minlength="6">
                        <i class="fas fa-eye toggle-password" data-target="regPassword" style="position: absolute; right: 12px; top: 15px; cursor: pointer; color: #888;"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm</label>
                    <div style="position: relative;">
                        <input type="password" id="regConfirmPassword" placeholder="Confirm" required>
                        <i class="fas fa-eye toggle-password" data-target="regConfirmPassword" style="position: absolute; right: 12px; top: 15px; cursor: pointer; color: #888;"></i>
                    </div>
                </div>
            </div>

            <button type="submit" id="regSubmitBtn">Create Account</button>
        </form>
        <div class="auth-switch">Already a member? <a href="#" onclick="switchModal('loginModal')">Login</a></div>
    </div>
</div>

<!-- Cart Drawer -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
        <h3>Your Cart</h3>
        <i class="fas fa-times cart-close" id="cartClose"></i>
    </div>
    <div class="cart-items" id="cartItemsList">
        <div class="cart-empty">
            <i class="fas fa-shopping-cart"></i>
            <p>Your cart is empty</p>
        </div>
    </div>
    <div class="cart-footer" id="cartFooter" style="display: none;">
        <div class="cart-total">
            <span>Total:</span>
            <span id="cartTotal">₱0.00</span>
        </div>
        <button class="btn-checkout" id="checkoutBtn">Proceed to Checkout</button>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal-overlay" id="reservationModal">
    <div class="auth-modal">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="auth-icon"><i class="fas fa-calendar-check"></i></div>
            <h2>Book a Table</h2>
            <p>Reserve your spot at Le Maison</p>
        </div>
        <form class="auth-form" id="reservationForm">
            <!-- Guest Name removed as per request, since user will be logged in -->
            <div class="form-group">
                <label class="form-label">Booking Type</label>
                <select id="resBookingType" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px; font-family: 'Inter', sans-serif; appearance: auto; background: #fff;">
                    <option value="Regular Table" selected>🪑 Regular Table</option>
                    <option value="Exclusive Venue">🏛️ Exclusive Venue (Whole Restaurant)</option>
                </select>
                <small id="bookingTypeHint" style="display:block; margin-top:6px; color:#888; font-size:0.82rem;">
                    <i class="fas fa-info-circle"></i> Regular Table shares the venue with other guests.
                </small>
            </div>
            <div class="form-group">
                <label class="form-label">Date & Time</label>
                <div class="name-row" style="grid-template-columns: 1fr 1fr;">
                    <input type="date" id="resDate" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" max="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
                    <input type="time" id="resTime" required min="11:30" max="20:30" step="1800">
                </div>
            </div>
            <!-- Availability Indicator -->
            <div id="resAvailabilityBox" style="display:none; padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 0.5rem; border: 1px solid transparent;">
                <i class="fas fa-circle-notch fa-spin" id="resAvailSpinner" style="display:none;"></i>
                <span id="resAvailText"></span>
            </div>
            <div class="form-group">
                <label class="form-label">Number of Guests <small id="resGuestsCapLabel" style="color:#999;">(Max: 20)</small></label>
                <input type="number" id="resGuests" min="1" max="20" placeholder="e.g., 2" required>
            </div>
            <div class="form-group">
                <label class="form-label">Type of Occasion</label>
                <select id="resOccasion" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px; font-family: 'Inter', sans-serif; appearance: auto; background: #fff;">
                    <option value="" disabled selected>Select an occasion</option>
                    <option value="Casual Dining">Casual Dining</option>
                    <option value="Birthday">Birthday</option>
                    <option value="Anniversary">Anniversary</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Corporate Event">Corporate Event</option>
                    <option value="Private Gathering">Private Gathering</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit" id="resSubmitBtn" class="btn-checkout" style="width: 100%; margin-top: 1rem; border-radius: 5px; padding: 12px; font-weight: bold;"><i class="fas fa-calendar-check"></i> Confirm Reservation</button>
        </form>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal-overlay" id="checkoutModal">
    <div class="auth-modal" style="max-width: 900px; padding: 0; overflow: hidden;">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; min-height: 600px;">
            <!-- Left Side: Data Entry -->
            <div style="padding: 3rem; background: #fff;">
                <h2 style="font-family: 'Playfair Display', serif; color: var(--dark-brown); margin-bottom: 2rem;">Checkout</h2>
                
                <div class="order-type-toggle" style="margin-bottom: 2rem;">
                    <button class="order-type-btn active" id="dineInBtn"><i class="fas fa-utensils"></i> Dine In</button>
                    <button class="order-type-btn" id="takeOutBtn"><i class="fas fa-shopping-bag"></i> Take Out</button>
                    <button class="order-type-btn" id="deliveryBtn"><i class="fas fa-truck"></i> Delivery</button>
                </div>

                <div class="address-fields" id="addressFields" style="display: none;">
                    <h4 style="margin-bottom:1rem; color: var(--dark-brown); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Delivery Address</h4>
                    <div class="form-group">
                        <input type="text" id="street" placeholder="Street Address / Landmark" style="width: 100%; padding: 0.8rem 0; border: none; border-bottom: 1px solid #eee; margin-bottom: 1rem; outline: none;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <input type="text" id="barangay" placeholder="Barangay" style="width: 100%; padding: 0.8rem 0; border: none; border-bottom: 1px solid #eee; outline: none;">
                        <input type="text" id="city" value="Pagsanjan" readonly style="width: 100%; padding: 0.8rem 0; border: none; border-bottom: 1px solid #eee; background: #fafafa; outline: none;">
                    </div>
                    <input type="tel" id="contact" placeholder="Contact number (09xx...)" style="width: 100%; padding: 0.8rem 0; border: none; border-bottom: 1px solid #eee; margin-top: 1rem; outline: none;">
                </div>

                <div class="payment-methods" style="margin-top: 2rem;">
                    <h4 style="margin-bottom:1.5rem; color: var(--dark-brown); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Payment Method</h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="payment-method active" id="gcashPayment" style="border: 1px solid var(--primary-gold); padding: 1.2rem; border-radius: 8px; cursor: pointer;">
                            <strong style="display: block; font-size: 0.9rem;"><i class="fas fa-mobile-alt"></i> GCash</strong>
                            <small style="color: #888;">Secure redirection to payment gateway</small>
                        </div>
                        <div class="payment-method" id="counterPayment" style="border: 1px solid #eee; padding: 1.2rem; border-radius: 8px; cursor: pointer;">
                            <strong style="display: block; font-size: 0.9rem;" id="counterLabel"><i class="fas fa-cash-register"></i> Pay at Counter</strong>
                            <small style="color: #888;" id="counterDesc">Pay when you arrive at Le Maison</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div style="padding: 3rem; background: #fafafa; border-left: 1px solid #eee; display: flex; flex-direction: column;">
                <h4 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 2px; color: #888; margin-bottom: 2rem;">Order Summary</h4>
                
                <div style="flex: 1;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:1rem; color: #555;">
                        <span>Subtotal</span>
                        <span id="checkoutSubtotal">₱0.00</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2rem; color: #555;">
                        <span>Service Fee</span>
                        <span style="font-weight: 600; color: #28a745;">FREE</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-top: 1.5rem; border-top: 2px solid #eee;">
                        <span style="font-weight: 700; font-size: 1.2rem; color: var(--dark-brown);">Total</span>
                        <span id="checkoutTotal" style="font-weight: 700; font-size: 1.5rem; color: var(--primary-gold);">₱0.00</span>
                    </div>
                </div>

                <div style="margin-top: auto;">
                    <p style="font-size: 0.75rem; color: #999; line-height: 1.6; margin-bottom: 2rem; text-align: center;">
                        By clicking "Place Order", you agree to the Terms of Service and Privacy Policy of Le Maison de Yelo Lane.
                    </p>
                    <button class="btn-checkout" id="placeOrderBtn" style="width: 100%; border-radius: 50px; padding: 1.2rem; background: var(--dark-brown); color: var(--primary-gold); border: 1px solid var(--primary-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; cursor: pointer;">
                        <i class="fas fa-check-circle"></i> Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editProfileModal">
    <div class="auth-modal">
        <div class="modal-close" onclick="closeModals()"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="avatar-upload-container" style="display:flex; justify-content:center; margin-bottom:1rem;">
                <div style="position:relative; width:100px; height:100px;">
                    <img id="editUserProfileAvatar" src="https://ui-avatars.com/api/?name=User" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid var(--primary-gold); box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                    <button type="button" onclick="document.getElementById('editUserAvatarInput').click()" style="position:absolute; bottom:0; right:0; background:var(--dark-brown); color:var(--primary-gold); border:2px solid var(--white); width:32px; height:32px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.3s;"><i class="fas fa-camera" style="font-size:0.8rem;"></i></button>
                    <input type="file" id="editUserAvatarInput" style="display:none;" accept="image/*">
                </div>
            </div>
            <h2>Edit Profile</h2>
            <p>Update your personal information</p>
        </div>
        <form id="editProfileForm" class="auth-form">
            <div class="form-group">
                <div class="name-row">
                    <input type="text" id="editFirstName" placeholder="First Name" required>
                    <input type="text" id="editMiddleName" placeholder="Middle">
                    <input type="text" id="editLastName" placeholder="Last Name" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Birthday</label>
                <input type="date" id="editBirthday" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="editEmail" disabled style="background:#f0f0f0; cursor:not-allowed; color:#888;">
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" id="editPhone" placeholder="09123456789 (Optional)">
            </div>
            <div class="form-group">
                <label class="form-label">Default Address</label>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <select id="editAddressLabel" class="form-control" style="padding: 0.8rem;">
                        <option value="Home">Home</option><option value="Office">Office</option><option value="Partner's House">Partner's House</option><option value="Other">Other</option>
                    </select>
                    <input type="text" id="editAddressStreet" placeholder="Street Name / House No.">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.5rem; margin-bottom: 0.5rem;">
                    <input type="text" id="editAddressBarangay" placeholder="Barangay">
                    <input type="text" id="editAddressCity" value="Pagsanjan, Laguna" disabled style="background:#f0f0f0;">
                </div>
                <textarea id="editAddressLandmark" class="form-control" placeholder="Landmark / Notes to Rider" rows="2"></textarea>
            </div>
            <div class="form-group" style="border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem;">
                <label class="form-label" style="font-weight: 700; color: var(--dark-brown);">Account Security</label>
                <input type="password" id="editPin" placeholder="Setup / Change 4-Digit PIN" maxlength="4" style="width: 100%; margin-bottom: 0.8rem;">
                <input type="password" id="editCurrentPassword" placeholder="Current Password (Required for changes)" style="width: 100%; margin-bottom: 0.8rem;">
                <input type="password" id="editNewPassword" placeholder="New Password" style="width: 100%; margin-bottom: 0.8rem;">
                <input type="password" id="editConfirmPassword" placeholder="Confirm New Password" style="width: 100%;">
            </div>
            <button type="submit" id="editProfileSubmitBtn"><i class="fas fa-save"></i> Save Changes</button>
        </form>
    </div>
</div>

<!-- OTP Verification Modal -->
<div id="otpModal" class="modal-overlay">
    <div class="auth-modal" style="max-width: 400px; text-align: center;">
        <div class="modal-close" onclick="document.getElementById('otpModal').classList.remove('active')"><i class="fas fa-times"></i></div>
        <div class="auth-modal-header">
            <div class="auth-icon"><i class="fas fa-envelope-open-text"></i></div>
            <h2>Verify Email</h2>
            <p>We've sent a 6-digit code to your email.</p>
        </div>
        <div class="form-group" style="margin: 2rem 0;">
            <input type="text" id="otpInput" maxlength="6" placeholder="123456" style="text-align: center; font-size: 2rem; letter-spacing: 10px; font-weight: bold; border: 2px solid var(--primary-gold); border-radius: 8px; width: 100%;">
        </div>
        <p id="otpError" style="color: #dc3545; font-size: 0.9rem; margin-top: 0.5rem; display: none;"></p>
        <button class="btn-checkout" id="verifyOtpBtn" style="width: 100%; margin-top: 1rem;"><i class="fas fa-check-circle"></i> Verify Code</button>
        <div style="margin-top: 1.5rem; font-size: 0.9rem;">
            <span style="color:#888;">Didn't receive code?</span> 
            <a href="#" id="resendOtpLink" style="color:var(--primary-gold); font-weight:bold; margin-left: 5px; text-decoration: none;">Resend Code</a>
        </div>
    </div>
</div>
