<!-- User Details Modal -->
<div class="modal" id="userDetailsModal">
    <div class="modal-content" style="max-width: 440px; text-align: center;">
        <div class="modal-header">
            <h3 class="modal-title">User Profile</h3>
            <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('userDetailsModal')"></i>
        </div>
        
        <div class="modal-body" style="padding-top: 1rem;">
            <div style="margin-bottom: 2rem; position: relative; display: inline-block;">
                <div id="userDetailAvatarContainer" style="width: 120px; height: 120px; border-radius: 50%; padding: 4px; background: linear-gradient(135deg, var(--primary-gold), #fff); box-shadow: 0 10px 25px rgba(201,169,97,0.3);">
                    <img id="userDetailAvatar" src="" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: #fff;">
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h2 id="userDetailName" style="color: var(--dark-brown); font-family: 'Playfair Display', serif; margin-bottom: 5px;">—</h2>
                <p id="userDetailEmail" style="color: var(--text-muted); font-size: 0.95rem;"></p>
                <div id="userDetailRoleBadge" style="margin-top: 10px;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
                <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                    <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Username</small>
                    <span id="userDetailUsername" style="font-weight: 600; color: var(--dark-brown);">—</span>
                </div>
                <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                    <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Birthday</small>
                    <span id="userDetailBirthday" style="font-weight: 600; color: var(--dark-brown);">—</span>
                </div>
                <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                    <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Contact</small>
                    <span id="userDetailPhone" style="font-weight: 600; color: var(--dark-brown);">—</span>
                </div>
                <div class="info-group" style="background: var(--light-bg); padding: 12px; border-radius: 12px;">
                    <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 4px;">Member Since</small>
                    <span id="userDetailJoined" style="font-weight: 600; color: var(--dark-brown);">—</span>
                </div>
            </div>

            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button class="btn-action" id="editUserBtn" style="background: var(--white); color: var(--dark-brown); border: 1px solid var(--border-color); padding: 10px 20px;">
                        <i class="fas fa-edit"></i> Edit User
                    </button>
                    <button class="btn-action" id="deleteUserModalBtn" style="background: #fff5f5; color: #d00; border: 1px solid #fed7d7; padding: 10px 20px;">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Modal -->
<div class="modal" id="menuModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="menuModalTitle">Add Menu Item</h3>
            <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('menuModal')"></i>
        </div>
        <form id="menuForm">
            <input type="hidden" id="menuItemId">
            <div class="form-group">
                <label class="form-label">Item Name</label>
                <input type="text" id="menuName" class="form-control" required maxlength="50" placeholder="e.g. Beef Teriyaki (Max 50 chars)">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="menuDesc" class="form-control" rows="3" required maxlength="200" placeholder="Ingredients, taste, etc. (Max 200 chars)"></textarea>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" id="menuPrice" class="form-control" required min="0" max="99999" step="0.01" oninput="window.limitDigits(this, 5)">
                </div>
                <div class="form-group">
                     <label class="form-label">Category</label>
                     <select id="menuCategory" class="form-control">
                         <option value="All Day Breakfast">All Day Breakfast</option>
                         <option value="Best Seller">Best Seller</option>
                         <option value="Cakes & Pastries">Cakes & Pastries</option>
                         <option value="Cocktails">Cocktails</option>
                         <option value="Desserts">Desserts</option>
                         <option value="Frappes">Frappes</option>
                         <option value="Hand-Tossed Pizza">Hand-Tossed Pizza</option>
                         <option value="Home Page">Home Page</option>
                         <option value="Hot Coffee">Hot Coffee</option>
                         <option value="Ice Beverages">Ice Beverages</option>
                         <option value="Ice Coffee">Ice Coffee</option>
                         <option value="Milk Tea">Milk Tea</option>
                         <option value="Milkshakes & Smoothies">Milkshakes & Smoothies</option>
                         <option value="Pasta & Salad">Pasta & Salad</option>
                         <option value="Rice Bowls">Rice Bowls</option>
                         <option value="Rice Plates">Rice Plates</option>
                         <option value="Sandwiches">Sandwiches</option>
                         <option value="Sides">Sides</option>
                         <option value="Starters & Sandwiches">Starters & Sandwiches</option>
                         <option value="Steaks">Steaks</option>
                         <option value="Sweet Breakfast">Sweet Breakfast</option>
                         <option value="Thin Crust Pizza">Thin Crust Pizza</option>
                     </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Image URL</label>
                <input type="url" id="menuImage" class="form-control" placeholder="https://..." required>
                <small style="color:var(--text-muted);">Use a valid image URL.</small>
            </div>

            <div class="form-group">
                 <label class="form-label">Recipe / Ingredients</label>
                 <div style="background:var(--light-bg); padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                     <table style="width:100%; font-size:0.85rem;" id="recipeTable">
                         <thead>
                             <tr style="text-align:left; color:var(--text-muted);">
                                 <th style="padding-bottom:5px;">Ingredient</th>
                                 <th style="padding-bottom:5px; width:70px;">Qty</th>
                                 <th style="padding-bottom:5px; width:40px;">Unit</th>
                                 <th style="width:30px;"></th>
                             </tr>
                         </thead>
                         <tbody id="recipeList">
                             <!-- Rows will be added here -->
                         </tbody>
                     </table>
                     <button type="button" id="addIngredientBtn" style="margin-top:10px; background:none; border:1px dashed var(--primary-gold); color:var(--primary-gold); padding:5px 10px; border-radius:4px; font-size:0.8rem; cursor:pointer; width:100%;">
                         <i class="fas fa-plus"></i> Add Ingredient
                     </button>
                 </div>
            </div>

            <button type="submit" class="btn-submit">Save Item</button>
        </form>
    </div>
