import { db, INVENTORY_COL } from './config.js';
import { query, orderBy, onSnapshot, updateDoc, addDoc, deleteDoc, doc } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

export function initInventory() {
    let cachedSnapshot = null;

    const renderInventory = () => {
        if (!cachedSnapshot) return;
        const addItemBtn = document.getElementById('invAddItemBtn');
        if (addItemBtn) {
            addItemBtn.style.display = (String(window.userRole).toLowerCase() === 'inventory') ? 'inline-block' : 'none';
        }

        window.inventoryData = window.inventoryData || {};
        const tbody = document.getElementById('inventoryTableBody');
        if (!tbody) return;

        let totalItems = 0, lowStock = 0, outOfStock = 0;
        if (cachedSnapshot.empty) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No inventory items found.</td></tr>';
            updateInventoryStats(0, 0, 0);
            return;
        }

        tbody.innerHTML = cachedSnapshot.docs.map(docSnap => {
            const item = docSnap.data();
            window.inventoryData[docSnap.id] = item;
            totalItems++;

            const quantity = parseFloat(item.quantity) || 0;
            const minLevel = parseFloat(item.minLevel) || 10;
            let statusBadge = '';

            if (quantity <= 0) { outOfStock++; statusBadge = '<span class="status-badge status-cancelled">OUT OF STOCK</span>'; }
            else if (quantity <= minLevel) { lowStock++; statusBadge = '<span class="status-badge status-pending">LOW STOCK</span>'; }
            else { statusBadge = '<span class="status-badge status-ready">IN STOCK</span>'; }

            const lastUpdated = item.lastUpdated ? (item.lastUpdated.toDate ? item.lastUpdated.toDate() : new Date(item.lastUpdated)).toLocaleDateString() : '-';
            const actionsHtml = (String(window.userRole).toLowerCase() === 'inventory')
                ? `<button class="btn-icon" onclick="window.openManageStockModal('${docSnap.id}')"><i class="fas fa-boxes"></i></button>
                   <button class="btn-icon" onclick="window.editInventoryItem('${docSnap.id}')"><i class="fas fa-pen"></i></button>
                   <button class="btn-icon" onclick="window.deleteInventoryItem('${docSnap.id}', '${item.name}')"><i class="fas fa-trash-alt"></i></button>`
                : '<span style="color:#aaa;">View Only</span>';

            return `<tr>
                <td style="font-weight:700;">${item.name}</td>
                <td><span class="badge">${item.category || 'General'}</span></td>
                <td><span style="font-weight:800; ${quantity <= minLevel ? 'color:#D32F2F;' : 'color:#2E7D32;'}">${quantity} <small>${item.unit || 'units'}</small></span></td>
                <td>${item.supplier || '-'}</td>
                <td>${item.expiry || '-'}</td>
                <td>${statusBadge}</td>
                <td>${lastUpdated}</td>
                <td><div style="display:flex; gap:8px;">${actionsHtml}</div></td>
            </tr>`;
        }).join('');

        updateInventoryStats(totalItems, lowStock, outOfStock);
    };

    onSnapshot(query(INVENTORY_COL, orderBy('name', 'asc')), (snap) => {
        cachedSnapshot = snap;
        renderInventory();
    });

    window.addEventListener('authReady', renderInventory);

    const invForm = document.getElementById('inventoryForm');
    if (invForm) {
        invForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('saveInvBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            try {
                const id = document.getElementById('invItemId').value;
                const data = {
                    name: document.getElementById('invName').value.trim(),
                    category: document.getElementById('invCategory').value,
                    quantity: parseFloat(document.getElementById('invQuantity').value),
                    unit: document.getElementById('invUnit').value.trim(),
                    minLevel: parseFloat(document.getElementById('invMinLevel').value),
                    supplier: document.getElementById('invSupplier').value.trim(),
                    expiry: document.getElementById('invExpiry').value,
                    lastUpdated: new Date()
                };
                if (id) await updateDoc(doc(db, 'inventory', id), data);
                else await addDoc(INVENTORY_COL, data);
                window.closeModal('inventoryModal');
                alert(id ? 'Updated!' : 'Added!');
            } catch (err) { alert("Error: " + err.message); }
            finally { btn.disabled = false; btn.innerHTML = originalText; }
        });
    }

    const manageForm = document.getElementById('manageStockForm');
    if (manageForm) {
        manageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('saveManageStockBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            try {
                const id = document.getElementById('manageStockId').value;
                const action = document.getElementById('manageStockAction').value;
                const qtyAdjust = parseFloat(document.getElementById('manageStockQty').value);
                const remarks = document.getElementById('manageStockRemarks').value.trim();
                const item = window.inventoryData[id];
                let newQty = parseFloat(item.quantity);
                if (action === 'Stock In') newQty += qtyAdjust;
                else {
                    if (qtyAdjust > newQty) { alert("Insufficient stock!"); return; }
                    newQty -= qtyAdjust;
                }
                const logEntry = { action, amount: action === 'Stock In' ? qtyAdjust : -qtyAdjust, remarks, date: new Date(), userRole: window.userRole || 'Admin' };
                const history = item.history || [];
                history.push(logEntry);
                await updateDoc(doc(db, 'inventory', id), { quantity: newQty, lastUpdated: new Date(), history });
                window.closeModal('manageStockModal');
                alert('Stock updated!');
            } catch (err) { alert("Error: " + err.message); }
            finally { btn.disabled = false; btn.innerHTML = originalText; }
        });
    }

    const searchInput = document.getElementById('inventorySearch');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('#inventoryTableBody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    // Expose helpers
    window.openInventoryModal = openInventoryModal;
    window.editInventoryItem = editInventoryItem;
    window.deleteInventoryItem = deleteInventoryItem;
    window.openManageStockModal = openManageStockModal;
    window.toggleSpoilageReason = toggleSpoilageReason;
    window.printInventorySheet = printInventorySheet;
}

