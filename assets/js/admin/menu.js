import { db, MENU_COL, INVENTORY_COL } from './config.js';
import { state } from './state.js';
import { getDocs, addDoc, updateDoc, deleteDoc, doc, onSnapshot } from "https://www.gstatic.com/firebasejs/11.2.0/firebase-firestore.js";

let allAdminMenuItems = [];
let inventoryItemsCache = [];

export function initMenu() {
    seedDefaultItems().catch(err => console.error("Seed check error:", err));
    deduplicateMenu();
    seedCakesAndPastries();
    seedCocktails();
    seedDesserts();

    // Fetch and Render
    onSnapshot(MENU_COL, (snapshot) => {
        const grid = document.getElementById('menuGrid');
        if (!grid) return;
        if (snapshot.empty) {
            grid.innerHTML = '<p style="grid-column:1/-1; text-align:center;">No menu items found.</p>';
            return;
        }

        allAdminMenuItems = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
        window.menuItemsData = {};
        allAdminMenuItems.forEach(item => window.menuItemsData[item.id] = item);

        const categories = [...new Set(allAdminMenuItems.map(item => item.category || 'General'))].sort();
        const filtersDiv = document.getElementById('adminCategoryFilters');
        if (filtersDiv) {
            let currentActiveCat = 'all';
            const activeBtn = filtersDiv.querySelector('.cat-btn.active');
            if (activeBtn) currentActiveCat = activeBtn.getAttribute('data-cat');

            let catHTML = `<button class="cat-btn ${currentActiveCat === 'all' ? 'active' : ''}" data-cat="all" onclick="window.filterAdminMenu('all', this)"><span>All</span></button>`;
            categories.forEach(cat => {
                catHTML += `<button class="cat-btn ${currentActiveCat === cat ? 'active' : ''}" data-cat="${cat}" onclick="window.filterAdminMenu('${cat}', this)"><span>${cat}</span></button>`;
            });
            filtersDiv.innerHTML = catHTML;
            const newActiveBtn = filtersDiv.querySelector(`.cat-btn[data-cat="${currentActiveCat}"]`);
            window.filterAdminMenu(currentActiveCat, newActiveBtn);
        } else {
            renderAdminMenuCards(allAdminMenuItems);
        }
    });

    onSnapshot(INVENTORY_COL, (snap) => {
        inventoryItemsCache = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    });

    const form = document.getElementById('menuForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const recipe = [];
            document.querySelectorAll('#recipeList tr').forEach(r => {
                const ingId = r.querySelector('[name="recipeIng"]').value;
                const qty = parseFloat(r.querySelector('[name="recipeQty"]').value);
                if (ingId && qty > 0) {
                    const ing = inventoryItemsCache.find(i => i.id === ingId);
                    recipe.push({ ingredientId: ingId, quantity: qty, ingredientName: ing ? ing.name : '' });
                }
            });

            const rawName = document.getElementById('menuName').value.trim();
            const rawDesc = document.getElementById('menuDesc').value.trim();
            const rawPrice = document.getElementById('menuPrice').value;
            const parsedPrice = parseFloat(rawPrice);

            if (!rawName || rawName.length > 50 || !rawDesc || rawDesc.length > 200 || isNaN(parsedPrice) || parsedPrice < 0) {
                alert("Invalid input detected.");
                return;
            }

            const data = {
                name: rawName,
                description: rawDesc,
                price: parsedPrice,
                imageUrl: document.getElementById('menuImage').value,
                category: document.getElementById('menuCategory').value,
                recipe: recipe,
                updatedAt: new Date()
            };

            state.isSaving = true;
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                if (state.currentEditId) {
                    await updateDoc(doc(db, 'menu_items', state.currentEditId), data);
                } else {
                    data.createdAt = new Date();
                    await addDoc(MENU_COL, data);
                }
                window.closeModal('menuModal');
                form.reset();
                document.getElementById('recipeList').innerHTML = '';
                state.currentEditId = null;
            } catch (error) { alert("Error: " + error.message); }
            finally {
                btn.disabled = false;
                btn.textContent = "Save Item";
                state.isSaving = false;
            }
        });
    }

    const addIngBtn = document.getElementById('addIngredientBtn');
    if (addIngBtn) addIngBtn.addEventListener('click', () => addRecipeRow());

    // Expose globals
    window.renderAdminMenuCards = renderAdminMenuCards;
    window.filterAdminMenu = filterAdminMenu;
    window.openAddMenuModal = openAddMenuModal;
    window.editMenuItem = editMenuItem;
    window.deleteMenuItem = deleteMenuItem;
    window._addRecipeRow = addRecipeRow;
}

