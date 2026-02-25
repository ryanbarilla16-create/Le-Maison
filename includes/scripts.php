<!-- Modal Logic & Core UI -->
<script>
    window.closeModals = function() {
        document.querySelectorAll('.modal-overlay, .checkout-modal, .chatbot-window').forEach(m => m.classList.remove('active'));
    };
    window.switchModal = function(modalId) {
        closeModals();
        const modal = document.getElementById(modalId);
        if(modal) modal.classList.add('active');
    };
    
    // Navbar Scroll Effect
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('navbar');
        if (window.scrollY > 50) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');
    });

    // Intersection Observer for Reveal Animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    window.restaurantObserver = observer; // Expose to modules

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        
        // Populate Date Dropdowns
        const daySelect = document.getElementById('regBdayDay');
        if(daySelect && daySelect.options.length <= 1) {
            for (let d = 1; d <= 31; d++) {
                const opt = document.createElement('option');
                opt.value = d; opt.textContent = d;
                daySelect.appendChild(opt);
            }
        }
        const yearSelect = document.getElementById('regBdayYear');
        if(yearSelect && yearSelect.options.length <= 1) {
            const currentYear = new Date().getFullYear();
            for (let y = currentYear; y >= 1920; y--) {
                const opt = document.createElement('option');
                opt.value = y; opt.textContent = y;
                yearSelect.appendChild(opt);
            }
        }
    });

    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', () => {
            const targetId = icon.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });

    // (Chatbot toggle removed to avoid double-firing with app.js)
</script>

<!-- Firebase & App Modules -->
<script type="module" src="assets/js/app.js?v=<?php echo time(); ?>"></script>
<script type="module" src="assets/js/cart.js?v=<?php echo time(); ?>"></script>

