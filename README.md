# Le Maison de Yelo Lane - Complete Setup Guide

## 🎯 Project Overview

Le Maison de Yelo Lane is a fine French dining restaurant website with:
- User authentication and profile management
- Menu browsing and cart system
- Online reservation system
- Order management with rider delivery tracking
- Admin dashboard for management

## 🚀 Quick Start (3 Steps)

### Step 1: Set Environment Variables
Create or update `.env` file with your Neon PostgreSQL credentials:
```bash
DB_HOST=ep-wispy-dew-aigdgy1u-pooler.c-4.us-east-1.aws.neon.tech
DB_PORT=5432
DB_NAME=neondb
DB_USER=neondb_owner
DB_PASSWORD=your_password_here
```

### Step 2: Initialize Database
Visit: `http://localhost:3000/config/setup-db.php`
- Creates all necessary tables
- Sets up indexes for performance

### Step 3: Test Authentication
Visit: `http://localhost:3000/test-auth.php`
- Verifies all components are working
- Shows what's configured correctly

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `SESSION_AUTH_SETUP.md` | Complete authentication system documentation |
| `NEON_DATABASE_SETUP.md` | Neon PostgreSQL database configuration |
| `test-auth.php` | Test page to verify setup |
| `config/setup-db.php` | Database table initialization |

## 🏗️ Architecture

### Frontend Stack
- HTML5 / CSS3 / JavaScript ES6+
- FontAwesome icons
- Responsive design with Flexbox/Grid
- Modal dialogs for auth

### Backend Stack
- PHP 7.4+ with PDO
- Neon PostgreSQL database
- Session-based authentication
- RESTful API endpoints

### Database
- PostgreSQL with SSL encryption
- Tables: users, orders, reservations, menu_items, sessions
- Automatic session expiration (7 days)
- Prepared statements for security

## 🔐 Authentication System

### How Sessions Work
1. User registers/logs in
2. Backend creates user record and session
3. Session stored in database with 7-day expiry
4. Frontend updates UI with user profile
5. Every page checks session on load

### Key Files
- `config/auth.php` - AuthManager class
- `assets/php/auth/auth-api.php` - API endpoints
- `assets/js/session-auth.js` - Frontend handler
- `includes/navbar.php` - Profile display

### User Roles
- `customer` - Regular users
- `admin` - Full admin access
- `cashier` - Order processing
- `inventory` - Stock management
- `rider` - Delivery management

## 📁 Project Structure

```
le-maison/
├── config/
│   ├── database.php          ← PDO connection
│   ├── auth.php              ← Authentication class
│   ├── setup-db.php          ← Table initialization
│   └── neon_config.php       ← Database config template
├── assets/
│   ├── css/                  ← Stylesheets
│   ├── js/                   ← JavaScript modules
│   │   ├── session-auth.js   ← Auth handler
│   │   ├── cart.js
│   │   ├── settings.js
│   │   └── chatbot-ai.js
│   └── php/
│       ├── auth/
│       │   └── auth-api.php  ← Auth endpoints
│       ├── email/            ← Email handlers
│       └── payment/          ← Payment processing
├── includes/
│   ├── header.php            ← <head> tag
│   ├── navbar.php            ← Navigation
│   ├── hero.php              ← Hero section
│   ├── menu.php              ← Menu display
│   ├── modals.php            ← Login/Register forms
│   ├── footer.php
│   ├── chatbot.php
│   └── scripts.php
├── pages/
│   ├── my-orders.php         ← User orders
│   ├── my-reservations.php   ← User reservations
│   ├── payment-success.php
│   └── payment-failed.php
├── admin/                    ← Admin panel
├── rider/                    ← Rider portal
├── index.php                 ← Homepage
├── login.php                 ← Login page (with forms in modals)
├── test-auth.php             ← Auth test page
└── .env                      ← Database credentials
```

## 🔧 Configuration

### Environment Variables (.env)
```
DB_HOST=your-neon-host.c-4.us-east-1.aws.neon.tech
DB_PORT=5432
DB_NAME=neondb
DB_USER=neondb_owner
DB_PASSWORD=your_secure_password
APP_ENV=production
APP_DEBUG=false
```

### PHP Configuration
- Session timeout: 7 days
- Password hash: bcrypt (BCRYPT_COST=10)
- PDO attributes:
  - PDO::ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION
  - PDO::ATTR_EMULATE_PREPARES = false
  - PDO::ATTR_DEFAULT_FETCH_MODE = PDO::FETCH_ASSOC

