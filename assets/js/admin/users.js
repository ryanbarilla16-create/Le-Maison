import { db, USERS_COL } from './config.js';
import { query, where, onSnapshot, getDocs, getDoc, updateDoc, addDoc, deleteDoc, doc, collection } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

let allUsers = [];
let currentFilter = 'all';

export function initUsers() {
    onSnapshot(USERS_COL, (snapshot) => {
        const tbody = document.getElementById('usersTableBody');
        const countEl = document.getElementById('userCount');
        if (!tbody) return;
        if (snapshot.empty) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:2rem; color:#999;">No users registered yet.</td></tr>'; return; }

        allUsers = snapshot.docs;
        if (countEl) countEl.textContent = `${allUsers.length} users`;

        renderUsers();
    });

    // Select All
    const selectAllCheck = document.getElementById('selectAllUsers');
    if (selectAllCheck) {
        selectAllCheck.addEventListener('change', () => {
            const isChecked = selectAllCheck.checked;
            // Only affect visible checkboxes
            document.querySelectorAll('#usersTableBody .user-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            window.updateBroadcastSelection();
        });
    }

    const broadcastForm = document.getElementById('broadcastForm');
    if (broadcastForm) {
        broadcastForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const subject = document.getElementById('broadcastSubject').value;
            const message = document.getElementById('broadcastMessage').value;
            const selected = document.querySelectorAll('.user-checkbox:checked');
            const userIds = Array.from(selected).map(cb => cb.dataset.id);
            const sendBtn = document.getElementById('sendBroadcastBtn');
            sendBtn.disabled = true;
            try {
                await Promise.all(userIds.map(uid => addDoc(collection(db, 'users', uid, 'notifications'), {
                    title: subject, message, createdAt: new Date(), isRead: false, type: 'broadcast', sender: 'Admin'
                })));
                alert(`Sent to ${userIds.length} users!`);
                window.closeModal('broadcastModal');
                broadcastForm.reset();
            } catch (err) { alert("Broadcast failed"); }
            finally { sendBtn.disabled = false; }
        });
    }

    window.deleteUser = async (id) => { if (confirm("Delete user?")) await deleteDoc(doc(db, 'users', id)); };
    window.viewUserDetails = viewUserDetails;
    window.updateBroadcastSelection = updateBroadcastSelection;
    window.openBroadcastModal = openBroadcastModal;
    window.filterUsersByRole = filterUsersByRole;
    window.openDirectMessage = openDirectMessage;
}

