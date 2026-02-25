-- ============================================
-- LE MAISON DE YELO LANE - COMPLETE SQL SCHEMA
-- Neon PostgreSQL Database
-- ============================================

-- 1. USERS TABLE (Authentication & User Management)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    username VARCHAR(100),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role VARCHAR(50) DEFAULT 'customer', -- customer, admin, rider, cashier, inventory
    avatar_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- 2. SESSIONS TABLE (Session Management)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INTEGER NOT NULL,
    user_agent VARCHAR(500),
    ip_address VARCHAR(45),
    session_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- 3. CATEGORIES TABLE (Menu Categories)
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. MENU ITEMS TABLE
CREATE TABLE IF NOT EXISTS menu_items (
    id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500),
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    prep_time_minutes INTEGER DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category_id (category_id),
    INDEX idx_is_available (is_available)
);

-- 5. CART TABLE (Shopping Cart)
CREATE TABLE IF NOT EXISTS cart (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    menu_item_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    special_instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, menu_item_id)
);

-- 6. ORDERS TABLE
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    delivery_fee DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'pending', -- pending, confirmed, preparing, ready, out_for_delivery, delivered, cancelled
    delivery_type VARCHAR(50) DEFAULT 'pickup', -- pickup, delivery
    delivery_address TEXT,
    delivery_instructions TEXT,
    rider_id INTEGER,
    payment_method VARCHAR(50), -- credit_card, cash, online
    payment_status VARCHAR(50) DEFAULT 'pending', -- pending, completed, failed, refunded
    estimated_delivery_time TIMESTAMP,
    actual_delivery_time TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (rider_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_rider_id (rider_id)
);

-- 7. ORDER ITEMS TABLE
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    menu_item_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    special_instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id),
    INDEX idx_order_id (order_id)
);

-- 8. RESERVATIONS TABLE
CREATE TABLE IF NOT EXISTS reservations (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    reservation_number VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    guest_count INTEGER NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    special_requests TEXT,
    status VARCHAR(50) DEFAULT 'pending', -- pending, confirmed, completed, cancelled
    table_assigned VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_customer_id (customer_id),
    INDEX idx_reservation_date (reservation_date),
    INDEX idx_status (status)
);

-- 9. REVIEWS TABLE
CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    customer_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(200),
    comment TEXT,
    status VARCHAR(50) DEFAULT 'pending', -- pending, approved, rejected
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status)
);

-- 10. RIDERS/DELIVERY TABLE
CREATE TABLE IF NOT EXISTS rider_deliveries (
    id SERIAL PRIMARY KEY,
    rider_id INTEGER NOT NULL,
    order_id INTEGER NOT NULL,
    status VARCHAR(50) DEFAULT 'assigned', -- assigned, picked_up, in_transit, delivered, cancelled
    pickup_time TIMESTAMP,
    delivery_time TIMESTAMP,
    current_latitude DECIMAL(11, 8),
    current_longitude DECIMAL(11, 8),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rider_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_rider_id (rider_id),
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
);

-- 11. PROMOTIONS TABLE
CREATE TABLE IF NOT EXISTS promotions (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    discount_type VARCHAR(50), -- percentage, fixed_amount
    discount_value DECIMAL(10, 2) NOT NULL,
    min_order_amount DECIMAL(10, 2) DEFAULT 0,
    max_uses INTEGER DEFAULT -1, -- -1 for unlimited
    current_uses INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    valid_from TIMESTAMP NOT NULL,
    valid_until TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_is_active (is_active)
);

-- 12. AUDIT LOG TABLE (For tracking changes)
CREATE TABLE IF NOT EXISTS audit_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER,
    table_name VARCHAR(100) NOT NULL,
    action VARCHAR(50) NOT NULL, -- INSERT, UPDATE, DELETE
    record_id INTEGER NOT NULL,
    old_values JSONB,
    new_values JSONB,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
);

-- 13. SETTINGS TABLE (Application Configuration)
CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Insert Categories
INSERT INTO categories (name, description) VALUES
('All Day Breakfast', 'Our signature breakfast items available all day'),
('Mains', 'Main course dishes'),
('Appetizers', 'Starters and appetizers'),
('Desserts', 'Sweet treats and desserts'),
('Beverages', 'Hot and cold drinks')
ON CONFLICT DO NOTHING;

