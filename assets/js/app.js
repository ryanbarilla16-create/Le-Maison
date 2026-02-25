// Main Application Controller
// Le Maison de Yelo Lane - Firebase & Vertex AI Integration

import { initializeApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-analytics.js";
import { firebaseConfig } from "./firebase-config.js";

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

// Initialize modules
import { initChatbot } from "./chatbot-ai.js";
import { initAuth } from "./auth.js";
import { initPublicSettings } from "./settings.js";

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Public Settings
    try {
        initPublicSettings(app);
    } catch (e) {
        console.error("Settings Initialization failed:", e);
    }
    // UI Interactions
    const launcher = document.querySelector('.chatbot-launcher');
    const window = document.querySelector('.chatbot-window');
    const closeBtn = document.querySelector('#closeAI');

    if (launcher && window && closeBtn) {
        launcher.addEventListener('click', () => {
            window.classList.toggle('active');
            if (window.classList.contains('active')) {
                const inputEl = document.querySelector('#aiInput');
                if (inputEl) inputEl.focus();
            }
        });

        closeBtn.addEventListener('click', () => {
            window.classList.remove('active');
        });
    }

    // Initialize AI & Auth
    try {
        initChatbot(app);
    } catch (e) {
        console.error("AI Initialization failed:", e);
    }

    try {
        initAuth(app);
    } catch (e) {
        console.error("Auth Initialization failed:", e);
    }

    console.log("Le Maison de Yelo Lane: Firebase, AI & Auth initialized.");
});
