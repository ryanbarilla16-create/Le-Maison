// ==================== UTILITY FUNCTIONS ====================

/**
 * Update cart count in the navigation
 */
function updateCartCount() {
    fetch('/api/cart/view')
        .then(res => res.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.item_count || 0;
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2);
}

/**
 * Show notification/alert
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        border-radius: 5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone number
 */
function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]{10,}$/;
    return re.test(phone);
}

// ==================== EVENT LISTENERS ====================

// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Add slide-in/slide-out animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});

// ==================== FORM VALIDATION ====================

/**
 * Validate checkout form
 */
function validateCheckoutForm(formData) {
    const errors = [];
    
    // Validate customer name
    if (!formData.customer_name || formData.customer_name.trim().length < 2) {
        errors.push('Please enter a valid name');
    }
    
    // Validate phone
    if (!formData.customer_phone || !validatePhone(formData.customer_phone)) {
        errors.push('Please enter a valid phone number');
    }
    
    // Validate email if provided
    if (formData.customer_email && !validateEmail(formData.customer_email)) {
        errors.push('Please enter a valid email address');
    }
    
    // Validate payment method
    if (!formData.payment_method || !['cod', 'gcash'].includes(formData.payment_method)) {
        errors.push('Please select a payment method');
    }
    
    return errors;
}

/**
 * Validate reservation form
 */
function validateReservationForm(formData) {
    const errors = [];
    
    // Validate customer name
    if (!formData.customer_name || formData.customer_name.trim().length < 2) {
        errors.push('Please enter a valid name');
    }
    
    // Validate phone
    if (!formData.customer_phone || !validatePhone(formData.customer_phone)) {
        errors.push('Please enter a valid phone number');
    }
    
    // Validate email if provided
    if (formData.customer_email && !validateEmail(formData.customer_email)) {
        errors.push('Please enter a valid email address');
    }
    
    // Validate date
    const selectedDate = new Date(formData.reservation_date);
    const now = new Date();
    if (selectedDate <= now) {
        errors.push('Please select a date and time in the future');
    }
    
    // Validate number of guests
    const guests = parseInt(formData.number_of_guests);
    if (isNaN(guests) || guests < 1 || guests > 100) {
        errors.push('Number of guests must be between 1 and 100');
    }
    
    return errors;
}

// ==================== LOCAL STORAGE HELPERS ====================

/**
 * Save data to localStorage
 */
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (e) {
        console.error('Error saving to localStorage:', e);
        return false;
    }
}

/**
 * Get data from localStorage
 */
function getFromLocalStorage(key) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : null;
    } catch (e) {
        console.error('Error reading from localStorage:', e);
        return null;
    }
}

/**
 * Remove data from localStorage
 */
function removeFromLocalStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (e) {
        console.error('Error removing from localStorage:', e);
        return false;
    }
}

// ==================== API HELPERS ====================

/**
 * Fetch with error handling
 */
async function fetchWithError(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// ==================== CART HELPERS ====================

/**
 * Add item to cart with validation
 */
async function addItemToCart(itemId, quantity = 1, specialInstructions = '') {
    if (quantity < 1) {
        showNotification('Please enter a valid quantity', 'error');
        return false;
    }
    
    try {
        const response = await fetchWithError('/api/cart/add', {
            method: 'POST',
            body: JSON.stringify({
                item_id: itemId,
                quantity: parseInt(quantity),
                special_instructions: specialInstructions
            })
        });
        
        if (response.success) {
            updateCartCount();
            showNotification('Item added to cart!', 'success');
            return true;
        } else {
            showNotification(response.message || 'Error adding to cart', 'error');
            return false;
        }
    } catch (error) {
        showNotification('Error adding item to cart', 'error');
        return false;
    }
}

/**
 * Remove item from cart
 */
async function removeItemFromCart(itemId) {
    if (!confirm('Remove this item from your cart?')) {
        return false;
    }
    
    try {
        const response = await fetchWithError(`/api/cart/remove/${itemId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            updateCartCount();
            showNotification('Item removed from cart', 'success');
            return true;
        } else {
            showNotification(response.message || 'Error removing item', 'error');
            return false;
        }
    } catch (error) {
        showNotification('Error removing item from cart', 'error');
        return false;
    }
}

// ==================== CHECKOUT HELPERS ====================

/**
 * Process checkout
 */
async function processCheckout(checkoutData) {
    const errors = validateCheckoutForm(checkoutData);
    if (errors.length > 0) {
        showNotification(errors[0], 'error');
        return null;
    }
    
    try {
        const response = await fetchWithError('/api/checkout/process', {
            method: 'POST',
            body: JSON.stringify(checkoutData)
        });
        
        if (response.success) {
            // Save order info for receipt
            saveToLocalStorage('last_order_id', response.order_id);
            saveToLocalStorage('last_order_number', response.order_number);
            return response;
        } else {
            showNotification(response.message || 'Error processing checkout', 'error');
            return null;
        }
    } catch (error) {
        showNotification('Error during checkout', 'error');
        return null;
    }
}

// ==================== RESERVATION HELPERS ====================

/**
 * Check reservation availability
 */
async function checkAvailability(reservationDate, numberOfGuests) {
    try {
        const response = await fetchWithError('/api/reservations/check-availability', {
            method: 'POST',
            body: JSON.stringify({
                reservation_date: reservationDate,
                number_of_guests: numberOfGuests
            })
        });
        
        return response;
    } catch (error) {
        console.error('Error checking availability:', error);
        return null;
    }
}

/**
 * Create reservation
 */
async function createReservation(reservationData) {
    const errors = validateReservationForm(reservationData);
    if (errors.length > 0) {
        showNotification(errors[0], 'error');
        return null;
    }
    
    try {
        const response = await fetchWithError('/api/reservations/create', {
            method: 'POST',
            body: JSON.stringify(reservationData)
        });
        
        if (response.success) {
            saveToLocalStorage('last_reservation_number', response.reservation_number);
            return response;
        } else {
            showNotification(response.message || 'Error creating reservation', 'error');
            return null;
        }
    } catch (error) {
        showNotification('Error creating reservation', 'error');
        return null;
    }
}

// ==================== ORDER TRACKING ====================

/**
 * Track order by order number
 */
async function trackOrder(orderNumber) {
    if (!orderNumber || orderNumber.trim().length === 0) {
        showNotification('Please enter an order number', 'error');
        return null;
    }
    
    try {
        const response = await fetchWithError(`/api/checkout/order/${orderNumber}`);
        
        if (response.success) {
            return response.order;
        } else {
            showNotification('Order not found', 'error');
            return null;
        }
    } catch (error) {
        showNotification('Error tracking order', 'error');
        return null;
    }
}

// ==================== MENU HELPERS ====================

/**
 * Load menu items
 */
async function loadMenuItems(categoryId = null) {
    try {
        const url = categoryId 
            ? `/api/items/category/${categoryId}`
            : '/api/items';
        
        const response = await fetchWithError(url);
        
        if (response.success) {
            return response.data;
        } else {
            console.error('Error loading menu items:', response.message);
            return [];
        }
    } catch (error) {
        console.error('Error loading menu items:', error);
        return [];
    }
}

/**
 * Load categories
 */
async function loadCategories() {
    try {
        const response = await fetchWithError('/api/categories');
        
        if (response.success) {
            return response.data;
        } else {
            console.error('Error loading categories:', response.message);
            return [];
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        return [];
    }
}

// ==================== EXPORT FUNCTIONS FOR GLOBAL USE ====================

// Note: These functions are defined above and are globally accessible
// They can be called from HTML onclick handlers or other scripts