function renderUsers() {
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;

    const filtered = currentFilter === 'all'
        ? allUsers
        : allUsers.filter(docSnap => {
            const role = (docSnap.data().role || 'customer').toLowerCase();
            if (currentFilter === 'admin') return role === 'admin' || role === 'super_admin';
            return role === currentFilter;
        });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:2rem; color:#999;">No ${currentFilter}s found.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(docSnap => {
        const u = docSnap.data();
        const joined = u.createdAt ? new Date(u.createdAt.seconds ? u.createdAt.seconds * 1000 : u.createdAt).toLocaleDateString() : '—';
        const nameChar = (u.firstName || u.email || '?')[0].toUpperCase();
        const fullName = u.fullName || [u.firstName, u.lastName].filter(Boolean).join(' ') || '—';
        const avatarHTML = (u.avatarUrl || u.avatarBase64)
            ? `<img src="${u.avatarUrl || u.avatarBase64}" onclick="window.viewUserDetails('${docSnap.id}')" style="width:40px;height:40px;border-radius:12px;object-fit:cover;cursor:pointer;">`
            : `<div onclick="window.viewUserDetails('${docSnap.id}')" style="width:40px;height:40px;border-radius:12px;background:#D4AF37;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-weight:700;">${nameChar}</div>`;

        // Role badge
        const role = (u.role || 'customer').toUpperCase();
        const roleColors = { 'ADMIN': '#e74c3c', 'SUPER_ADMIN': '#e74c3c', 'CASHIER': '#2ecc71', 'INVENTORY': '#3498db', 'RIDER': '#f39c12', 'CUSTOMER': '#95a5a6' };
        const roleColor = roleColors[role] || '#95a5a6';
        const roleBadge = `<span style="background:${roleColor}; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.7rem; font-weight:600;">${role}</span>`;

        // Account status
        const status = (u.accountStatus || 'APPROVED').toUpperCase();
        const statusColors = { 'APPROVED': '#2ecc71', 'PENDING': '#f39c12', 'PENDING_VERIFICATION': '#f39c12', 'SUSPENDED': '#e74c3c' };
        const statusColor = statusColors[status] || '#95a5a6';
        const statusBadge = `<span style="background:${statusColor}22; color:${statusColor}; padding:3px 10px; border-radius:12px; font-size:0.7rem; font-weight:600; border:1px solid ${statusColor}44;">${status}</span>`;

        return `<tr>
            <td style="text-align:center;"><input type="checkbox" class="user-checkbox" data-id="${docSnap.id}" onchange="window.updateBroadcastSelection()" style="cursor:pointer; width:16px; height:16px; accent-color:var(--primary-gold);"></td>
            <td>
                <div style="display:flex;align-items:center;gap:10px;cursor:pointer;" onclick="window.viewUserDetails('${docSnap.id}')">
                    ${avatarHTML}
                    <div>
                        <div style="font-weight:600;font-size:0.9rem;">${fullName}</div>
                        <div style="font-size:0.75rem;color:#999;">${u.email || '—'}</div>
                    </div>
                </div>
            </td>
            <td>${roleBadge}</td>
            <td>${statusBadge}</td>
            <td>${joined}</td>
            <td style="display:flex; gap:5px; justify-content:center;">
                <button class="btn-icon" title="Message User" onclick="window.openDirectMessage('${docSnap.id}', '${fullName}')" style="color:#6c5ce7;"><i class="fas fa-comment-dots"></i></button>
                <button class="btn-icon" title="Delete User" onclick="window.deleteUser('${docSnap.id}')" style="color:#e74c3c;"><i class="fas fa-trash-alt"></i></button>
            </td>
        </tr>`;
    }).join('');
}

function filterUsersByRole(role) {
    currentFilter = role;
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.role === role);
    });
    renderUsers();
}

function openDirectMessage(userId, userName) {
    // Uncheck all
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
    // Check this user
    const userCb = document.querySelector(`.user-checkbox[data-id="${userId}"]`);
    if (userCb) userCb.checked = true;

    window.updateBroadcastSelection();

    // Update modal title
    const modalTitle = document.querySelector('#broadcastModal .modal-title');
    if (modalTitle) modalTitle.textContent = `Message to ${userName}`;

    window.openModal('broadcastModal');
}

async function viewUserDetails(id) {
    const userDoc = await getDoc(doc(db, 'users', id));
    if (!userDoc.exists()) return;
    const u = userDoc.data();
    document.getElementById('userDetailAvatar').src = u.avatarUrl || u.avatarBase64 || `https://ui-avatars.com/api/?name=${u.firstName || u.email}`;
    document.getElementById('userDetailName').textContent = u.fullName || u.firstName || '—';
    document.getElementById('userDetailEmail').textContent = u.email || '—';
    document.getElementById('userDetailUsername').textContent = u.username || '—';
    document.getElementById('userDetailPhone').textContent = u.phone || '—';
    document.getElementById('userDetailBirthday').textContent = u.birthDate ? `${u.birthDate.month} ${u.birthDate.day}, ${u.birthDate.year}` : '—';
    document.getElementById('userDetailJoined').textContent = u.createdAt ? new Date(u.createdAt.seconds * 1000).toLocaleDateString() : '—';
    document.getElementById('deleteUserModalBtn').onclick = () => { window.closeModal('userDetailsModal'); window.deleteUser(id); };
    window.openModal('userDetailsModal');
}

function updateBroadcastSelection() {
    const selected = document.querySelectorAll('.user-checkbox:checked');
    const btn = document.getElementById('broadcastBtn');
    if (btn) btn.style.opacity = selected.length === 0 ? '0.6' : '1';
    const countText = document.getElementById('broadcastRecipientCount');
    if (countText) countText.textContent = `${selected.length} recipients selected`;
}

