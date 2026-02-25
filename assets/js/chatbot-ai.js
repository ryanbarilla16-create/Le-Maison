// AI Chatbot Module using Vertex AI for Firebase (Gemini)
// Le Maison de Yelo Lane - AI Concierge

import { getVertexAI, getGenerativeModel } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-vertexai.js";
import { getFirestore, collection, getDocs, doc, getDoc, query, where } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";

let model;
let db;
let auth;
let currentUser = null;

const SYSTEM_INSTRUCTION = "You are 'L’Assistant', the virtual concierge for 'Le Maison de Yelo Lane', a premium French restaurant in Pagsanjan. You are elegant, helpful, and speak with a slight French flair. You assist guests with menu inquiries, reservations, and general information about the restaurant. If asked about the menu, mention French Onion Soup, Coq au Vin, and Crème Brûlée. Always be polite and inviting.";

export function initChatbot(app) {
    // Initialize services
    const vertexAI = getVertexAI(app);
    db = getFirestore(app);
    auth = getAuth(app);

    // Track auth state
    onAuthStateChanged(auth, (user) => {
        currentUser = user;
    });

    // Initialize the generative model
    model = getGenerativeModel(vertexAI, {
        model: "gemini-1.5-flash",
        systemInstruction: {
            parts: [{ text: SYSTEM_INSTRUCTION }]
        }
    });

    setupEventListeners();
}

function setupEventListeners() {
    const input = document.querySelector('#aiInput');
    const sendBtn = document.querySelector('#sendAI');

    if (!input || !sendBtn) return;

    // Handle button clicks from bot messages
    document.querySelector('#aiMessages').addEventListener('click', (e) => {
        if (e.target.classList.contains('chat-btn')) {
            const action = e.target.dataset.action;
            const value = e.target.dataset.value;
            handleCommand(action, value);
        }
    });

    const handleSend = async () => {
        const text = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';

        const lowerText = text.toLowerCase();

        // Check for direct commands first
        if (await processRules(lowerText)) {
            return;
        }

        // Fallback to AI
        try {
            const botMessageId = appendMessage('bot', '...', true);

            // Generate content
            const result = await model.generateContent(text);
            const response = await result.response;
            const botText = response.text();

            updateMessage(botMessageId, botText);
        } catch (error) {
            console.error("AI Error:", error);
            appendMessage('bot', "Pardon, I'm having a momentary difficulty. Please try again later.");
        }
    };

    sendBtn.addEventListener('click', handleSend);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleSend();
    });
}

// Rule-based Processing
async function processRules(text) {
    // 1. Greetings
    if (['hi', 'hello', 'kamusta', 'start', 'hey'].includes(text)) {
        const msg = "Bonjour! Welcome to Le Maison de Yelo Lane. I am your concierge. How may I assist you today?";
        const buttons = [
            { label: "View Menu", action: "menu" },
            { label: "Track Order", action: "track_order_prompt" },
            { label: "Store Info", action: "info" },
            { label: "Help", action: "help" }
        ];
        appendMessage('bot', msg, false, buttons);
        return true;
    }

    // 2. Menu
    if (text === 'menu' || text === 'view menu') {
        await handleCommand('menu');
        return true;
    }

    // 3. Order Status
    if (text === 'order status' || text === 'track order') {
        handleCommand('track_order_prompt');
        return true;
    }

    // Check for specific order ID pattern (e.g., #123)
    if (text.startsWith('#')) {
        await handleCommand('check_order', text.substring(1));
        return true;
    }

    // 4. Store Info
    if (text === 'store info' || text === 'location' || text === 'hours') {
        handleCommand('info');
        return true;
    }

    // 5. Help
    if (text === 'help' || text === 'options') {
        handleCommand('help');
        return true;
    }

    return false;
}

