// Public Settings Loader - Dynamic Content Updates
import { getFirestore, doc, onSnapshot } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initPublicSettings(app) {
    const db = getFirestore(app);
    const settingsRef = doc(db, 'settings', 'general');

    console.log("Initializing Public Settings Listener...");

    onSnapshot(settingsRef, (docSnap) => {
        if (!docSnap.exists()) {
            console.warn("Public settings document not found.");
            return;
        }

        const data = docSnap.data();

        // Update Footer Contact Info
        const footerAddress = document.getElementById('footerAddress');
        const footerPhone = document.getElementById('footerPhone');
        const footerEmail = document.getElementById('footerEmail');

        if (footerAddress && data.address) footerAddress.innerText = data.address;
        if (footerPhone && data.phone) footerPhone.innerText = data.phone;
        if (footerEmail && data.email) footerEmail.innerText = data.email;

        // Update Footer Social Links
        const footerFacebook = document.getElementById('footerFacebook');
        const footerInstagram = document.getElementById('footerInstagram');

        if (footerFacebook) {
            if (data.facebook) {
                footerFacebook.href = data.facebook;
                footerFacebook.style.display = 'inline-flex';
            } else {
                footerFacebook.style.display = 'none';
            }
        }

        if (footerInstagram) {
            if (data.instagram) {
                footerInstagram.href = data.instagram;
                footerInstagram.style.display = 'inline-flex';
            } else {
                footerInstagram.style.display = 'none';
            }
        }

        // Update Website Title/Name if needed
        if (data.name) {
            const logoElements = document.querySelectorAll('.nav-logo, .hero-script, .hero-title');
            // We generally keep the branding, but if the user wants strictly dynamic:
            // logoElements.forEach(el => el.innerText = data.name);
            // For now, only update if it's a specific brand element.
        }

        console.log("Public settings updated from Firestore.");
    }, (error) => {
        console.error("Error listening to settings:", error);
    });
}
