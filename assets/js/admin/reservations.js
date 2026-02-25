import { db, RESERVATIONS_COL } from './config.js';
import { onSnapshot, updateDoc, deleteDoc, doc, addDoc } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initReservations() {
    onSnapshot(RESERVATIONS_COL, (snap) => {
        const tbody = document.getElementById('reservationsTableBody');
        if (!tbody) return;
        if (snap.empty) { tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:2rem; color:#999;">No reservations yet.</td></tr>'; return; }
        tbody.innerHTML = snap.docs.map(d => {
            const r = d.data();
            const statusClass = { 'Pending': 'pending', 'Confirmed': 'delivered', 'Cancelled': 'cancelled', 'Completed': 'ready' }[r.status] || 'pending';
            const dateTime = `${r.date || '—'} <br><small style="color:#999;">${r.time || ''}</small>`;
            const approveBtn = r.status === 'Pending' ? `<button class="btn-icon" onclick="window.openTableAssign('${d.id}', '${(r.guestName || '').replace(/'/g, "\\\\'")}')" title="Approve"><i class="fas fa-check-circle" style="color:#2ecc71;"></i></button>` : '';
            const occasionBadge = r.occasion ? `<span style="background:var(--secondary-gold); color:var(--dark-brown); padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">${r.occasion}</span>` : '<span style="color:#aaa;">—</span>';
            const typeBadge = r.bookingType === 'Exclusive Venue'
                ? `<span style="background:linear-gradient(135deg, #2C1810, #4a2c1f); color:#FFD700; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;"><i class="fas fa-crown" style="margin-right:4px;"></i>Exclusive</span>`
                : `<span style="background:#e8f5e9; color:#2e7d32; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;">Regular</span>`;
            return `<tr>
                <td><strong>${r.guestName || '—'}</strong></td>
                <td style="text-align:center;">${typeBadge}</td>
                <td>${dateTime}</td>
                <td style="text-align:center;">${r.guests || '—'}</td>
                <td style="text-align:center;">${occasionBadge}</td>
                <td style="text-align:center;">${r.table ? '<strong>T' + r.table + '</strong>' : '<span style="color:#aaa;">—</span>'}</td>
                <td><span class="status-badge status-${statusClass}">${(r.status || 'Pending').toUpperCase()}</span></td>
                <td>${approveBtn}<button class="btn-icon" onclick="window.deleteReservation('${d.id}')" title="Delete"><i class="fas fa-trash" style="color:#C62828;"></i></button></td>
            </tr>`;
        }).join('');
    });

    // Admin Manuel Reservation Submission
    const resForm = document.getElementById('reservationForm');
    if (resForm) {
        resForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = resForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const data = {
                    guestName: document.getElementById('resGuestName').value,
                    date: document.getElementById('resDate').value,
                    time: document.getElementById('resTime').value,
                    guests: parseInt(document.getElementById('resGuests').value),
                    occasion: document.getElementById('resOccasion').value,
                    bookingType: document.getElementById('resBookingType')?.value || 'Regular Table',
                    table: document.getElementById('resTable').value || null,
                    status: document.getElementById('resStatus').value,
                    createdAt: new Date()
                };
                await addDoc(RESERVATIONS_COL, data);
                alert('Reservation saved successfully.');
                resForm.reset();
                window.closeModal('reservationModal');
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Save Reservation';
            }
        });
    }

    const assignForm = document.getElementById('tableAssignForm');
    if (assignForm) {
        assignForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('assignTableBtn');
            btn.disabled = true;
            try {
                const resId = document.getElementById('assignResId').value;
                const tableNumber = document.getElementById('assignTableNumber').value;
                if (!tableNumber) return alert('Select a table.');
                await updateDoc(doc(db, 'reservations', resId), { status: 'Confirmed', table: tableNumber, confirmedAt: new Date() });
                window.closeModal('tableAssignModal');
            } catch (err) { alert('Error: ' + err.message); }
            finally { btn.disabled = false; }
        });
    }

    window.openTableAssign = (resId, guestName) => {
        document.getElementById('assignResId').value = resId;
        document.getElementById('assignGuestName').value = guestName;
        document.getElementById('assignTableNumber').value = '';
        if (window.renderTableMap) window.renderTableMap('assignTableMap', 'assignTableNumber', new Date().toISOString().split('T')[0]);
        window.openModal('tableAssignModal');
    };
    window.deleteReservation = async (id) => { if (confirm("Delete?")) await deleteDoc(doc(db, 'reservations', id)); };
    window.openReservationModal = openReservationModal;
}

function openReservationModal() {
    document.getElementById('resDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('resTable').value = '';
    if (window.renderTableMap) window.renderTableMap('resTableMap', 'resTable', document.getElementById('resDate').value);
    window.openModal('reservationModal');
}

export function renderTableMap(containerId, inputId, dateStr) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Improved Grid Styling
    container.style.display = 'grid';
    container.style.gridTemplateColumns = 'repeat(4, 1fr)';
    container.style.gap = '12px';
    container.style.marginTop = '10px';

    const tables = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    container.innerHTML = tables.map(n => `
        <div class="table-box" data-table="${n}" 
             onclick="window.selectTable('${containerId}', '${inputId}', '${n}', this)" 
             style="padding: 15px 10px; border: 2px solid var(--border-color); border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fff; font-weight: 600; color: var(--dark-brown);">
             <i class="fas fa-chair" style="display:block; margin-bottom:5px; font-size:1.1rem; color: #ccc;"></i>
             T${n}
        </div>
    `).join('');

    window.selectTable = (cId, iId, num, el) => {
        const parent = document.getElementById(cId);
        parent.querySelectorAll('.table-box').forEach(b => {
            b.style.borderColor = 'var(--border-color)';
            b.style.background = '#fff';
            b.querySelector('i').style.color = '#ccc';
        });
        el.style.borderColor = 'var(--primary-gold)';
        el.style.background = 'var(--gold-glow)';
        el.querySelector('i').style.color = 'var(--primary-gold)';
        document.getElementById(iId).value = num;
    };
}