// Command Handler
async function handleCommand(action, value = null) {
    if (action === 'menu') {
        try {
            appendMessage('bot', 'Fetching our exquisite menu categories...', true);
            const querySnapshot = await getDocs(collection(db, "menu_items"));
            const categories = new Set();
            querySnapshot.forEach(doc => {
                const data = doc.data();
                if (data.category && data.available !== false) categories.add(data.category);
            });

            const buttons = Array.from(categories).map(cat => ({
                label: cat.charAt(0).toUpperCase() + cat.slice(1),
                action: 'view_category',
                value: cat
            }));

            updateLastMessage("Please select a category to explore:", buttons);
        } catch (e) {
            console.error(e);
            updateLastMessage("I apologize, but I cannot access the menu at this moment.");
        }
    }
    else if (action === 'view_category') {
        try {
            appendMessage('bot', `Retrieving ${value} selections...`, true);
            const q = query(collection(db, "menu_items"), where("category", "==", value));
            const querySnapshot = await getDocs(q);

            let message = `<strong>${value.toUpperCase()}</strong><br>`;
            querySnapshot.forEach(doc => {
                const item = doc.data();
                if (item.available !== false) {
                    message += `• ${item.name} - ₱${item.price}<br>`;
                }
            });

            const buttons = [{ label: "Order Now", action: "redirect_order" }];
            updateLastMessage(message, buttons);
        } catch (e) {
            updateLastMessage("Could not load items.");
        }
    }
    else if (action === 'track_order_prompt') {
        if (!currentUser) {
            appendMessage('bot', "Please log in to track your orders.", false, [
                { label: "Login", action: "redirect_login" },
                { label: "Register", action: "redirect_register" }
            ]);
        } else {
            appendMessage('bot', "Please enter your Order ID (e.g., #123) to check its status.");
        }
    }
    else if (action === 'check_order') {
        if (!currentUser) {
            handleCommand('track_order_prompt');
            return;
        }

        // Simple mock check or real DB check if ID is known
        // Since Firestore IDs are typically UUIDs, user might input a short ID if implemented.
        // For now, assuming standard Firestore ID or specific field search.
        // Let's assume we search by a 'displayId' or just try to find the doc.

        appendMessage('bot', `Checking status for Order #${value}...`, true);

        // *NOTE*: In a real app, you'd query by a custom short ID field
        // query(collection(db, 'orders'), where('shortId', '==', value))
        // For now, let's simulate or check if ID exists in a collection if user knows full ID
        // Or specific 'order_id' field.

        try {
            // Assuming 'order_id' is the field for human-readable IDs
            const q = query(collection(db, "orders"), where("order_formatted_id", "==", `#${value}`));
            const querySnapshot = await getDocs(q); // Try finding by formatted ID first

            if (!querySnapshot.empty) {
                const order = querySnapshot.docs[0].data();
                updateLastMessage(`Order #${value} is currently: <strong>${order.status.toUpperCase()}</strong>`);
            } else {
                updateLastMessage(`I could not find an order with ID #${value}. Please check and try again.`);
            }
        } catch (e) {
            updateLastMessage(`Unable to track order #${value}.`);
        }
    }
    else if (action === 'info') {
        const msg = `
            <strong>Le Maison de Yelo Lane</strong><br>
            📍 Pagsanjan, Laguna<br>
            🕒 Open Daily: 10:00 AM - 9:00 PM<br>
            📞 (049) 555-1234
        `;
        appendMessage('bot', msg, false, [
            { label: "View Map", action: "open_map" } // Mock action
        ]);
    }
    else if (action === 'help') {
        const msg = "I can assist you with:<br>• Browsing the Menu<br>• Tracking Orders<br>• Restaurant Information";
        const buttons = [
            { label: "Menu", action: "menu" },
            { label: "Track Order", action: "track_order_prompt" }
        ];
        appendMessage('bot', msg, false, buttons);
    }
    else if (action === 'redirect_login') {
        window.location.href = 'login.php';
    }
    else if (action === 'redirect_register') {
        window.location.href = 'register.php'; // or toggle register modal
    }
    else if (action === 'redirect_order') {
        // Scroll to menu section or redirect
        document.querySelector('#menu-section')?.scrollIntoView({ behavior: 'smooth' });
    }
}

function appendMessage(sender, text, isLoading = false, buttons = []) {
    const container = document.querySelector('#aiMessages');
    if (!container) return;

    const msgDiv = document.createElement('div');
    const id = 'msg-' + Date.now();
    msgDiv.id = id;
    msgDiv.className = `message ${sender}`;

    let content = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');

    // Add buttons if any
    if (buttons.length > 0) {
        content += '<div class="chat-actions">';
        buttons.forEach(btn => {
            content += `<button class="chat-btn" data-action="${btn.action}" data-value="${btn.value || ''}">${btn.label}</button>`;
        });
        content += '</div>';
    }

    msgDiv.innerHTML = content;
    container.appendChild(msgDiv);
    container.scrollTop = container.scrollHeight;
    return id;
}

function updateLastMessage(text, buttons = []) {
    const container = document.querySelector('#aiMessages');
    const lastMsg = container.lastElementChild;
    if (lastMsg) {
        let content = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');

        if (buttons.length > 0) {
            content += '<div class="chat-actions">';
            buttons.forEach(btn => {
                content += `<button class="chat-btn" data-action="${btn.action}" data-value="${btn.value || ''}">${btn.label}</button>`;
            });
            content += '</div>';
        }

        lastMsg.innerHTML = content;
        container.scrollTop = container.scrollHeight;
    }
}

function updateMessage(id, text) {
    const msgDiv = document.getElementById(id);
    if (msgDiv) {
        msgDiv.innerHTML = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        const container = document.querySelector('#aiMessages');
        if (container) container.scrollTop = container.scrollHeight;
    }
}