function openBroadcastModal() {
    const selected = document.querySelectorAll('.user-checkbox:checked');
    if (selected.length === 0) return alert("Select users first.");

    // Reset title if it was changed by openDirectMessage
    const modalTitle = document.querySelector('#broadcastModal .modal-title');
    if (modalTitle) modalTitle.textContent = "Send Message";

    window.openModal('broadcastModal');
}

export function initCustomers() {
    onSnapshot(USERS_COL, async (snapshot) => {
        const tbody = document.getElementById('customersTableBody');
        if (!tbody) return;

        // Filter only customers (exclude admin, staff roles)
        const customers = snapshot.docs.filter(d => {
            const role = (d.data().role || 'customer').toLowerCase();
            return role === 'customer';
        });

        if (customers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 2rem; color: #999;">No customers found.</td></tr>';
            return;
        }

        // Get all orders to count per customer
        let allOrders = [];
        try {
            const ordersSnap = await getDocs(collection(db, 'orders'));
            allOrders = ordersSnap.docs.map(d => ({ ...d.data(), id: d.id }));
        } catch (e) {
            console.error("Error fetching orders for customers:", e);
        }

        tbody.innerHTML = customers.map(docSnap => {
            const u = docSnap.data();
            const name = u.fullName || [u.firstName, u.lastName].filter(Boolean).join(' ') || '—';
            const email = u.email || '—';
            const phone = u.phone || '—';

            // Count orders for this customer
            const customerOrders = allOrders.filter(o => o.customerId === docSnap.id);
            const totalOrders = customerOrders.length;

            // Find last order date
            let lastOrder = '—';
            if (customerOrders.length > 0) {
                const sorted = customerOrders.sort((a, b) => {
                    const aTime = a.createdAt?.seconds || 0;
                    const bTime = b.createdAt?.seconds || 0;
                    return bTime - aTime;
                });
                const lastDate = sorted[0].createdAt;
                if (lastDate) {
                    lastOrder = new Date(lastDate.seconds ? lastDate.seconds * 1000 : lastDate).toLocaleDateString();
                }
            }

            // Avatar
            const nameChar = (u.firstName || u.email || '?')[0].toUpperCase();
            const avatarHTML = (u.avatarUrl || u.avatarBase64)
                ? `<img src="${u.avatarUrl || u.avatarBase64}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">`
                : `<div style="width:36px;height:36px;border-radius:50%;background:#D4AF37;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;">${nameChar}</div>`;

            return `<tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        ${avatarHTML}
                        <span>${name}</span>
                    </div>
                </td>
                <td>${email}</td>
                <td>${phone}</td>
                <td style="text-align:center;"><strong>${totalOrders}</strong></td>
                <td>${lastOrder}</td>
                <td><button class="btn-icon" onclick="window.viewUserDetails('${docSnap.id}')"><i class="fas fa-eye"></i></button></td>
            </tr>`;
        }).join('');
    });
}

export function initApprovals() {
    const q = query(USERS_COL, where('accountStatus', 'in', ['PENDING', 'PENDING_VERIFICATION']));
    onSnapshot(q, (snapshot) => {
        const tbody = document.getElementById('approvalsTable');
        const badge = document.getElementById('sidebarApprovalCount');
        if (badge) { badge.textContent = snapshot.size; badge.style.display = snapshot.size > 0 ? 'inline-block' : 'none'; }
        if (!tbody) return;
        if (snapshot.empty) { tbody.innerHTML = '<tr><td colspan="4">No pending approvals.</td></tr>'; return; }
        tbody.innerHTML = snapshot.docs.map(docSnap => {
            const u = docSnap.data();
            const date = u.createdAt ? new Date(u.createdAt.seconds * 1000).toLocaleDateString() : '—';
            return `<tr><td>${u.fullName || 'New User'}</td><td>${u.email}</td><td>${date}</td><td><button onclick="window.approveAccount('${docSnap.id}', '${u.email}', '${(u.fullName || '').replace(/'/g, "\\'")}')">Approve</button></td></tr>`;
        }).join('');
    });
    window.approveAccount = approveAccount;
}

async function approveAccount(id, email, name) {
    if (confirm(`Approve ${email}?`)) {
        await updateDoc(doc(db, 'users', id), { accountStatus: 'APPROVED', approvedAt: new Date() });
        alert("Approved!");
    }
}
