<!-- Chatbot UI -->
<div class="chatbot-launcher" id="aiLauncher">
    <i class="fas fa-magic"></i>
</div>

<div class="chatbot-window" id="aiWindow">
    <div class="chatbot-header">
        <div>
            <h4 style="margin: 0; font-family: 'Playfair Display';">AI Assistant</h4>
            <small style="opacity: 0.7;">Powered by Vertex AI</small>
        </div>
        <i class="fas fa-times" id="closeAI" style="cursor: pointer;"></i>
    </div>
    <div class="chatbot-messages" id="aiMessages">
        <div class="message bot">Hello! 👋 I am the AI Concierge of **Le Maison**. How can I assist your culinary journey today?</div>
    </div>
    <div class="chatbot-input">
        <input type="text" id="aiInput" placeholder="Write your message...">
        <button id="sendAI" style="background:var(--dark-brown); color:var(--primary-gold); border:none; padding:10px; border-radius:5px; cursor:pointer;">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>