<!-- Menu Loader -->
<script type="module">
    import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
    import { getFirestore, collection, getDocs, orderBy, query, addDoc, where } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
    import { getAuth } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
    import { firebaseConfig } from "./assets/js/firebase-config.js";

    const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
    const db = getFirestore(app);
    const auth = getAuth(app);

    let allMenuItems = [];

    async function loadPublicMenu() {
        const grid = document.getElementById('publicMenuGrid');
        const filtersDiv = document.getElementById('categoryFilters');
        if (!grid) return;

        try {
            const snapshot = await getDocs(collection(db, 'menu_items'));
            if (snapshot.empty) {
                grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#999; padding:3rem;">No menu items available yet.</p>';
                return;
            }
            allMenuItems = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));

            const categories = [...new Set(allMenuItems.map(item => item.category || 'Uncategorized'))].sort();
            let catHTML = '';
            categories.forEach((cat, index) => {
                catHTML += `<button class="cat-btn ${index === 0 ? 'active' : ''}" data-cat="${cat}" onclick="filterMenu('${cat}', this)">${cat}</button>`;
            });
            if(filtersDiv) filtersDiv.innerHTML = catHTML;

            if (categories.length > 0) {
                const defaultCat = categories[0];
                const headerText = document.getElementById('currentCategoryName');
                if (headerText) headerText.innerText = defaultCat;
                renderMenuCards(allMenuItems.filter(item => item.category === defaultCat));
            }
        } catch (err) {
            console.error('Error loading menu:', err);
            grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#D4654A;">Failed to load menu.</p>';
        }
    }

    function renderMenuCards(items) {
        const grid = document.getElementById('publicMenuGrid');
        if (items.length === 0) {
            grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#999; padding:2rem;">No items in this category.</p>';
            return;
        }
        grid.innerHTML = items.map(item => `
            <div class="menu-card">
                <img src="${item.imageUrl || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=400&fit=crop'}" alt="${item.name}" loading="lazy">
                <div class="menu-card-body">
                    <div class="menu-info-top">
                        <h3>${item.name}</h3>
                        <p class="menu-desc">${item.description || ''}</p>
                    </div>
                    <div class="menu-actions">
                        <p class="menu-price">₱${Number(item.price).toFixed(2)}</p>
                        <button class="btn-add-cart" onclick="window.addToCart('${item.id}', '${item.name.replace(/'/g, "\\'")}', ${item.price}, '${item.imageUrl || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=400&fit=crop'}')">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    window.filterMenu = function(cat, btn) {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        
        const headerText = document.getElementById('currentCategoryName');
        if (headerText) headerText.innerText = cat;
        
        renderMenuCards(allMenuItems.filter(item => item.category === cat));
    };

    // Reservation Submission Logic
    const RESTAURANT_MAX_CAPACITY = 50; // Maximum pax for the restaurant
    const CLOSING_TIME_MINUTES = 20 * 60 + 30; // 8:30 PM (20:30) in minutes

    // Helper: convert "HH:MM" to total minutes
    function timeToMinutes(t) {
        if (!t) return 0;
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
    }

    // Booking Type hint updater
    const bookingTypeSelect = document.getElementById('resBookingType');
    const bookingTypeHint = document.getElementById('bookingTypeHint');
    const guestsCapLabel = document.getElementById('resGuestsCapLabel');
    const resGuestsInput = document.getElementById('resGuests');

    if (bookingTypeSelect) {
        bookingTypeSelect.addEventListener('change', () => {
            const type = bookingTypeSelect.value;
            if (type === 'Exclusive Venue') {
                bookingTypeHint.innerHTML = '<i class="fas fa-crown" style="color:var(--primary-gold);"></i> The entire restaurant is reserved exclusively for your event (until closing).';
                if (guestsCapLabel) guestsCapLabel.textContent = `(Max: ${RESTAURANT_MAX_CAPACITY})`;
                if (resGuestsInput) resGuestsInput.max = RESTAURANT_MAX_CAPACITY;
            } else {
                bookingTypeHint.innerHTML = '<i class="fas fa-info-circle"></i> Regular Table shares the venue with other guests.';
                if (guestsCapLabel) guestsCapLabel.textContent = '(Max: 20)';
                if (resGuestsInput) resGuestsInput.max = 20;
            }
            checkAvailability();
        });
    }

    // Real-time availability check when date or time changes
    const resDateInput = document.getElementById('resDate');
    const resTimeInput = document.getElementById('resTime');
    if (resDateInput) resDateInput.addEventListener('change', checkAvailability);
    if (resTimeInput) resTimeInput.addEventListener('change', checkAvailability);

    async function checkAvailability() {
        const date = document.getElementById('resDate')?.value;
        const time = document.getElementById('resTime')?.value;
        const type = document.getElementById('resBookingType')?.value;
        const box = document.getElementById('resAvailabilityBox');
        const text = document.getElementById('resAvailText');
        const spinner = document.getElementById('resAvailSpinner');

        if (!date || !time || !box) return;

        box.style.display = 'block';
        spinner.style.display = 'inline';
        text.textContent = ' Checking availability...';
        box.style.background = '#f0f0f0';
        box.style.borderColor = '#ddd';
        box.style.color = '#555';

        const newTimeMins = timeToMinutes(time);

        try {
            // Query ALL reservations for this DATE (not exact time!)
            const q = query(collection(db, 'reservations'), where('date', '==', date));
            const snap = await getDocs(q);
            const activeBookings = snap.docs.map(d => d.data()).filter(r => r.status !== 'Cancelled');

            // Check for existing Exclusive bookings that OVERLAP the requested time
            const blockingExclusive = activeBookings.find(r => {
                if (r.bookingType !== 'Exclusive Venue') return false;
                const exclStart = timeToMinutes(r.time);
                // Exclusive runs from its start time until closing
                return newTimeMins >= exclStart && newTimeMins <= CLOSING_TIME_MINUTES;
            });

            if (blockingExclusive) {
                box.style.background = '#fff5f5';
                box.style.borderColor = '#fed7d7';
                box.style.color = '#c53030';
                const exclTime = blockingExclusive.time || '';
                text.innerHTML = `<i class="fas fa-times-circle"></i> Blocked — an Exclusive Venue event starts at ${exclTime} and runs until closing.`;
            } else if (type === 'Exclusive Venue') {
                // If booking Exclusive, check if ANY existing bookings fall at or after the new start time
                const conflicting = activeBookings.filter(r => {
                    const rTimeMins = timeToMinutes(r.time);
                    return rTimeMins >= newTimeMins;
                });
                if (conflicting.length > 0) {
                    box.style.background = '#fff5f5';
                    box.style.borderColor = '#fed7d7';
                    box.style.color = '#c53030';
                    text.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Cannot book Exclusive — ${conflicting.length} existing booking(s) overlap from ${time} onward.`;
                } else {
                    box.style.background = '#f0fff4';
                    box.style.borderColor = '#c6f6d5';
                    box.style.color = '#276749';
                    text.innerHTML = '<i class="fas fa-check-circle"></i> Time slot is available for Exclusive Venue!';
                }
            } else {
                // Regular Table — check capacity for this exact timeslot
                const sameSlotBookings = activeBookings.filter(r => r.time === time && r.bookingType !== 'Exclusive Venue');
                const totalGuests = sameSlotBookings.reduce((sum, r) => sum + (r.guests || 0), 0);
                const remaining = RESTAURANT_MAX_CAPACITY - totalGuests;

                if (remaining <= 0) {
                    box.style.background = '#fff5f5';
                    box.style.borderColor = '#fed7d7';
                    box.style.color = '#c53030';
                    text.innerHTML = '<i class="fas fa-times-circle"></i> This time slot is at full capacity.';
                } else {
                    box.style.background = '#f0fff4';
                    box.style.borderColor = '#c6f6d5';
                    box.style.color = '#276749';
                    text.innerHTML = `<i class="fas fa-check-circle"></i> Available! ${remaining} seats remaining for this slot.`;
                }
            }
        } catch (err) {
            box.style.background = '#fffbeb';
            box.style.borderColor = '#fefcbf';
            box.style.color = '#975a16';
            text.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Could not verify availability.';
            console.error('Availability check error:', err);
        } finally {
            spinner.style.display = 'none';
        }
    }

    const resForm = document.getElementById('reservationForm');
    if (resForm) {
        resForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const currentUser = auth.currentUser;
            if (!currentUser) {
                alert('Please sign in to make a reservation.');
                window.switchModal('authModal');
                return;
            }

            const resSubmitBtn = document.getElementById('resSubmitBtn');
            const originalText = resSubmitBtn.innerHTML;
            resSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            resSubmitBtn.disabled = true;

            try {
                const date = document.getElementById('resDate').value;
                const time = document.getElementById('resTime').value;
                const guests = parseInt(document.getElementById('resGuests').value) || 1;
                const occasion = document.getElementById('resOccasion').value;
                const bookingType = document.getElementById('resBookingType').value;

                // Basic Validations
                if(!date || !time || !occasion) {
                    throw new Error("Please fill in all required fields.");
                }

                const maxGuests = bookingType === 'Exclusive Venue' ? RESTAURANT_MAX_CAPACITY : 20;
                if(!Number.isInteger(Number(document.getElementById('resGuests').value)) || guests < 1 || guests > maxGuests) {
                    throw new Error(`Guest count must be a whole number between 1 and ${maxGuests}.`);
                }

                const selectedDate = new Date(date + "T00:00:00");
                const today = new Date();
                today.setHours(0,0,0,0);
                
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                
                const maxDateLimit = new Date(today);
                maxDateLimit.setDate(maxDateLimit.getDate() + 14);

                if (selectedDate < tomorrow) {
                    throw new Error("Reservations must be made at least 1 day in advance (no same-day bookings).");
                }
                if (selectedDate > maxDateLimit) {
                    throw new Error("You can only book up to 14 days in advance.");
                }

                const [hours, minutes] = time.split(':').map(Number);
                const timeInMinutes = hours * 60 + minutes;
                const minTime = 11 * 60 + 30; // 11:30 AM

                if (timeInMinutes < minTime || timeInMinutes > CLOSING_TIME_MINUTES) {
                    throw new Error("Reservation time must be between 11:30 AM and 8:30 PM.");
                }

                if (minutes !== 0 && minutes !== 30) {
                    throw new Error("Reservation time must be in 30-minute intervals (e.g., 12:00 PM or 12:30 PM).");
                }

                // ===== AVAILABILITY CHECK — QUERY BY DATE ONLY =====
                const q = query(collection(db, 'reservations'), where('date', '==', date));
                const snap = await getDocs(q);
                const activeBookings = snap.docs.map(d => d.data()).filter(r => r.status !== 'Cancelled');

                const newTimeMins = timeToMinutes(time);

                // Rule 1: Check if any existing EXCLUSIVE booking covers the requested time
                const blockingExclusive = activeBookings.find(r => {
                    if (r.bookingType !== 'Exclusive Venue') return false;
                    const exclStart = timeToMinutes(r.time);
                    return newTimeMins >= exclStart && newTimeMins <= CLOSING_TIME_MINUTES;
                });

                if (blockingExclusive) {
                    throw new Error(`This time is blocked by an Exclusive Venue event starting at ${blockingExclusive.time}. The restaurant is reserved until closing.`);
                }

                // Rule 2: If booking Exclusive, check no existing bookings overlap from start time onward
                if (bookingType === 'Exclusive Venue') {
                    const conflicting = activeBookings.filter(r => {
                        const rTimeMins = timeToMinutes(r.time);
                        return rTimeMins >= newTimeMins;
                    });
                    if (conflicting.length > 0) {
                        throw new Error(`Cannot book Exclusive Venue — ${conflicting.length} existing booking(s) are scheduled at or after ${time}. They must be cleared first.`);
                    }
                }

                // Rule 3: If Regular Table, check capacity for this exact time slot
                if (bookingType === 'Regular Table') {
                    const sameSlotBookings = activeBookings.filter(r => r.time === time && r.bookingType !== 'Exclusive Venue');
                    const totalGuests = sameSlotBookings.reduce((sum, r) => sum + (r.guests || 0), 0);
                    const remaining = RESTAURANT_MAX_CAPACITY - totalGuests;
                    if (guests > remaining) {
                        throw new Error(`Not enough capacity. Only ${remaining} seat(s) remaining for this time slot. You requested ${guests}.`);
                    }
                }

                const resData = {
                    userId: currentUser.uid,
                    guestName: currentUser.displayName || currentUser.email.split('@')[0],
                    customerEmail: currentUser.email,
                    date: date,
                    time: time,
                    guests: guests,
                    occasion: occasion,
                    bookingType: bookingType,
                    status: 'Pending',
                    createdAt: new Date(),
                    table: null
                };

                await addDoc(collection(db, 'reservations'), resData);
                alert('Reservation submitted successfully! We will confirm your table shortly.');
                resForm.reset();
                document.getElementById('resAvailabilityBox').style.display = 'none';
                window.closeModals();
            } catch (err) {
                console.error("Reservation error: ", err);
                alert(err.message || 'An error occurred while submitting your reservation.');
            } finally {
                resSubmitBtn.innerHTML = originalText;
                resSubmitBtn.disabled = false;
            }
        });
    }

    loadPublicMenu();