function updateInventoryStats(total, low, out) {
    if (document.getElementById('invTotalItems')) document.getElementById('invTotalItems').textContent = total;
    if (document.getElementById('invLowStock')) document.getElementById('invLowStock').textContent = low;
    if (document.getElementById('invOutOfStock')) document.getElementById('invOutOfStock').textContent = out;
}

function openInventoryModal() {
    document.getElementById('invModalTitle').textContent = 'Add Inventory Item';
    const form = document.getElementById('inventoryForm');
    if (form) form.reset();
    document.getElementById('invItemId').value = '';
    window.openModal('inventoryModal');
}

function editInventoryItem(id) {
    const item = window.inventoryData[id];
    if (item) {
        document.getElementById('invModalTitle').textContent = 'Edit Inventory Item';
        document.getElementById('invItemId').value = id;
        document.getElementById('invName').value = item.name;
        document.getElementById('invCategory').value = item.category;
        document.getElementById('invQuantity').value = item.quantity;
        document.getElementById('invUnit').value = item.unit;
        document.getElementById('invMinLevel').value = item.minLevel;
        document.getElementById('invSupplier').value = item.supplier || '';
        document.getElementById('invExpiry').value = item.expiry || '';
        window.openModal('inventoryModal');
    }
}

async function deleteInventoryItem(id, name) {
    if (confirm(`Delete "${name}"?`)) {
        try { await deleteDoc(doc(db, 'inventory', id)); }
        catch (e) { alert("Delete failed"); }
    }
}

function openManageStockModal(id) {
    const item = window.inventoryData[id];
    if (!item) return;
    document.getElementById('manageStockId').value = id;
    document.getElementById('manageStockTitle').textContent = item.name;
    document.getElementById('manageStockCurrentDisplay').textContent = `${item.quantity} ${item.unit || 'units'}`;
    const form = document.getElementById('manageStockForm');
    if (form) form.reset();
    document.getElementById('spoilageReasonGroup').style.display = 'none';
    window.openModal('manageStockModal');
}

function toggleSpoilageReason() {
    const action = document.getElementById('manageStockAction').value;
    document.getElementById('spoilageReasonGroup').style.display = action === 'Spoilage' ? 'block' : 'none';
}

function printInventorySheet() {
    const printWindow = window.open('', '_blank');
    let tableHtml = `<table border="1" style="width:100%; border-collapse: collapse;"><thead><tr><th>Item Name</th><th>Category</th><th>Current Stock</th><th>Supplier</th><th>Physical Count</th><th>Discrepancy</th></tr></thead><tbody>`;
    Object.values(window.inventoryData || {}).sort((a, b) => a.name.localeCompare(b.name)).forEach(item => {
        tableHtml += `<tr><td>${item.name}</td><td>${item.category || 'General'}</td><td>${item.quantity} ${item.unit || ''}</td><td>${item.supplier || '-'}</td><td></td><td></td></tr>`;
    });
    tableHtml += `</tbody></table>`;
    printWindow.document.write(`<html><body><h2>Inventory Check Sheet</h2><p>Date: ${new Date().toLocaleDateString()}</p>${tableHtml}</body></html>`);
    printWindow.document.close();
    printWindow.print();
}
