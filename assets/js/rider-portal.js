// Rider Portal Logic - Real-time Location & Status
import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-app.js";
import {
    getFirestore, collection, query, where, onSnapshot, doc, updateDoc, getDocs, orderBy
} from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";
import {
    getAuth, signInWithEmailAndPassword, onAuthStateChanged
} from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
import { firebaseConfig } from "./firebase-config.js";

// Initialize Firebase
const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);

// State
let riderId = null;
let riderName = "";
let watchId = null;
let currentDeliveries = [];

// DOM Elements
const loginOverlay = document.getElementById('riderLogin');
const loginBtn = document.getElementById('loginBtn');
const onlineSwitch = document.getElementById('onlineSwitch');
const statusLabel = document.getElementById('statusLabel');
const locationStatus = document.getElementById('locationStatus');
const deliveryList = document.getElementById('deliveryList');
const taskCount = document.getElementById('taskCount');

// --- 1. Authentication ---
loginBtn.addEventListener('click', async () => {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorEl = document.getElementById('loginError');

    loginBtn.disabled = true;
    loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';

    try {
        await signInWithEmailAndPassword(auth, email, password);
        errorEl.style.display = 'none';
    } catch (error) {
        console.error("Login failed:", error);
        errorEl.style.display = 'block';
        loginBtn.disabled = false;
        loginBtn.innerHTML = 'Enter Portal <i class="fas fa-chevron-right"></i>';
    }
});

onAuthStateChanged(auth, (user) => {
    if (user) {
        riderId = user.uid;
        riderName = user.displayName || user.email.split('@')[0];
        loginOverlay.classList.add('hidden');
        initDashboard();
    } else {
        loginOverlay.classList.remove('hidden');
        stopTracking();
    }
});

// --- 2. Dashboard Logic ---
function initDashboard() {
    // Listen for deliveries assigned to this rider
    const q = query(
        collection(db, 'deliveries'),
        where('riderId', '==', riderId),
        where('status', 'not-in', ['Delivered', 'Cancelled'])
    );

    onSnapshot(q, (snapshot) => {
        currentDeliveries = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
        renderDeliveries();
    });
}

function renderDeliveries() {
    taskCount.textContent = currentDeliveries.length;

    if (currentDeliveries.length === 0) {
        deliveryList.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-motorcycle"></i>
                <p>No active deliveries assigned to you.</p>
            </div>
        `;
        return;
    }

    deliveryList.innerHTML = currentDeliveries.map(d => `
        <div class="order-card">
            <div class="order-header">
                <span class="order-id">#${d.orderId ? d.orderId.slice(0, 8) : 'ORD-TEMP'}</span>
                <span class="order-time">${d.status}</span>
            </div>
            <div class="customer-info">
                <span class="customer-name">${d.customerName || 'Customer'}</span>
                <div class="customer-address">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${d.address || 'Address not specified'}</span>
                </div>
            </div>
            <div class="btn-group">
                <button class="btn btn-outline" onclick="window.viewMap('${d.address}')">
                    <i class="fas fa-directions"></i> Guide
                </button>
                <button class="btn btn-gold" onclick="window.updateStatus('${d.id}', 'In Transit')">
                    <i class="fas fa-box"></i> Start
                </button>
                <button class="btn btn-delivered" onclick="window.updateStatus('${d.id}', 'Delivered')">
                    <i class="fas fa-check-circle"></i> Mark as Delivered
                </button>
            </div>
        </div>
    `).join('');
}

// Global Actions for Window
window.updateStatus = async (id, status) => {
    try {
        await updateDoc(doc(db, 'deliveries', id), {
            status: status,
            updatedAt: new Date()
        });

        // Also update corresponding Order status
        const delSnap = await getDocs(query(collection(db, 'deliveries'), where('id', '==', id)));
        if (!delSnap.empty) {
            const orderId = delSnap.docs[0].data().orderId;
            if (orderId) {
                await updateDoc(doc(db, 'orders', orderId), {
                    status: status.toLowerCase() === 'delivered' ? 'delivered' : 'out for delivery'
                });
            }
        }
    } catch (err) {
        alert("Error updating status: " + err.message);
    }
};

window.viewMap = (address) => {
    window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`, '_blank');
};

// --- 3. Geolocation Tracking ---
onlineSwitch.addEventListener('change', () => {
    if (onlineSwitch.checked) {
        startTracking();
    } else {
        stopTracking();
    }
});

function startTracking() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        onlineSwitch.checked = false;
        return;
    }

    statusLabel.style.color = 'var(--success)';
    statusLabel.textContent = 'ONLINE';
    locationStatus.textContent = 'Seeking GPS signal...';

    watchId = navigator.geolocation.watchPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            updateRiderLocation(latitude, longitude);
            locationStatus.textContent = `Tracking Active (${latitude.toFixed(4)}, ${longitude.toFixed(4)})`;
        },
        (error) => {
            console.error("GPS Error:", error);
            locationStatus.textContent = "GPS Error: " + error.message;
            if (error.code === 1) {
                alert("Please enable location permissions to use Live Tracking.");
                onlineSwitch.checked = false;
                stopTracking();
            }
        },
        { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
    );
}

function stopTracking() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
    statusLabel.style.color = '#666';
    statusLabel.textContent = 'OFFLINE';
    locationStatus.textContent = 'GPS Tracking is currently disabled.';
}

async function updateRiderLocation(lat, lng) {
    if (!riderId) return;

    try {
        // Update all active deliveries for this rider with the new location
        // In a real system, you might have a 'riders' collection too. 
        // Here we update the 'currentLocation' field in the 'deliveries' collection items.

        currentDeliveries.forEach(async (d) => {
            await updateDoc(doc(db, 'deliveries', d.id), {
                currentLocation: { lat, lng },
                updatedAt: new Date()
            });
        });

        // (Optionally) Store globally in a riders collection if you had one
        await setDoc(doc(db, 'riders', riderId), {
            name: riderName,
            currentLocation: { lat, lng },
            lastActive: new Date(),
            isOnline: true
        }, { merge: true });

    } catch (err) {
        console.error("Location upload failed:", err);
    }
}