function renderAdminMenuCards(items) {
    const grid = document.getElementById('menuGrid');
    if (!grid) return;
    if (items.length === 0) {
        grid.innerHTML = '<p style="grid-column:1/-1; text-align:center;">No items matching category.</p>';
        return;
    }
    grid.innerHTML = items.map(item => `
        <div class="menu-item-card">
            <div class="menu-item-actions">
                <button class="btn-icon edit" type="button" onclick="window.editMenuItem('${item.id}')"><i class="fas fa-edit"></i></button>
                <button class="btn-icon delete" type="button" onclick="window.deleteMenuItem('${item.id}')"><i class="fas fa-trash"></i></button>
            </div>
            <img src="${item.imageUrl || 'https://via.placeholder.com/300'}" class="menu-item-img" alt="${item.name}">
            <div class="menu-item-details">
                <h4 class="menu-item-title">${item.name}</h4>
                <p class="menu-item-desc">${item.description}</p>
                <div class="menu-item-price">₱${item.price}</div>
                <small style="color:#999; text-transform:uppercase; font-size:0.7rem;">${item.category || 'General'}</small>
            </div>
        </div>
    `).join('');
}

function filterAdminMenu(cat, btn) {
    const container = document.getElementById('adminCategoryFilters');
    if (container) container.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    if (cat === 'all') renderAdminMenuCards(allAdminMenuItems);
    else renderAdminMenuCards(allAdminMenuItems.filter(item => (item.category || 'General') === cat));
}

function addRecipeRow(ingId = '', qty = 1) {
    const recipeList = document.getElementById('recipeList');
    if (!recipeList) return;
    const row = document.createElement('tr');
    const options = inventoryItemsCache.map(i => `<option value="${i.id}" data-unit="${i.unit}" ${i.id === ingId ? 'selected' : ''}>${i.name}</option>`).join('');
    row.innerHTML = `
        <td><select class="form-control" style="padding:5px;" name="recipeIng"><option value="">Select Ingredient</option>${options}</select></td>
        <td><input type="number" class="form-control" style="padding:5px;" name="recipeQty" value="${qty}" min="0.1" step="0.1"></td>
        <td style="font-size:0.8rem; color:#666;" class="unit-disp">-</td>
        <td><i class="fas fa-times" style="color:#dc3545; cursor:pointer;" onclick="this.closest('tr').remove()"></i></td>
    `;
    const select = row.querySelector('select');
    const unitDisp = row.querySelector('.unit-disp');
    const updateUnit = () => {
        const opt = select.selectedOptions[0];
        unitDisp.textContent = opt ? (opt.dataset.unit || '-') : '-';
    };
    select.addEventListener('change', updateUnit);
    updateUnit();
    recipeList.appendChild(row);
}

function openAddMenuModal() {
    const form = document.getElementById('menuForm');
    if (form) form.reset();
    document.getElementById('menuItemId').value = '';
    state.currentEditId = null;
    document.getElementById('menuModalTitle').textContent = "Add Menu Item";
    document.getElementById('recipeList').innerHTML = '';
    window.openModal('menuModal');
}