</div>

<!-- Inventory Modal -->
<div id="inventoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="invModalTitle">Add Inventory Item</h3>
            <button class="btn-icon" onclick="closeModal('inventoryModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="inventoryForm">
            <input type="hidden" id="invItemId">
            <div class="form-group">
                <label class="form-label">Item Name</label>
                <input type="text" id="invName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select id="invCategory" class="form-control">
                    <option value="Ingredients">Ingredients</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Packaging">Packaging</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Quantity</label>
                <input type="number" id="invQuantity" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Unit</label>
                <input type="text" id="invUnit" class="form-control" placeholder="e.g., kg, pcs, box" required>
            </div>
            <div class="form-group">
                <label class="form-label">Min. Stock Level (Alert)</label>
                <input type="number" id="invMinLevel" class="form-control" min="0" value="10">
            </div>
            <button type="submit" class="btn-submit" id="saveInvBtn">Save Item</button>
        </form>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal" id="reservationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="reservationModalTitle">New Reservation</h3>
            <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('reservationModal')"></i>
        </div>
        <form id="reservationForm">
            <div class="form-group">
                <label class="form-label">Guest Name</label>
                <input type="text" id="resGuestName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Booking Type</label>
                <select id="resBookingType" class="form-control">
                    <option value="Regular Table" selected>🪑 Regular Table</option>
                    <option value="Exclusive Venue">🏛️ Exclusive Venue (Whole Restaurant)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" id="resDate" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Time</label>
                <input type="time" id="resTime" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Number of Guests</label>
                <input type="number" id="resGuests" class="form-control" min="1" max="50" required>
            </div>
            <div class="form-group">
                <label class="form-label">Type of Occasion</label>
                <select id="resOccasion" class="form-control" required>
                    <option value="" disabled selected>Select an occasion</option>
                    <option value="Casual Dining">Casual Dining</option>
                    <option value="Birthday">Birthday</option>
                    <option value="Anniversary">Anniversary</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Corporate Event">Corporate Event</option>
                    <option value="Private Gathering">Private Gathering</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Select Table (Visual Map)</label>
                <input type="hidden" id="resTable"> <!-- Hidden Input for value -->
                <div id="resTableMap" class="table-map-grid">
                    <!-- Tables rendered by JS -->
                </div>
                <p style="font-size:0.8rem; color:var(--text-muted); margin-top:5px;">
                    <span style="display:inline-block; width:10px; height:10px; background:#e8f5e9; border:1px solid #c8e6c9; margin-right:5px;"></span>Available
                    <span style="display:inline-block; width:10px; height:10px; background:#ffebee; border:1px solid #ffcdd2; margin-left:10px; margin-right:5px;"></span>Occupied
                </p>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select id="resStatus" class="form-control">
                    <option value="Confirmed">Confirmed</option>
                    <option value="Pending">Pending</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Save Reservation</button>
        </form>
    </div>
