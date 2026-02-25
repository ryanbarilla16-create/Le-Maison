import { db, RIDERS_COL, DELIVERIES_COL } from './config.js';
import { state } from './state.js';
import { onSnapshot, updateDoc, doc, getDocs, query, where, collection } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initDeliveries() {
    if (document.getElementById('deliveryMap') && !state.deliveryMap) {
        state.deliveryMap = L.map('deliveryMap').setView([13.9419, 121.1644], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(state.deliveryMap);
    }

    onSnapshot(RIDERS_COL, (snap) => {
        snap.docs.forEach(docSnap => {
            const data = docSnap.data();
            const riderId = docSnap.id;
            if (data.location?.lat && data.location?.lng) {
                const lastUpdated = data.lastUpdated?.toDate ? data.lastUpdated.toDate() : (data.lastUpdated ? new Date(data.lastUpdated) : new Date(0));
                const stale = lastUpdated < new Date(Date.now() - 5 * 60 * 1000);
                if (data.isOnline && !stale) updateRiderMarker(riderId, data.name || 'Rider', data.location);
                else {
                    if (state.activeRiderMarkers[riderId]) {
                        state.deliveryMap.removeLayer(state.activeRiderMarkers[riderId]);
                        delete state.activeRiderMarkers[riderId];
                    }
                }
            }
        });
    });

    onSnapshot(DELIVERIES_COL, (snap) => {
        const tbody = document.getElementById('deliveryTable');
        if (!tbody) return;
        if (snap.empty) { tbody.innerHTML = '<tr><td colspan="7">No active deliveries.</td></tr>'; return; }
        tbody.innerHTML = snap.docs.map(docSnap => {
            const d = docSnap.data();
            if (d.riderId && d.currentLocation && d.status === 'In Transit') updateRiderMarker(d.riderId, d.riderName || 'Rider', d.currentLocation);
            const statusClass = { 'In Transit': 'status-pending', 'Picked Up': 'status-preparing', 'Delivered': 'status-delivered' }[d.status] || 'status-pending';
            return `<tr><td>#${d.orderId?.slice(0, 6) || 'ORD'}</td><td>${d.riderName || '-'}</td><td>${d.customerName || '-'}</td><td>${d.address || '-'}</td><td>${d.eta || '-'}</td><td><span class="status-badge ${statusClass}">${d.status}</span></td><td><select onchange="window.updateDeliveryStatus('${docSnap.id}', this.value)"><option value="" disabled selected>Update</option><option value="Picked Up">Picked Up</option><option value="In Transit">In Transit</option><option value="Delivered">Delivered</option></select></td></tr>`;
        }).join('');
    });

    window.updateDeliveryStatus = async function (id, status) {
        await updateDoc(doc(db, 'deliveries', id), { status, updatedAt: new Date() });
    };
    window.updateRiderMarker = updateRiderMarker;
}

function updateRiderMarker(id, name, loc) {
    if (!state.deliveryMap) return;
    if (state.activeRiderMarkers[id]) {
        state.activeRiderMarkers[id].setLatLng([loc.lat, loc.lng]);
    } else {
        const icon = L.divIcon({ className: 'rider-marker', html: `<div style="background:var(--primary-gold); width:30px; height:30px; border-radius:50%; border:2px solid white; display:flex; align-items:center; justify-content:center; color:white;"><i class="fas fa-motorcycle"></i></div>` });
        state.activeRiderMarkers[id] = L.marker([loc.lat, loc.lng], { icon }).addTo(state.deliveryMap).bindPopup(name);
    }
}