function editMenuItem(id) {
    const data = window.menuItemsData ? window.menuItemsData[id] : null;
    if (data) {
        document.getElementById('menuItemId').value = id;
        document.getElementById('menuName').value = data.name;
        document.getElementById('menuDesc').value = data.description;
        document.getElementById('menuPrice').value = data.price;
        document.getElementById('menuImage').value = data.imageUrl;
        document.getElementById('menuCategory').value = data.category || 'All Day Breakfast';
        const recipeList = document.getElementById('recipeList');
        if (recipeList) {
            recipeList.innerHTML = '';
            if (data.recipe && Array.isArray(data.recipe)) {
                data.recipe.forEach(r => addRecipeRow(r.ingredientId, r.quantity));
            }
        }
        state.currentEditId = id;
        document.getElementById('menuModalTitle').textContent = "Edit Menu Item";
        window.openModal('menuModal');
    }
}

async function deleteMenuItem(id) {
    if (confirm("Are you sure you want to delete this item?")) {
        try {
            await deleteDoc(doc(db, 'menu_items', id));
        } catch (e) { alert("Delete failed: " + e.message); }
    }
}

async function deduplicateMenu() {
    const snap = await getDocs(MENU_COL);
    const seen = new Set();
    const toDelete = [];
    snap.docs.forEach(d => {
        const n = d.data().name?.trim().toLowerCase();
        if (seen.has(n)) toDelete.push(d.id);
        else seen.add(n);
    });
    for (const id of toDelete) await deleteDoc(doc(db, 'menu_items', id));
}