</script>

<!-- Reviews Loader -->
<script type="module">
    import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
    import { getFirestore, collection, query, where, orderBy, limit, onSnapshot } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
    import { firebaseConfig } from "./assets/js/firebase-config.js";

    const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
    const db = getFirestore(app);

    const reviewsGrid = document.getElementById('homepageReviews');
    if (reviewsGrid) {
        const q = query(collection(db, 'reviews'), where('status', '==', 'APPROVED'), orderBy('createdAt', 'desc'), limit(6));
        onSnapshot(q, (snapshot) => {
            if (snapshot.empty) {
                reviewsGrid.innerHTML = '<p class="reviews-empty" style="grid-column:1/-1; text-align:center; padding:3rem; color:#888;">Our guest stories will appear here soon.</p>';
                return;
            }
            reviewsGrid.innerHTML = snapshot.docs.map(docSnap => {
                const r = docSnap.data();
                const rating = r.rating || 5;
                const stars = Array(5).fill(0).map((_, i) => `<i class="${i < rating ? 'fas' : 'far'} fa-star" style="font-size: 0.7rem; margin-right: 2px; color: var(--primary-gold);"></i>`).join('');
                
                const date = r.createdAt ? new Date(r.createdAt.seconds * 1000).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '';
                const name = r.customerName || 'Distinguished Guest';
                
                return `
                    <div class="review-card reveal">
                        <div class="review-quote"><i class="fas fa-quote-left"></i></div>
                        <div class="review-stars">${stars}</div>
                        <p class="review-comment">"${r.comment || 'A truly exceptional culinary journey.'}"</p>
                        <div class="review-author">
                            <div class="review-avatar">${name.charAt(0)}</div>
                            <div class="review-info">
                                <h4 class="review-author-name">${name}</h4>
                                <span class="review-author-date">${date}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            // Trigger animation for new cards
            if (window.restaurantObserver) {
                document.querySelectorAll('.review-card.reveal').forEach(el => window.restaurantObserver.observe(el));
            }
        });
    }
</script>

<!-- Carousel Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('aboutCarousel');
        if (!carousel) return;
        const slides = carousel.querySelectorAll('.carousel-slide');
        const dots = carousel.querySelectorAll('.carousel-dot');
        let current = 0;
        let timer;
        const showSlide = (index) => {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
        };
        const next = () => showSlide(current + 1);
        timer = setInterval(next, 3500);
        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                showSlide(idx);
                clearInterval(timer);
                timer = setInterval(next, 3500);
            });
        });
    });
</script>