</div>

<!-- Table Assignment Modal -->
<div class="modal" id="tableAssignModal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title">Assign Table</h3>
            <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('tableAssignModal')"></i>
        </div>
        <form id="tableAssignForm">
            <input type="hidden" id="assignResId">
            <div class="form-group">
                <label class="form-label">Guest Name</label>
                <input type="text" id="assignGuestName" class="form-control" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group">
                <label class="form-label">Select Table</label>
                <select id="assignTableNumber" class="form-control" style="display:none;"> <!-- Hidden select -->
                    <option value="">-- Choose a Table --</option>
                </select>
                <div id="assignTableMap" class="table-map-grid"></div>
            </div>
            <button type="submit" class="btn-submit" id="assignTableBtn">
                <i class="fas fa-check"></i> Confirm & Approve
            </button>
        </form>
    </div>
</div>

<!-- Promo Modal -->
<div class="modal" id="promoModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="promoModalTitle">Create Promotion</h3>
            <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('promoModal')"></i>
        </div>
        <form id="promoForm">
            <div class="form-group">
                <label class="form-label">Promo Code</label>
                <input type="text" id="promoCode" class="form-control" placeholder="e.g. SAVE20" required style="text-transform:uppercase;">
            </div>
            <div class="form-group">
                <label class="form-label">Discount Amount</label>
                <input type="number" id="promoDiscount" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Discount Type</label>
                <select id="promoType" class="form-control">
                    <option value="Percentage">Percentage (%)</option>
                    <option value="Fixed">Fixed Amount (₱)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Valid Until</label>
                <input type="date" id="promoExpiry" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Max Uses (0 = unlimited)</label>
                <input type="number" id="promoMaxUses" class="form-control" value="0" min="0">
            </div>
            <button type="submit" class="btn-submit">Save Promotion</button>
        </form>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal" id="orderDetailsModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Order Details <span id="modalOrderId" style="font-weight:400; color:var(--text-muted); font-size:0.9rem;"></span></h3>
            <i class="fas fa-times" style="cursor:pointer;" onclick="closeModal('orderDetailsModal')"></i>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color:var(--primary-gold); margin-bottom: 5px;">Customer Info</h4>
            <p id="modalCustomerName" style="font-weight:600; font-size:1.1rem; color:var(--dark-brown);"></p>
            <p id="modalCustomerContact" style="font-size:0.9rem; color:var(--text-muted);"></p>
            <p id="modalDeliveryAddress" style="font-size:0.9rem; color:var(--text-muted); margin-top:4px;"></p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h4 style="color:var(--primary-gold); margin-bottom: 10px;">Order Items</h4>
            <table style="width:100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee; text-align: left;">
                        <th style="padding: 8px 0; color:var(--text-muted);">Item</th>
                        <th style="padding: 8px 0; color:var(--text-muted); text-align: center;">Qty</th>
                        <th style="padding: 8px 0; color:var(--text-muted); text-align: right;">Price</th>
                        <th style="padding: 8px 0; color:var(--text-muted); text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody id="modalOrderItems">
                    <!-- Items will be populated here -->
                </tbody>
                <tfoot style="border-top: 2px solid #eee;">
                    <tr>
                        <td colspan="3" style="padding: 15px 0; text-align: right; font-weight: 700; color:var(--dark-brown);">Grand Total</td>
                        <td style="padding: 15px 0; text-align: right; font-weight: 700; color:var(--primary-gold); font-size: 1.1rem;" id="modalOrderTotal">₱0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="text-align: right;">
             <span id="modalPaymentStatus" class="status-badge" style="margin-right: 10px;"></span>
             <span id="modalOrderStatus" class="status-badge"></span>
             <button class="btn-action" id="btnPrintReceipt" style="margin-left: 10px; background: var(--dark-brown); color: white;">
                <i class="fas fa-print"></i> Print Receipt
             </button>
        </div>
    </div>