// Seeding logic (private to module)
async function seedDefaultItems() {
    const snap = await getDocs(MENU_COL);
    if (!snap.empty) return;
    const defaultItems = [
        { name: "Chicken Teriyaki Doria", price: 285, description: "Chicken Teriyaki, Rice, Teriyaki Sauce, Mozzarella", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=500&h=400&fit=crop" },
        { name: "Dark Chocolate Champorado", price: 229, description: "Glutinous Rice, Dutch Cocoa Powder, Dark Chocolate, Dilis, Honey, Milk", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1517673132405-a56a62b18caf?w=500&h=400&fit=crop" },
        { name: "Beef Teriyaki Doria", price: 285, description: "Beef Teriyaki, Rice, Teriyaki Sauce, Mozzarella", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&h=400&fit=crop" },
        { name: "Lucban Longganisa", price: 319, description: "Lucban Longganisa, Garlic Rice, Egg & Side Salad", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=500&h=400&fit=crop" },
        { name: "Breakfast Sausage", price: 289, description: "Breakfast Sausage, Garlic Rice, Double Eggs, Side Salad", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1525351484163-7529414344d8?w=500&h=400&fit=crop" },
        { name: "Salmon Teriyaki Doria", price: 349, description: "Norwegian Salmon Teriyaki, Rice, Teriyaki Sauce, Mozzarella", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=500&h=400&fit=crop" },
        { name: "Big Breakfast", price: 369, description: "Choice of Pancakes or Toast, Bacon, Sausage, Egg, Beans, Tomato, Mushrooms", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=500&h=400&fit=crop" },
        { name: "Bangus Belly", price: 385, description: "Fried Bangus Belly, Garlic Rice, Egg & Side Salad", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=500&h=400&fit=crop" },
        { name: "Bacon Rice", price: 325, description: "Premium Smoked Bacon, Garlic Rice, Egg and Side Salad", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1606787366850-de6330128bfc?w=500&h=400&fit=crop" },
        { name: "Beef Sirloin", price: 325, description: "Thinly Sliced Beef Marinated with Calamansi & Soy Sauce, Served with Rice, Egg and Side Salad", category: "All Day Breakfast", imageUrl: "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500&h=400&fit=crop" },
    ];
    for (const item of defaultItems) {
        try { await addDoc(MENU_COL, { ...item, createdAt: new Date(), updatedAt: new Date() }); }
        catch (err) { console.error("Seed failed", err); }
    }
}

async function seedCakesAndPastries() {
    const pastries = [
        { name: "Chocolate-Dipped Potato Chips", price: 290, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/QdWjS5Xy/ylmenu-CHOCOLATEDIPPEDPOTATOCHIPS-1.webp" },
        { name: "Blueberry Danish", price: 180, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/vmtFStCN/danishblueberry.webp" },
        { name: "Strawberry Danish", price: 180, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/j29GJgJP/danishstrawberry.webp" },
        { name: "Chocolate Fudge Cake", price: 1000, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/LX87QKtV/chocolatefudgecake-0810fb37-8a3d-4e44-b0ba-b7d46ba3dea0.webp" },
        { name: "No Bake Pistachio Cheesecake", price: 980, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/qvmZtZVt/nobakepistachiocheesecake.webp" },
        { name: "No Bake Chocolate Cheesecake", price: 780, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/sgFgkK0S/nobakechocolatecheesecake-d8f74613-7aa8-449f-9627-92f5a835a245.webp" },
        { name: "Carrot Cake", price: 980, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/26TqmvLT/carrotcake-4aa42c8e-c314-4fb1-a312-24ce3efc56d2.webp" },
        { name: "Strawberry Shortcake", price: 980, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/Dy3VFDn2/strawberryshortcake.webp" },
        { name: "S'mores", price: 780, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/wxmv0S0r/s-mores-77eeea0a-256d-49e7-9e92-380ca2313646.webp" },
        { name: "Red Velvet", price: 780, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/J7Zrbjzv/redvelvet-4a29f5d4-0078-43c3-863c-7b79d089943f.webp" },
        { name: "Pistachio Sansrival", price: 1200, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/DZsTPKn6/pistachiosansrival-1fe55d1c-d232-4b38-89a7-2d952b0092d1.webp" },
        { name: "No Bake Blueberry Cheesecake", price: 780, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/wT9pLPPN/nobakeblueberrycheesecake-3a1a7ed1-d1f0-49cf-a869-b540fad5332d.webp" },
        { name: "New York Style Cheesecake", price: 880, description: "sweets", category: "Cakes & Pastries", imageUrl: "https://i.postimg.cc/FRttRn3Y/newyorkstylecheesecake.webp" }
    ];
    try {
        const snap = await getDocs(MENU_COL);
        const existingNames = snap.docs.map(doc => doc.data().name);
        for (const item of pastries) {
            if (!existingNames.includes(item.name)) {
                await addDoc(MENU_COL, { ...item, createdAt: new Date(), updatedAt: new Date() });
            }
        }
    } catch (err) { console.error("Seed failed", err); }
}

async function seedCocktails() {
    const cocktails = [
        { name: "Pineapple Whiskey Smash", price: 200, description: "A refreshing blend of bold whiskey, sweet pineapple, and fresh mint.", category: "Cocktails", imageUrl: "https://i.postimg.cc/3N76SK67/1.webp" },
        { name: "Flat White Martini", price: 220, description: "A creamy, boozy kick of espresso and Irish cream liqueur.", category: "Cocktails", imageUrl: "https://i.postimg.cc/CLrPDHFY/2.webp" },
        { name: "Muslide", price: 220, description: "A dessert in a glass featuring vodka, coffee liqueur, and cream.", category: "Cocktails", imageUrl: "https://i.postimg.cc/PxH2hSMN/3.webp" },
        { name: "Ginger Highball", price: 200, description: "Crisp and spicy, mixing classic whiskey with ginger ale.", category: "Cocktails", imageUrl: "https://i.postimg.cc/Pr7yNX4T/4.webp" },
        { name: "Midnight Purple", price: 200, description: "A visually stunning, sweet berry-infused spirit.", category: "Cocktails", imageUrl: "https://i.postimg.cc/DZ5dhnZR/5.webp" },
        { name: "Concord Sour", price: 200, description: "A fruity twist on the classic sour with rich grape flavors.", category: "Cocktails", imageUrl: "https://i.postimg.cc/fRt3CX9k/Concord-Sour.webp" },
        { name: "Jack Coke", price: 260, description: "The timeless, bold mix of Jack Daniel's whiskey and Coca-Cola.", category: "Cocktails", imageUrl: "https://i.postimg.cc/GmYF2k9n/7.webp" },
        { name: "Classic Margarita", price: 200, description: "The perfect balance of tequila, tangy lime, and a salted rim.", category: "Cocktails", imageUrl: "https://i.postimg.cc/d3b7Px2Z/8.webp" },
        { name: "Blue Moscow Mule", price: 260, description: "A citrusy, striking blue variation of the classic ginger-vodka mule.", category: "Cocktails", imageUrl: "https://i.postimg.cc/0r1NHJvk/9.webp" },
        { name: "Tequila Sunrise", price: 260, description: "A vibrant, sweet mix of tequila, orange juice, and grenadine.", category: "Cocktails", imageUrl: "https://i.postimg.cc/ZqQdZHzM/10.webp" },
        { name: "Florida Sunset", price: 260, description: "A sweet, tropical blend of fruit juices and spirits.", category: "Cocktails", imageUrl: "https://i.postimg.cc/HngjsPZJ/11.webp" },
        { name: "Mojito", price: 220, description: "A highly refreshing mix of white rum, fresh lime, and muddled mint.", category: "Cocktails", imageUrl: "https://i.postimg.cc/h41t1sZ6/12.webp" },
        { name: "Sangria", price: 260, description: "A chilled, fruity wine cocktail loaded with fresh fruit slices.", category: "Cocktails", imageUrl: "https://i.postimg.cc/Wz5jL9Qg/13.webp" },
        { name: "Frozen Margarita", price: 200, description: "The classic tequila and lime cocktail, blended into an icy slush.", category: "Cocktails", imageUrl: "https://i.postimg.cc/tJv0msx9/14.webp" },
        { name: "Strawberry Daiquiri", price: 220, description: "A sweet, ice-blended rum cocktail packed with strawberries.", category: "Cocktails", imageUrl: "https://i.postimg.cc/YqVK6mkS/15.webp" },
        { name: "Frozen Cucumber Margarita", price: 220, description: "An ultra-refreshing, icy margarita with a cool cucumber twist.", category: "Cocktails", imageUrl: "https://i.postimg.cc/s2YkC6h8/16.webp" },
        { name: "Frozen Mango Margarita", price: 260, description: "A tropical, ice-blended treat bursting with sweet mango flavor.", category: "Cocktails", imageUrl: "https://i.postimg.cc/tgNMM104/17.webp" }
    ];
    try {
        const snap = await getDocs(MENU_COL);
        const existingNames = snap.docs.map(doc => doc.data().name);
        for (const item of cocktails) {
            if (!existingNames.includes(item.name)) {
                await addDoc(MENU_COL, { ...item, createdAt: new Date(), updatedAt: new Date() });
            }
        }
    } catch (err) { console.error("Seed failed", err); }
}

async function seedDesserts() {
    const desserts = [
        { name: "No Bake Pistachio Cheesecake", price: 165, description: "Creamy, chilled cheesecake packed with rich crushed pistachios.", category: "Desserts", imageUrl: "https://i.postimg.cc/T2LZR3g5/1.webp" },
        { name: "No Bake Oreo Cheesecake", price: 180, description: "Chilled cheesecake loaded with crushed Oreo cookies.", category: "Desserts", imageUrl: "https://i.postimg.cc/LXq06xY6/2.webp" },
        { name: "Pistachio Sansrival", price: 250, description: "Layers of chewy meringue and buttercream covered in crunchy pistachios.", category: "Desserts", imageUrl: "https://i.postimg.cc/7P2QFQyq/3.webp" },
        { name: "No Bake Chocolate Cheesecake", price: 180, description: "Smooth and decadent chilled cheesecake for chocolate lovers.", category: "Desserts", imageUrl: "https://i.postimg.cc/Px82kN9J/4.webp" },
        { name: "S'mores", price: 180, description: "Classic dessert treat made with toasted marshmallows, chocolate, and graham crackers.", category: "Desserts", imageUrl: "https://i.postimg.cc/htPst2ND/5.webp" },
        { name: "Strawberry Bingsu", price: 425, description: "Korean shaved ice topped with fresh strawberries, syrup, and condensed milk.", category: "Desserts", imageUrl: "https://i.postimg.cc/KzLrHw34/6.webp" },
        { name: "Carrot Cake", price: 200, description: "Moist spiced cake topped with rich cream cheese frosting.", category: "Desserts", imageUrl: "https://i.postimg.cc/XNk9Bwf5/7.webp" },
        { name: "Red Velvet Cake", price: 200, description: "Classic cocoa-flavored red cake finished with sweet cream cheese icing.", category: "Desserts", imageUrl: "https://i.postimg.cc/3wGv8pr3/8.webp" },
        { name: "No Bake Blueberry Cheesecake", price: 180, description: "Chilled cheesecake topped with sweet and tangy blueberry compote.", category: "Desserts", imageUrl: "https://i.postimg.cc/L4DZ8mS9/9.webp" },
        { name: "Baked Blueberry Cheesecake", price: 200, description: "Classic, dense baked cheesecake finished with a rich blueberry topping.", category: "Desserts", imageUrl: "https://i.postimg.cc/nhyXPz4R/10.webp" },
        { name: "Oreo Cake", price: 200, description: "Moist chocolate cake layers filled with cookies and cream frosting.", category: "Desserts", imageUrl: "https://i.postimg.cc/j235QdqF/11.webp" },
        { name: "Chocolate Fudge Cake", price: 200, description: "Ultra-rich dark chocolate cake covered in gooey fudge icing.", category: "Desserts", imageUrl: "https://i.postimg.cc/bwtJkh73/12.webp" },
        { name: "Mango Bingsu", price: 425, description: "Fluffy Korean shaved ice topped with sweet, ripe mango cubes.", category: "Desserts", imageUrl: "https://i.postimg.cc/tTXCKVYB/13.webp" },
        { name: "Oreo Bingsu", price: 345, description: "Snowy shaved ice generously topped with crushed Oreos and sweet cream.", category: "Desserts", imageUrl: "https://i.postimg.cc/prSW5GkS/14.webp" },
        { name: "Strawberry Bingsu Petite", price: 265, description: "Solo-sized serving of our refreshing strawberry shaved ice.", category: "Desserts", imageUrl: "https://i.postimg.cc/9QZmzB9n/15.webp" },
        { name: "Mango Bingsu Petite", price: 285, description: "Personal-sized shaved ice bowl topped with fresh sweet mangoes.", category: "Desserts", imageUrl: "https://i.postimg.cc/nL1nGc01/16.webp" },
        { name: "Oreo Bingsu Petite", price: 265, description: "Solo serving of our popular cookies and cream shaved ice.", category: "Desserts", imageUrl: "https://i.postimg.cc/WzxV1D1p/17.webp" },
        { name: "Affogato", price: 99, description: "A scoop of vanilla ice cream topped with a hot espresso shot.", category: "Desserts", imageUrl: "https://i.postimg.cc/mgfRxFny/18.webp" },
        { name: "Mango Belle", price: 280, description: "A special, creamy, and refreshing sweet mango dessert treat.", category: "Desserts", imageUrl: "https://i.postimg.cc/mDDW33cb/19.webp" },
        { name: "Mango Float", price: 290, description: "Classic layered dessert of graham crackers, sweetened cream, and fresh mangoes.", category: "Desserts", imageUrl: "https://i.postimg.cc/SQHjsR52/20.webp" }
    ];
    try {
        const snap = await getDocs(MENU_COL);
        for (const item of desserts) {
            const existingDoc = snap.docs.find(doc => doc.data().name === item.name);
            if (!existingDoc) {
                await addDoc(MENU_COL, { ...item, createdAt: new Date(), updatedAt: new Date() });
            } else if (existingDoc.data().category !== 'Desserts') {
                await updateDoc(doc(db, 'menu_items', existingDoc.id), { category: 'Desserts', updatedAt: new Date() });
            }
        }
    } catch (err) { console.error("Seed failed", err); }
}
