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





<!-- Reviews Loader removed -->

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