</div>

<!-- Receipt Container (Hidden for Print) -->
<div id="receipt-container" style="display:none;">
    <div style="text-align:center; margin-bottom:10px;">
        <h3 style="margin:0; font-size:16px; font-weight:bold;">Le Maison</h3>
        <p style="margin:0; font-size:10px;">de Yelo Lane</p>
        <p style="margin:5px 0 0; font-size:10px;">--------------------------------</p>
    </div>
    
    <div style="margin-bottom:10px;">
        <p style="margin:2px 0;"><strong>Order:</strong> <span id="receiptOrderId">#123456</span></p>
        <p style="margin:2px 0;"><strong>Date:</strong> <span id="receiptDate">2023-10-25</span></p>
        <p style="margin:2px 0;"><strong>Customer:</strong> <span id="receiptCustomer">John Doe</span></p>
        <p style="margin:2px 0;"><strong>Type:</strong> <span id="receiptType">Delivery</span></p>
    </div>

    <p style="margin:5px 0 0; font-size:10px;">--------------------------------</p>

    <table style="width:100%; font-size:11px; margin-bottom:10px;">
        <tbody id="receiptItems">
            <!-- Items populated here -->
        </tbody>
    </table>

    <p style="margin:5px 0 0; font-size:10px;">--------------------------------</p>

    <div style="text-align:right; margin-bottom:10px;">
        <p style="margin:2px 0;"><strong>Total:</strong> <span id="receiptTotal" style="font-size:14px; font-weight:bold;">₱0.00</span></p>
        <p style="margin:2px 0; font-size:10px;">Payment: <span id="receiptPayment">Cash</span></p>
    </div>

    <div style="text-align:center; margin-top:15px;">
        <p style="margin:0; font-size:10px;">Thank you for ordering!</p>
        <p style="margin:0; font-size:10px;">Please come again.</p>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="paymentModalTitle">Process Payment</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;" id="paymentModalOrderInfo">Order # - Total: ₱0.00</p>
            </div>
            <button class="btn-icon" onclick="closeModal('paymentModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="paymentForm">
            <input type="hidden" id="payOrderId">
            <input type="hidden" id="payTotalAmount">
            
            <div class="form-group">
                <label class="form-label">Payment Method</label>
                <select id="paymentMethod" class="form-control" onchange="window.togglePaymentView()" required>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash / Online</option>
                </select>
            </div>

            <div id="cashSection">
                <div class="form-group">
                    <label class="form-label">Amount Received (₱)</label>
                    <input type="number" id="amountReceived" class="form-control" step="0.01" min="0" oninput="window.calculateChange()" placeholder="Enter cash given">
                </div>
                <div class="form-group">
                    <label class="form-label">Change (Sukli)</label>
                    <div id="changeLabel" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-gold); padding: 0.5rem; background: rgba(201,169,97,0.1); border-radius: 8px; text-align: center;">₱0.00</div>
                </div>
            </div>

            <div id="onlineSection" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Reference Number</label>
                    <input type="text" id="referenceNumber" class="form-control" placeholder="GCash reference number">
                </div>
                <div id="proofContainer" style="margin-top:1rem; text-align:center; display:none;">
                    <button type="button" class="btn-action" id="viewProofBtn" style="width:100%; justify-content:center; background:#e3f2fd; color:#1e88e5;">
                        <i class="fas fa-image"></i> View Customer Proof
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top:1rem;">
                <i class="fas fa-check-circle" style="margin-right:8px;"></i> Confirm Payment
            </button>
        </form>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageViewerModal" class="modal">
    <div class="modal-content" style="max-width:90%; text-align:center; background:transparent; border:none; box-shadow:none; padding:0;">
        <div style="position:relative; display:inline-block;">
            <button class="btn-icon" onclick="closeModal('imageViewerModal')" style="position:absolute; top:-40px; right:0; background:white; border-radius:50%; z-index:100;"><i class="fas fa-times"></i></button>
            <img id="viewerImage" src="" style="max-width:100%; max-height:85vh; border-radius:8px; border:4px solid white; box-shadow:0 10px 40px rgba(0,0,0,0.8);">
        </div>
    </div>
