<?php
/**
 * Database Setup - Create Tables
 * Run this once to initialize the database
 */

// Load database connection
$pdo = require_once __DIR__ . '/database.php';

try {
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(255),
            phone VARCHAR(20),
            avatar_url TEXT,
            role VARCHAR(50) DEFAULT 'customer',
            address TEXT,
            city VARCHAR(100),
            barangay VARCHAR(100),
            street VARCHAR(255),
            status VARCHAR(50) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id SERIAL PRIMARY KEY,
            customer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            order_number VARCHAR(50) UNIQUE NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            total_amount DECIMAL(10, 2) NOT NULL,
            delivery_address TEXT,
            special_notes TEXT,
            delivery_method VARCHAR(50),
            rider_id INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create reservations table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reservations (
            id SERIAL PRIMARY KEY,
            customer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            date_time TIMESTAMP NOT NULL,
            party_size INTEGER NOT NULL,
            special_requests TEXT,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create menu_items table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS menu_items (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            category VARCHAR(100),
            image_url TEXT,
            available BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create sessions table for session storage
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessions (
            id VARCHAR(255) PRIMARY KEY,
            user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
            data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL
        )
    ");
    
    // Create indexes for better performance
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_orders_customer ON orders(customer_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_reservations_customer ON reservations(customer_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_sessions_expires ON sessions(expires_at)");
    
    echo "✅ Database tables created successfully!";
    
} catch (PDOException $e) {
    error_log('Database setup error: ' . $e->getMessage());
    echo "Error setting up database: " . htmlspecialchars($e->getMessage());
}

?>