-- Insert Sample Menu Items
INSERT INTO menu_items (category_id, name, description, price, is_available, prep_time_minutes) VALUES
(1, 'Chicken Teriyaki Doria', 'Generous portion of succulent teriyaki chicken with fluffy white rice', 325.00, TRUE, 12),
(1, 'Pork Katsu Doria', 'Crispy breaded pork cutlet served with rice and katsu sauce', 295.00, TRUE, 10),
(1, 'Beef Stroganoff Doria', 'Tender beef in creamy mushroom sauce over rice', 375.00, TRUE, 15),
(2, 'Grilled Fish Fillet', 'Fresh daily catch, grilled to perfection', 425.00, TRUE, 18),
(2, 'Beef Tenderloin Steak', 'Premium cut, cooked to your preference', 550.00, TRUE, 20),
(3, 'Crispy Calamari', 'Tender squid rings, fried until golden', 225.00, TRUE, 8),
(3, 'Bruschetta Trio', 'Toasted bread with three delicious toppings', 185.00, TRUE, 5),
(4, 'Chocolate Lava Cake', 'Warm chocolate cake with molten center', 165.00, TRUE, 8),
(4, 'Crème Brûlée', 'Classic French dessert', 155.00, TRUE, 2),
(5, 'Café Au Lait', 'Rich French coffee with warm milk', 95.00, TRUE, 3),
(5, 'Fresh Lemonade', 'Refreshing house-made lemonade', 75.00, TRUE, 2)
ON CONFLICT DO NOTHING;

-- Insert Admin User (Password: admin123 - CHANGE THIS!)
INSERT INTO users (email, password_hash, username, first_name, last_name, role) VALUES
('admin@lemaison.com', '$2y$10$nOUIs5kJ7naTuTQqeS.2NO8/LewY5IiS8aN4VJKrJ7DYrB8tzugCi', 'admin', 'Admin', 'User', 'admin')
ON CONFLICT DO NOTHING;

-- ============================================
-- TRIGGERS & FUNCTIONS
-- ============================================

-- Function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Apply timestamp triggers to all tables with updated_at
CREATE TRIGGER update_users_timestamp BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

CREATE TRIGGER update_menu_items_timestamp BEFORE UPDATE ON menu_items
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

CREATE TRIGGER update_orders_timestamp BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

CREATE TRIGGER update_reservations_timestamp BEFORE UPDATE ON reservations
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

CREATE TRIGGER update_reviews_timestamp BEFORE UPDATE ON reviews
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

CREATE TRIGGER update_promotions_timestamp BEFORE UPDATE ON promotions
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

-- Function to generate order number
CREATE OR REPLACE FUNCTION generate_order_number()
RETURNS VARCHAR(50) AS $$
BEGIN
    RETURN 'ORD-' || TO_CHAR(CURRENT_TIMESTAMP, 'YYYYMMDD') || '-' || LPAD(NEXTVAL('order_number_seq')::TEXT, 5, '0');
END;
$$ LANGUAGE plpgsql;

-- Create sequence for order numbers
CREATE SEQUENCE IF NOT EXISTS order_number_seq START 1;

-- ============================================
-- NOTES FOR USE
-- ============================================
/*
1. ADMIN DEFAULT CREDENTIALS:
   Email: admin@lemaison.com
   Password: admin123
   ** CHANGE THIS IMMEDIATELY **

2. SESSIONS:
   - Sessions are stored in the sessions table
   - Set expires_at to SESSION_TIMEOUT (e.g., 24 hours)
   - Check is_active flag before allowing access
   - Clean up expired sessions periodically

3. PASSWORD HASHING:
   - Always use bcrypt (password_hash() in PHP)
   - Never store plain text passwords
   - Use PASSWORD_BCRYPT cost of 10 or higher

4. USER ROLES:
   - customer: Regular customer
   - admin: Full access
   - rider: Delivery rider
   - cashier: Payment processing
   - inventory: Manage menu items

5. ORDER STATUSES:
   - pending: Order received
   - confirmed: Confirmed by restaurant
   - preparing: Being prepared
   - ready: Ready for pickup/delivery
   - out_for_delivery: On the way
   - delivered: Completed
   - cancelled: Order cancelled

6. BACKUP REGULARLY
   - Use Neon's built-in backups
   - Set automated backup retention
*/
