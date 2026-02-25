import { auth, db } from './config.js';
import { onAuthStateChanged, reauthenticateWithCredential, EmailAuthProvider, updateProfile, updatePassword } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-auth.js";
import { getDoc, updateDoc, setDoc, doc } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initSettings() {
    const SETTINGS_DOC = doc(db, 'settings', 'general');
    getDoc(SETTINGS_DOC).then(docSnap => {
        if (docSnap.exists()) {
            const s = docSnap.data();
            if (s.name) document.getElementById('settingName').value = s.name;
            if (s.address) document.getElementById('settingAddress').value = s.address;
            if (s.phone) document.getElementById('settingPhone').value = s.phone;
            if (s.email) document.getElementById('settingEmail').value = s.email;
            if (s.facebook) document.getElementById('settingFacebook').value = s.facebook;
            if (s.instagram) document.getElementById('settingInstagram').value = s.instagram;
            if (s.openTime) document.getElementById('settingOpen').value = s.openTime;
            if (s.closeTime) document.getElementById('settingClose').value = s.closeTime;
            if (s.deliveryFee) document.getElementById('settingDeliveryFee').value = s.deliveryFee;
        }
    });

    document.querySelectorAll('.settings-card .btn-action').forEach(btn => {
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            try {
                await setDoc(SETTINGS_DOC, {
                    name: document.getElementById('settingName').value,
                    address: document.getElementById('settingAddress').value,
                    phone: document.getElementById('settingPhone').value,
                    email: document.getElementById('settingEmail').value || '',
                    facebook: document.getElementById('settingFacebook').value || '',
                    instagram: document.getElementById('settingInstagram').value || '',
                    openTime: document.getElementById('settingOpen').value,
                    closeTime: document.getElementById('settingClose').value,
                    deliveryFee: parseFloat(document.getElementById('settingDeliveryFee').value) || 50,
                    updatedAt: new Date()
                }, { merge: true });
                btn.innerHTML = '<i class="fas fa-check"></i> Saved!';
                setTimeout(() => { btn.innerHTML = '<i class="fas fa-save"></i> Save'; btn.disabled = false; }, 2000);
            } catch (err) { alert('Save failed'); btn.disabled = false; }
        });
    });
}

export async function initAdminProfile() {
    onAuthStateChanged(auth, (user) => { if (user) setupProfileListeners(user); });
    window.openAdminProfileModal = openAdminProfileModal;

    const form = document.getElementById('adminProfileForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('saveProfileBtn');
            btn.disabled = true;
            try {
                const u = auth.currentUser;
                const fName = document.getElementById('editProfileFirstName').value.trim();
                const lName = document.getElementById('editProfileLastName').value.trim();
                const fullName = `${fName} ${lName}`.trim();
                const updates = { firstName: fName, lastName: lName, fullName, username: document.getElementById('editProfileUsername').value, phone: document.getElementById('editProfilePhone').value, avatarUrl: document.getElementById('editProfileAvatarUrl').value, updatedAt: new Date() };
                await updateDoc(doc(db, "users", u.uid), updates);
                await updateProfile(u, { displayName: fullName, photoURL: updates.avatarUrl });
                alert("Profile Saved!");
                window.closeModal('adminProfileModal');
            } catch (err) { alert("Save failed: " + err.message); }
            finally { btn.disabled = false; }
        });
    }
}

async function setupProfileListeners(user) {
    const snap = await getDoc(doc(db, "users", user.uid));
    if (snap.exists()) {
        const d = snap.data();
        if (document.getElementById('sidebarAdminName')) document.getElementById('sidebarAdminName').textContent = d.fullName || user.displayName || 'Admin';
        if (document.getElementById('sidebarAdminAvatar')) document.getElementById('sidebarAdminAvatar').src = d.avatarUrl || user.photoURL || `https://ui-avatars.com/api/?name=${encodeURIComponent(d.fullName || 'Admin')}`;
    }
}

async function openAdminProfileModal() {
    const u = auth.currentUser;
    if (!u) return;
    const snap = await getDoc(doc(db, "users", u.uid));
    if (snap.exists()) {
        const d = snap.data();
        document.getElementById('editProfileFirstName').value = d.firstName || '';
        document.getElementById('editProfileLastName').value = d.lastName || '';
        document.getElementById('editProfileUsername').value = d.username || '';
        document.getElementById('editProfileEmail').value = u.email;
        document.getElementById('editProfilePhone').value = d.phone || '';
        document.getElementById('editProfileAvatarUrl').value = d.avatarUrl || '';
        document.getElementById('editProfileAvatar').src = d.avatarUrl || `https://ui-avatars.com/api/?name=${encodeURIComponent(d.fullName || 'Admin')}`;
    }
    window.openModal('adminProfileModal');
}