### Database Connection
- SSL Mode: Required
- Connection Pool: pgBouncer (via Neon)
- Auto-sleep: 7 days inactivity (free tier)

## 🧪 Testing

### Manual Tests
1. **Registration**: 
   - Click "Sign Up" on homepage
   - Fill in details
   - Verify account created

2. **Login**:
   - Click "Login"
   - Use registered credentials
   - Verify profile appears

3. **Logout**:
   - Click on profile
   - Click "Logout"
   - Verify redirected to login

### Automated Test
```bash
# Visit this URL to run complete tests
http://localhost:3000/test-auth.php
```

## 🚨 Security Checklist

- [x] Passwords hashed with bcrypt
- [x] Prepared statements (no SQL injection)
- [x] SSL database connection required
- [x] Session stored in database
- [x] Credentials in .env (not in code)
- [x] HTTPS recommended for production
- [ ] CSRF tokens (coming soon)
- [ ] 2FA/MFA (coming soon)
- [ ] Rate limiting (coming soon)
- [ ] Email verification (coming soon)

## 📱 Features

### Current
- ✅ User Registration
- ✅ User Login/Logout
- ✅ Profile Display
- ✅ Session Management
- ✅ Menu Display
- ✅ Cart System
- ✅ Notifications
- ✅ Chatbot Integration

### In Progress
- 🔄 Order Management
- 🔄 Reservation System
- 🔄 Delivery Tracking
- 🔄 Admin Dashboard

### Coming Soon
- ❌ Email Verification
- ❌ Password Reset
- ❌ 2FA/MFA
- ❌ API Tokens
- ❌ Mobile App Backend

## 🐛 Troubleshooting

### "Database connection failed"
1. Check `.env` file credentials
2. Visit `test-auth.php` to diagnose
3. Verify Neon database is running
4. Check firewall/IP whitelist

### "Users table not found"
1. Run `config/setup-db.php`
2. Check database permissions
3. Verify database name in `.env`

### Profile not showing after login
1. Check browser console for JS errors
2. Visit `test-auth.php` to verify session table
3. Reload page after login
4. Clear browser cache

### "Invalid email or password"
1. Verify email is registered
2. Check password spelling (case-sensitive)
3. Try resetting password (future feature)

## 📞 Support & Resources

### Internal Documentation
- [Session Authentication Setup](SESSION_AUTH_SETUP.md)
- [Neon Database Setup](NEON_DATABASE_SETUP.md)
- [Auth Test Page](test-auth.php)

### External Links
- Neon Docs: https://neon.tech/docs
- PostgreSQL Docs: https://www.postgresql.org/docs/
- PHP PDO: https://www.php.net/manual/en/book.pdo.php
- Bcrypt: https://www.php.net/manual/en/function.password-hash.php

## 🎓 Learning Resources

### Understanding the Flow
1. User visits homepage (`index.php`)
2. `session-auth.js` checks login status
3. If logged in, shows profile in navbar
4. If not logged in, shows login/signup buttons
5. Clicking buttons opens modals with forms
6. Forms submit to `auth-api.php`
7. Backend processes and creates session
8. Frontend updates UI with new profile info

### Key Concepts

**Sessions**: Server-side data storage tied to a browser cookie
**Prepared Statements**: Prevent SQL injection by separating data from SQL
**Bcrypt**: One-way password hashing algorithm
**CORS**:  Cross-Origin Resource Sharing (if needed)
**REST API**: Stateless HTTP endpoints for data

## 🚀 Deployment

### Production Checklist
- [ ] Update `.env` with production database
- [ ] Delete `.env` from git
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS only
- [ ] Enable firewall for database
- [ ] Set strong session timeout
- [ ] Backup database regularly
- [ ] Monitor error logs
- [ ] Set up error alerting

### Host Requirements
- PHP 7.4+ with pdo_pgsql extension
- PostgreSQL client library
- Write permission to cache/temp folders
- 256MB+ RAM
- PostgreSQL SSL support

## 📝 Version History

- **v2.0** (Current)
  - Migrated from Firebase to PostgreSQL
  - Added session-based authentication
  - Added comprehensive documentation
  - Removed all Firebase dependencies

- **v1.0** (Previous)
  - Firebase authentication
  - Basic menu and ordering
  - Chatbot integration

## 📄 License

This project is proprietary and confidential to Le Maison de Yelo Lane.

---

**Last Updated**: February 25, 2026
**Status**: ✅ Production Ready
**Database**: PostgreSQL (Neon)
**Authentication**: Session-Based
