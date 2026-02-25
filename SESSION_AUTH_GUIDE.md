# Session & Database Authentication System Setup

## Overview

Your Le Maison restaurant website now has:
- ✅ PostgreSQL Neon database integration
- ✅ Secure session-based authentication
- ✅ User profiles with role management
- ✅ Real-time database backend (no more Firebase)

## How to Use

### 1. Copy the SQL Schema to Neon Database

1. Go to https://console.neon.tech
2. Click your project → SQL Editor
3. Paste the entire contents of `NEON_FULL_SCHEMA.sql`
4. Execute all queries

### 2. Configure Environment Variables

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update `.env` with your Neon credentials:
   ```
   DB_HOST=ep-wispy-dew-aigdgy1u-pooler.c-4.us-east-1.aws.neon.tech
   DB_PORT=5432
   DB_NAME=neondb
   DB_USER=neondb_owner
   DB_PASSWORD=your_new_password_here
   ```

### 3. Update Your PHP Files

Add this to the top of `index.php` and any page that needs authentication:

```php
<?php
require_once __DIR__ . '/config/bootstrap.php';

// Now you have access to $auth and $session globally
$current_user = $auth->isUserAuthenticated() ? $auth->getCurrentUser() : null;
?>
```

### 4. Include the Navbar

Update your main layout to include the navbar:

```php
<?php require_once 'includes/navbar.php'; ?>
```

The navbar will automatically:
- Show user profile when logged in
- Show "Login/Sign Up" buttons when logged out
- Display role-specific menu items (Admin Dashboard, My Orders, etc.)

## File Structure

```
config/
├── database.php           # Database connection (PSO)
├── bootstrap.php          # Initialize app & load classes
├── SessionHandler.php     # Session management
└── AuthHandler.php        # Login/Register/Password

api/
└── auth.php              # Authentication API endpoints

includes/
├── navbar.php            # Main navigation with user profile
└── navbar-auth.js        # Dropdown & logout functionality

NEON_FULL_SCHEMA.sql      # Complete database schema
NEON_DATABASE_SETUP.md    # Setup guidance
```

## Login & Registration

### Admin Account (Default)
- **Email**: admin@lemaison.com
- **Password**: admin123
- **⚠️ CHANGE IMMEDIATELY**

### API Endpoints

#### Login
```javascript
fetch('api/auth.php?action=login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password123'
    })
})
.then(r => r.json())
.then(data => {
    if (data.success) {
        window.location.href = 'index.php';
    } else {
        alert(data.message);
    }
});
```

#### Register
```javascript
fetch('api/auth.php?action=register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'newuser@example.com',
        password: 'password123',
        first_name: 'John',
        last_name: 'Doe',
        phone: '555-1234'
    })
})
.then(r => r.json())
.then(data => console.log(data));
```

#### Get Current User
```javascript
fetch('api/auth.php?action=get-user')
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            console.log('Current user:', data.user);
        }
    });
```

#### Logout
```javascript
fetch('api/auth.php?action=logout', { method: 'POST' })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'index.php';
        }
    });
```

## Database Tables

### Users Table
- `id` - Primary key
- `email` - Unique email
- `password_hash` - Bcrypt hashed password
- `first_name`, `last_name`, `phone`
- `role` - customer, admin, rider, cashier, inventory
- `avatar_url` - Profile picture
- `is_active` - Account status
- `last_login` - Last login timestamp

### Sessions Table
- `id` - Session ID (128-byte hex)
- `user_id` - Foreign key to users
- `user_agent` - Browser info
- `ip_address` - Login IP address
- `expires_at` - Session expiration
- `is_active` - Active flag

### Other Tables
- **categories** - Menu categories
- **menu_items** - Restaurant menu
- **cart** - Shopping cart
- **orders** - Customer orders
- **order_items** - Items in orders
- **reservations** - Table reservations
- **reviews** - Customer reviews
- **rider_deliveries** - Delivery tracking
- **promotions** - Discount codes
- **audit_logs** - Change tracking

## User Roles

| Role | Access |
|------|--------|
| **customer** | Browse menu, place orders, make reservations, view orders |
| **admin** | Full admin dashboard, manage menu, manage users, view analytics |
| **rider** | View assigned deliveries, update delivery status, track GPS |
| **cashier** | Process payments, view sales |
| **inventory** | Manage menu items and stock |

## Security Features

✅ **Password Hashing**
- Uses bcrypt with cost 12
- Never stores plain text passwords

✅ **Session Security**
- Secure session IDs (64-byte random)
- HTTP-only cookies (JS cannot access)
- Secure flag (HTTPS only)
- SameSite=Strict (CSRF protection)
- Automatic expiration (24 hours)

✅ **Database Security**
- SSL/TLS connection required
- Prepared statements (SQL injection prevention)
- Parameterized queries

✅ **Input Validation**
- Email format validation
- Password strength requirements
- SQL injection protection

## Troubleshooting

### "Database connection failed"
- Check `.env` file exists and has correct credentials
- Verify Neon database is running
- Check if your IP is whitelisted in Neon

### "Session validation failed"
- Clear browser cookies and try again
- Sessions expire after 24 hours
- Check if `sessions` table exists in database

### "Invalid email or password"
- Password is case-sensitive
- Check capslock
- Verify user exists and is_active = true

## Next Steps

1. ✅ [x] Run SQL schema in Neon
2. ✅ [x] Update `.env` file
3. ✅ [x] Add bootstrap to index.php
4. ✅ [x] Update navbar in layout
5. [ ] Create login page with proper form
6. [ ] Create profile settings page
7. [ ] Implement order management UI
8. [ ] Add payment processing
9. [ ] Set up email notifications
10. [ ] Deploy to production

## Support

For issues, check:
- Database logs in Neon console
- PHP error logs on your server
- Browser console for JS errors
- `.env` file permissions (should be readable by PHP)

---

**Last Updated**: February 25, 2026
**Author**: Le Maison Development Team