</div>

<!-- Overview Modal -->
<div id="overviewModal" class="modal">
    <div class="modal-content overview-modal-content">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Business Overview</h2>
                <p style="font-size:0.8rem; color:var(--text-muted);">Detailed financial and operational breakdown</p>
            </div>
            <span class="close-btn" onclick="closeModal('overviewModal')" style="cursor:pointer; font-size:1.5rem;">&times;</span>
        </div>
        <div class="modal-body">
            <div class="overview-metrics-grid">
                <div class="metric-card">
                    <h3>Total Revenue</h3>
                    <p id="modalRevenue">₱0</p>
                </div>
                 <div class="metric-card">
                    <h3>Total Expenses</h3>
                    <p id="modalExpenses">₱0</p>
                    <small>(Estimated 60% of Revenue)</small>
                </div>
                 <div class="metric-card">
                    <h3>Net Income</h3>
                    <p id="modalNetIncome">₱0</p>
                    <small style="color:var(--success);">+40% Margin</small>
                </div>
            </div>

            <div class="overview-charts-grid">
                <div class="chart-container">
                    <h4 style="margin-bottom:1rem; color:var(--dark-brown);">Revenue Trend</h4>
                    <canvas id="modalRevenueChart"></canvas>
                </div>
                <div class="chart-container">
                    <h4 style="margin-bottom:1rem; color:var(--dark-brown);">Order Status Breakdown</h4>
                    <canvas id="modalOrderBreakdownChart"></canvas>
                </div>
            </div>

            <div class="overview-lists-grid">
                <div class="list-container">
                     <h4>Top Selling Items</h4>
                     <ul id="topSellingList" class="styled-list">
                         <li style="justify-content:center; color:#ccc;">Loading...</li>
                     </ul>
                </div>
                 <div class="list-container">
                     <h4>Recent Activity</h4>
                     <table class="data-table" style="font-size:0.85rem;">
                         <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                         </thead>
                         <tbody id="modalRecentOrders">
                             <!-- Populated by JS -->
                         </tbody>
                     </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kitchen Display System -->
<div id="kitchen-view-section" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#1a1a1a; z-index:9999; overflow-y:auto; padding:20px;">
    <div class="kitchen-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid #333; padding-bottom:15px;">
        <div style="display:flex; align-items:center; gap:15px;">
            <h1 style="color:var(--primary-gold); font-family:'Playfair Display', serif; font-size:2rem;"><i class="fas fa-utensils"></i> KITCHEN DISPLAY</h1>
            <span id="kdsClock" style="color:#888; font-size:1.2rem; font-family:monospace;">--:--:--</span>
        </div>
        <button id="exitKdsBtn" style="background:#dc3545; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer;">
            <i class="fas fa-times"></i> EXIT KDS
        </button>
    </div>
    <div id="kdsGrid" class="kitchen-grid">
        <!-- Orders Rendered Here -->
        <div style="color:#666; text-align:center; grid-column:1/-1; padding:50px; font-size:1.5rem;">Waiting for orders...</div>
    </div>
</div>

