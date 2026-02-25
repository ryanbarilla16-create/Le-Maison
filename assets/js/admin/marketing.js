import { db, REVIEWS_COL, PROMOTIONS_COL } from './config.js';
import { state } from './state.js';
import { onSnapshot, updateDoc, deleteDoc, doc, addDoc, getDocs } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initReviews() {
    onSnapshot(REVIEWS_COL, (snap) => {
        const tbody = document.getElementById('reviewsTable');
        if (!tbody) return;
        if (snap.empty) { tbody.innerHTML = '<tr><td colspan="6">No reviews yet.</td></tr>'; return; }
        tbody.innerHTML = snap.docs.map(docSnap => {
            const r = docSnap.data();
            const stars = '★'.repeat(r.rating || 0) + '☆'.repeat(5 - (r.rating || 0));
            const status = r.status || 'PENDING';
            const approveBtn = status === 'PENDING' ? `<button class="btn-icon" onclick="window.approveReview('${docSnap.id}')"><i class="fas fa-check"></i></button>` : '';
            return `<tr><td>${r.customerName || '—'}</td><td><span style="color:#E8A838;">${stars}</span></td><td>${r.comment || '—'}</td><td>${r.createdAt?.toDate ? r.createdAt.toDate().toLocaleDateString() : '—'}</td><td><span class="status-badge status-${status.toLowerCase()}">${status}</span></td><td>${approveBtn}<button class="btn-icon" onclick="window.deleteReview('${docSnap.id}')"><i class="fas fa-trash"></i></button></td></tr>`;
        }).join('');
    });
    window.approveReview = async (id) => { if (confirm('Approve?')) await updateDoc(doc(db, 'reviews', id), { status: 'APPROVED', updatedAt: new Date() }); };
    window.deleteReview = async (id) => { if (confirm('Delete?')) await deleteDoc(doc(db, 'reviews', id)); };
}

export function initPromotions() {
    onSnapshot(PROMOTIONS_COL, (snap) => {
        const tbody = document.getElementById('promotionsTable');
        if (!tbody) return;
        if (snap.empty) { tbody.innerHTML = '<tr><td colspan="7">No active promotions.</td></tr>'; return; }
        tbody.innerHTML = snap.docs.map(docSnap => {
            const p = docSnap.data();
            const isExpired = p.validUntil && new Date(p.validUntil) < new Date();
            const statusBadge = `<span class="status-badge status-${isExpired ? 'preparing' : 'delivered'}">${isExpired ? 'Expired' : 'Active'}</span>`;
            return `<tr><td>${p.code || '—'}</td><td>${p.type === 'Percentage' ? p.discount + '%' : '₱' + p.discount}</td><td>${p.type}</td><td>${p.validUntil || '—'}</td><td>${p.usedCount || 0}/${p.maxUses || '∞'}</td><td>${statusBadge}</td><td><button class="btn-icon" onclick="window.editPromo('${docSnap.id}')"><i class="fas fa-edit"></i></button><button class="btn-icon" onclick="window.deletePromo('${docSnap.id}')"><i class="fas fa-trash"></i></button></td></tr>`;
        }).join('');
    });

    const form = document.getElementById('promoForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                code: document.getElementById('promoCode').value.toUpperCase(),
                discount: parseFloat(document.getElementById('promoDiscount').value),
                type: document.getElementById('promoType').value,
                validUntil: document.getElementById('promoExpiry').value,
                maxUses: parseInt(document.getElementById('promoMaxUses').value) || 0,
                updatedAt: new Date()
            };
            try {
                if (state.currentPromoEditId) {
                    await updateDoc(doc(db, 'promotions', state.currentPromoEditId), data);
                    state.currentPromoEditId = null;
                } else {
                    data.createdAt = new Date(); data.usedCount = 0;
                    await addDoc(PROMOTIONS_COL, data);
                }
                window.closeModal('promoModal');
                form.reset();
            } catch (err) { alert('Error: ' + err.message); }
        });
    }

    window.editPromo = async (id) => {
        const snap = await getDocs(PROMOTIONS_COL);
        const p = snap.docs.find(d => d.id === id)?.data();
        if (!p) return;
        document.getElementById('promoCode').value = p.code || '';
        document.getElementById('promoDiscount').value = p.discount || 0;
        document.getElementById('promoType').value = p.type || 'Percentage';
        document.getElementById('promoExpiry').value = p.validUntil || '';
        document.getElementById('promoMaxUses').value = p.maxUses || 0;
        state.currentPromoEditId = id;
        document.getElementById('promoModalTitle').textContent = 'Edit Promotion';
        window.openModal('promoModal');
    };
    window.deletePromo = async (id) => { if (confirm('Delete promo?')) await deleteDoc(doc(db, 'promotions', id)); };
    window.openPromoModal = () => { state.currentPromoEditId = null; document.getElementById('promoModalTitle').textContent = 'Create Promotion'; if (document.getElementById('promoForm')) document.getElementById('promoForm').reset(); window.openModal('promoModal'); };
}
