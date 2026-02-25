<!-- Footer -->
<footer class="footer-main">
    <div class="footer-grid">
        <!-- Brand Column -->
        <div class="footer-col">
            <h3 style="font-family: 'Pinyon Script', cursive; font-size: 3rem; color: var(--primary-gold); margin-bottom: 0.8rem;">Le Maison</h3>
            <p style="color: #aaa; font-size: 0.9rem; line-height: 2; margin-bottom: 2rem; font-weight: 300;">
                Experience the timeless elegance of French cuisine in the heart of Pagsanjan. Where tradition meets modern luxury in every exquisite plate.
            </p>
            <div style="display: flex; gap: 1.2rem;">
                <a href="#" id="footerFacebook" style="color: var(--primary-gold); font-size: 1.4rem; transition: 0.3s;"><i class="fab fa-facebook-f"></i></a>
                <a href="#" id="footerInstagram" style="color: var(--primary-gold); font-size: 1.4rem; transition: 0.3s;"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- About Us (Map) Column -->
        <div class="footer-col" id="location">
            <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--white); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 2px;">Find Us</h4>
            <div style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(212, 175, 55, 0.2); box-shadow: var(--shadow-premium);">
                <iframe 
                    width="100%" 
                    height="180" 
                    frameborder="0" 
                    scrolling="no" 
                    marginheight="0" 
                    marginwidth="0" 
                    style="display: block; filter: grayscale(100%) contrast(1.2) sepia(20%); transition: 0.5s;"
                    onmouseover="this.style.filter='none'" 
                    onmouseout="this.style.filter='grayscale(100%) contrast(1.2) sepia(20%)'"
                    src="https://maps.google.com/maps?q=Le+Maison+Yelo+Lane+Pagsanjan+Branch+laguna&t=&z=17&ie=UTF8&iwloc=&output=embed">
                </iframe>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
            <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--white); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 2px;">Quick Links</h4>
            <ul class="footer-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">Our Story</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#location">Location</a></li>
                <li><a href="#" onclick="document.getElementById('aiLauncher').click()">Ask Concierge</a></li>
            </ul>
        </div>

        <!-- Contact Info -->
        <div class="footer-col">
            <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--white); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 2px;">Contact Us</h4>
            <ul style="list-style: none; padding:0;">
                <li style="margin-bottom: 1.2rem; color: #aaa; display: flex; gap: 12px; font-size: 0.95rem;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary-gold); margin-top: 5px;"></i>
                    <span id="footerAddress">123 Rizal Street, Pagsanjan<br>Laguna, Philippines 4008</span>
                </li>
                <li style="margin-bottom: 1.2rem; color: #aaa; display: flex; gap: 12px; font-size: 0.95rem;">
                    <i class="fas fa-phone" style="color: var(--primary-gold); margin-top: 5px;"></i>
                    <span id="footerPhone">+63 912 345 6789</span>
                </li>
                <li style="margin-bottom: 1.2rem; color: #aaa; display: flex; gap: 12px; font-size: 0.95rem;">
                    <i class="fas fa-envelope" style="color: var(--primary-gold); margin-top: 5px;"></i>
                    <span id="footerEmail">bonjour@lemaison.ph</span>
                </li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-col" style="min-width: 280px;">
            <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--white); margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 2px;">Newsletter</h4>
            <p style="color: #aaa; font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.6; font-weight: 300;">Stay updated with our latest French culinary creations.</p>
            <form onsubmit="event.preventDefault(); alert('Merci! Thank you for subscribing!');" style="display: flex; gap: 0.5rem; border-bottom: 1px solid rgba(212, 175, 55, 0.3); padding-bottom: 5px;">
                <input type="email" placeholder="Email Address" style="flex: 1; padding: 0.8rem 0; border: none; background: transparent; color: white; outline: none; font-size: 0.9rem;">
                <button type="submit" style="background: transparent; color: var(--primary-gold); border: none; padding: 0.8rem; cursor: pointer; font-weight: 700; transition: 0.3s; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">Join</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 Le Maison de Yelo Lane. All rights reserved.</p>
    </div>
</footer>
