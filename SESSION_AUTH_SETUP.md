# Session-Based Authentication Setup Guide

## Overview

Your website has been migrated from Firebase to a session-based authentication system using Neon PostgreSQL. Users can now register, login, and maintain persistent sessions.

## Quick Start

### 1. Initialize the Database

Visit this URL once to create all necessary tables:

```
http://localhost:3000/config/setup-db.php
```

You should see: ✅ Database tables created successfully!

### 2. Test Authentication

- **Homepage**: `http://localhost:3000/index.php`
- **Register**: Click "Sign Up" button in the top right
- **Login**: Click "Login" button after registration
- **Profile Display**: Your profile should appear in the top right once logged in

## How It Works

### File Structure

```
config/
├── database.php          # PDO connection handler
├── auth.php             # AuthManager class for authentication
└── setup-db.php         # Database table initialization

assets/php/auth/
└── auth-api.php         # Backend API for login/register/logout

assets/js/
└── session-auth.js      # Frontend authentication handler

includes/
├── header.php           # Page header with session start
├── navbar.php           # Navigation with profile display
└── modals.php           # Login/Register forms
```

### Database Tables

#### users
```sql
- id (Primary Key)
- email (Unique)
- password_hash
- full_name
- phone
- avatar_url
- role (customer/admin/rider/etc)
- address, city, barangay, street
- status (active/inactive)
- created_at, updated_at
```

#### orders
```sql
- id (Primary Key)
- customer_id (Foreign Key → users)
- order_number
- status (pending/confirmed/delivered/etc)
- total_amount
- delivery_address
- special_notes
- delivery_method
- rider_id (Foreign Key → users)
- created_at, updated_at
```

#### reservations
```sql
- id (Primary Key)
- customer_id (Foreign Key → users)
- date_time
- party_size
- special_requests
- status (pending/confirmed/cancelled)
- created_at, updated_at
```

#### menu_items
```sql
- id (Primary Key)
- name
- description
- price
- category
- image_url
- available (boolean)
- created_at, updated_at
```

#### sessions
```sql
- id (Primary Key - session_id)
- user_id (Foreign Key → users)
- data (JSON serialized session data)
- created_at
- expires_at (7 days from creation)
```

## Authentication Flow

### Login Process

1. User clicks "Login" button
2. `session-auth.js` captures email and password
3. Form is submitted to `auth-api.php?action=login`
4. `AuthManager::login()` verifies credentials
5. Session is created and stored in database
6. User redirected to homepage
7. Profile appears in navbar

### Registration Process

1. User clicks "Sign Up"
2. Accepts terms and conditions
3. Fills in name, email, password
4. Form submitted to `auth-api.php?action=register`
5. `AuthManager::register()` creates user account
6. Password is hashed with bcrypt
7. Session is automatically created
8. User logged in immediately

### Logout Process

1. User clicks "Logout" in profile menu
2. `auth-api.php?action=logout` is called
3. Session record deleted from database
4. PHP session destroyed
5. Page reloads with login buttons showing

## API Endpoints

All endpoints are in `assets/php/auth/auth-api.php`

### Register
```bash
POST /assets/php/auth/auth-api.php
Parameters:
  - action=register
  - email=user@example.com
  - password=secure_password
  - full_name=John Doe

Response:
{
  "success": true,
  "user_id": 123
}
```

### Login
```bash
POST /assets/php/auth/auth-api.php
Parameters:
  - action=login
  - email=user@example.com
  - password=secure_password

Response:
{
  "success": true,
  "user_id": 123,
  "name": "John Doe",
  "email": "user@example.com",
  "role": "customer"
}
```

### Get Current User
```bash
GET /assets/php/auth/auth-api.php?action=getCurrentUser

Response:
{
  "success": true,
  "user": {
    "id": 123,
    "email": "user@example.com",
    "name": "John Doe",
    "avatar": null,
    "role": "customer"
  }
}
```

### Logout
```bash
GET /assets/php/auth/auth-api.php?action=logout

Response:
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Update Profile
```bash
POST /assets/php/auth/auth-api.php
Parameters:
  - action=updateProfile
  - full_name=Jane Doe (optional)
  - phone=+1234567890 (optional)
  - avatar_url=https://... (optional)
  - address=123 Main St (optional)
  - city=Paris (optional)
  - barangay=Barangay (optional)
  - street=Main Street (optional)

Response:
{
  "success": true
}
```

## Using in Your Pages

### Check if User is Logged In

```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    echo "Welcome, " . $_SESSION['user_name'];
} else {
    echo "Please login first";
}
?>
```

### Get Current User Info in JavaScript

```javascript
// The SessionAuth class is globally available as window.sessionAuth

// Check if logged in
if (window.sessionAuth.currentUser) {
    console.log("Logged in as:", window.sessionAuth.currentUser.name);
}

// Listen for authentication changes (manual implementation needed)
// You can extend SessionAuth to support event listeners
```

## Security Considerations

### Password Security
- Passwords are hashed using PHP's `password_hash()` with bcrypt algorithm
- Never store plain text passwords
- Always use `password_verify()` to check passwords

### Session Security
- Sessions expire after 7 days of inactivity
- Session data is stored in database
- CSRF tokens should be added to forms (future enhancement)

### Database Connection
- SSL is required for Neon connections
- Use environment variables for credentials
- Never commit `.env` file

### SQL Injection Prevention
- All queries use prepared statements with bound parameters
- PDO emulation is disabled for better security
- Use `:param` syntax for parameter binding

## Creating Admin Users

To create an admin user (currently must be done via database):

```sql
INSERT INTO users (email, password_hash, full_name, role, status)
VALUES (
  'admin@lemaisonxyz.ph',
  -- Use: echo password_hash('secure_password', PASSWORD_BCRYPT);
  '$2y$10$...',
  'Admin User',
  'admin',
  'active'
);
```

Or via PHP:

```php
<?php
require_once 'config/database.php';
require_once 'config/auth.php';

$auth = new AuthManager($pdo);

// This creates a customer by default
$result = $auth->register('admin@lemaisonxyz.ph', 'secure_password', 'Admin User');

if ($result['success']) {
    // Now update to admin
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
    $stmt->execute([':id' => $result['user_id']]);
    echo "Admin user created!";
}
?>
```

## Troubleshooting

### "Database connection failed"
- Check `.env` file has correct credentials
- Verify Neon database is accessible
- Run `config/setup-db.php` to initialize tables

### "Invalid email or password"
- Ensure email is registered
- Check password is correct
- Passwords are case-sensitive

### Profile not showing after login
- Check browser console for errors
- Verify `session-auth.js` is loading
- Make sure session is being created (`config/setup-db.php` run first)

### Sessions not persisting
- Check if cookies are enabled
- Verify database sessions table exists
- Check session timeout settings

## Next Steps

1. ✅ Database initialized
2. ✅ Authentication working
3. [] Add password reset functionality
4. [] Add email verification
5. [] Add 2FA/MFA support
6. [] Add API tokens for mobile app
7. [] Create admin dashboard
8. [] Add order management
9. [] Add reservation management
10. [] Add delivery rider management

## Support

For issues or questions:
1. Check the test endpoints in this guide
2. Review browser console for JavaScript errors
3. Check PHP error logs
4. Verify all files are in the correct locations