<!-- Admin Profile Modal -->
<div class="modal" id="adminProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Profile</h3>
            <button class="btn-icon" onclick="closeModal('adminProfileModal')"><i class="fas fa-times"></i></button>
        </div>
        
        <div style="display:flex; justify-content:center; margin-bottom: 2rem;">
            <div style="position:relative; width:100px; height:100px;">
                <img id="editProfileAvatar" src="" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid var(--primary-gold); background:#eee;">
                <button onclick="document.getElementById('profileAvatarInput').click()" style="position:absolute; bottom:0; right:0; background:var(--dark-brown); color:white; border:none; width:30px; height:30px; border-radius:50%; box-shadow:0 3px 6px rgba(0,0,0,0.2); cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-camera" style="font-size:0.8rem;"></i>
                </button>
                <input type="text" id="profileAvatarInput" placeholder="Enter Image URL" style="display:none;" onchange="document.getElementById('editProfileAvatar').src = this.value">
            </div>
        </div>

        <form id="adminProfileForm">
            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">First Name</label>
                    <input type="text" id="editProfileFirstName" class="form-control" required placeholder="First">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Middle Name</label>
                    <input type="text" id="editProfileMiddleName" class="form-control" placeholder="Middle">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Last Name</label>
                    <input type="text" id="editProfileLastName" class="form-control" required placeholder="Last">
                </div>
            </div>
            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Birthday</label>
                    <input type="date" id="editProfileBirthday" class="form-control">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Username</label>
                    <input type="text" id="editProfileUsername" class="form-control" required placeholder="User">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email (Read Only)</label>
                <input type="email" id="editProfileEmail" class="form-control" readonly style="background:#f5f5f5; color:#888;">
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" id="editProfilePhone" class="form-control" placeholder="09XX...">
            </div>
            <div class="form-group">
                <label class="form-label">Avatar URL (Optional)</label>
                <input type="url" id="editProfileAvatarUrl" class="form-control" placeholder="https://image-url.com/avatar.jpg" oninput="document.getElementById('editProfileAvatar').src = this.value || 'https://ui-avatars.com/api/?name=Admin'">
            </div>

            <div class="form-group" style="border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem;">
                <label class="form-label" style="font-weight: 700; color: var(--dark-brown);">Account Security</label>
                <div style="position: relative; margin-bottom: 0.8rem;">
                    <input type="password" id="editAdminPin" class="form-control" placeholder="Setup / Change 4-Digit PIN" maxlength="4" pattern="[0-9]{4}" style="padding-right: 40px; letter-spacing: 5px; text-align: center; font-size: 1.2rem;" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <div style="position: relative; margin-bottom: 0.8rem;">
                    <input type="password" id="editAdminCurrentPassword" class="form-control" placeholder="Current Password" style="padding-right: 40px;">
                </div>
                <div style="position: relative; margin-bottom: 0.8rem;">
                    <input type="password" id="editAdminNewPassword" class="form-control" placeholder="New Password" style="padding-right: 40px;">
                </div>
                <div style="position: relative;">
                    <input type="password" id="editAdminConfirmPassword" class="form-control" placeholder="Confirm New Password" style="padding-right: 40px;">
                </div>
            </div>
            
            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="btn-cancel" onclick="closeModal('adminProfileModal')" style="margin-right:10px; background:#ddd; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-submit" id="saveProfileBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Broadcast Modal -->
<div class="modal" id="broadcastModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Send Message</h3>
                <p id="broadcastRecipientCount" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">0 recipients selected</p>
            </div>
            <button class="btn-icon" onclick="closeModal('broadcastModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="broadcastForm">
            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" id="broadcastSubject" class="form-control" placeholder="Notification Subject" required>
            </div>
            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea id="broadcastMessage" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
            </div>
            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-cancel" onclick="closeModal('broadcastModal')" style="padding: 10px 20px; border: none; background: #eee; border-radius: 8px; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn-submit" id="sendBroadcastBtn" style="background: linear-gradient(135deg, #6c5ce7, #a29bfe) !important; color: white !important;">
                    <i class="fas fa-paper-plane"></i> Send Now
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Account Approval Details Modal -->
<div id="approvalInfoModal" class="modal">
    <div class="modal-content" style="max-width: 500px; border-radius: 20px; overflow: hidden; background: white;">
        <div style="background: linear-gradient(135deg, var(--dark-brown), #3d2217); padding: 2.5rem 2rem; text-align: center; position: relative;">
            <div class="modal-close" onclick="closeModal('approvalInfoModal')" style="position: absolute; right: 20px; top: 20px; color: rgba(255,255,255,0.7); cursor: pointer;">
                <i class="fas fa-times"></i>
            </div>
            <div style="width: 130px; height: 130px; margin: 0 auto 1.2rem; position: relative;">
                <img id="approvalUserAvatar" src="" style="width: 100%; height: 100%; border-radius: 50%; border: 4px solid var(--primary-gold); object-fit: cover; background: white;">
                <div id="approvalUserBadge" style="position: absolute; bottom: 5px; right: 5px; background: var(--primary-gold); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; border: 2px solid white;">PENDING</div>
            </div>
            <h2 id="approvalUserFullName" style="color: var(--primary-gold); margin: 0; font-family: 'Playfair Display', serif; font-size: 1.8rem;">User Name</h2>
            <p id="approvalUserUsername" style="color: #c0b7af; margin: 8px 0 0;">@username</p>
        </div>
        <div style="padding: 2.5rem; background: white;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2.5rem;">
                <div class="info-item">
                    <label style="display: block; font-size: 0.7rem; color: #aaa; text-transform: uppercase;">Email Address</label>
                    <div id="approvalUserEmail" style="font-weight: 600; color: var(--dark-brown); font-size: 0.95rem;">-</div>
                </div>
                <div class="info-item">
                    <label style="display: block; font-size: 0.7rem; color: #aaa; text-transform: uppercase;">Role Designation</label>
                    <div id="approvalUserRole" style="font-weight: 600; color: var(--dark-brown); font-size: 0.95rem;">Customer</div>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn-action" id="approvalApproveBtn" style="flex: 2; background: var(--success); color: white;">Approve Account</button>
                <button class="btn-action" id="approvalRejectBtn" style="flex: 1; color: #dc3545; border: 1.5px solid #dc3545;">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- POS Modal -->
<div id="posModal" class="modal">
    <div class="modal-content" style="max-width: 900px; display: flex; flex-direction: column; height: 85vh; padding: 0;">
        <div class="modal-header" style="padding: 1rem 1.5rem; background: var(--dark-brown); color: var(--white);">
            <h3 class="modal-title" style="color: var(--primary-gold);">Walk-in POS</h3>
            <i class="fas fa-times modal-close" style="cursor:pointer;" onclick="closeModal('posModal')"></i>
        </div>
        <div style="display: flex; flex: 1; overflow: hidden; background: var(--light-bg);">
            <div style="flex: 2; padding: 1.5rem; overflow-y: auto; border-right: 1px solid var(--border-color);">
                <div id="posItemsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px;"></div>
            </div>
            <div style="flex: 1.2; display: flex; flex-direction: column; background: white; padding: 1rem;">
                <div style="flex: 1; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 1rem; padding: 10px;">
                    <ul id="posCartList" style="list-style: none; padding: 0; margin: 0; font-size: 0.9rem;"></ul>
                </div>
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom: 15px; font-size: 1.2rem;">
                        <strong>Total:</strong>
                        <strong id="posTotal">₱0.00</strong>
                    </div>
                    <button class="btn-submit" style="width: 100%;" onclick="window.posCheckout()">Create Order & Pay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shift Summary Modal -->
<div id="shiftSummaryModal" class="modal">
    <div class="modal-content" style="max-width: 400px; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="padding: 1.5rem; background: var(--dark-brown); color: white; text-align: center;">
            <h3 style="color: var(--primary-gold);">End of Shift Summary</h3>
            <p id="shiftDate" style="font-size: 0.8rem; opacity: 0.7;">Date</p>
        </div>
        <div style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Total Cash Sales:</span>
                <strong id="shiftCashTotal">₱0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span>Total GCash Sales:</span>
                <strong id="shiftGCashTotal">₱0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px; font-size: 1.2rem;">
                <strong>Grand Total:</strong>
                <strong id="shiftGrandTotal">₱0.00</strong>
            </div>
            <button class="btn-submit" style="margin-top: 1.5rem;" onclick="window.printShiftSummary()">Print Summary</button>
             <button class="btn-action" style="width: 100%; margin-top: 10px; background: #eee; color: #666;" onclick="closeModal('shiftSummaryModal')">Close</button>
        </div>
    </div>
</div>
