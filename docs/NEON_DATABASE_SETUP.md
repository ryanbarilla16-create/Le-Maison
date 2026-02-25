# Neon PostgreSQL Setup Guide

## Quick Start

1. **Install PHP PostgreSQL Extension**
   ```bash
   # On Ubuntu/Debian
   sudo apt-get install php-pgsql
   
   # On macOS with Homebrew
   brew install php@8.2
   ```

2. **Set Environment Variables**
   
   Copy `.env.example` to `.env` and fill in your Neon credentials:
   ```bash
   cp .env.example .env
   ```

3. **Update `.env` with Your Credentials**
   ```
   DB_HOST=ep-wispy-dew-aigdgy1u-pooler.c-4.us-east-1.aws.neon.tech
   DB_PORT=5432
   DB_NAME=neondb
   DB_USER=neondb_owner
   DB_PASSWORD=your_password_here
   ```

4. **Test Connection**
   ```php
   $pdo = require_once 'config/database.php';
   echo "Successfully connected to Neon PostgreSQL!";
   ```

## Using in Your Application

### In any PHP file that needs database access:

```php
<?php
// Load the database connection
$pdo = require_once __DIR__ . '/config/database.php';

// Now use $pdo for queries
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => 'user@example.com']);
$result = $stmt->fetch();
?>
```

## Security Best Practices

⚠️ **IMPORTANT - YOUR PASSWORD WAS EXPOSED!**

You shared your database password publicly. Please:

1. **Immediately rotate your password in Neon Dashboard:**
   - Go to https://console.neon.tech
   - Project → Roles → neondb_owner
   - Click "Reset password"

2. **Never commit `.env` to version control**
   - Already added to `.gitignore`
   - Only commit `.env.example`

3. **Use environment variables in production:**
   ```bash
   # Set in server environment
   export DB_PASSWORD="new_secure_password"
   ```

4. **Use PDO with prepared statements:**
   ```php
   // GOOD - Prevents SQL injection
   $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
   $stmt->execute([$userId]);
   
   // BAD - DO NOT USE
   $pdo->query("SELECT * FROM users WHERE id = " . $_GET['id']);
   ```

## Creating Tables

See `config/database-examples.php` for SQL examples.

## Migration from Firebase

Since you removed Firebase, you'll need to:

1. Create PostgreSQL tables to replace Firestore collections
2. Update your application code to use PDO instead of Firebase SDKs
3. Implement authentication using sessions or JWT tokens

Example authentication flow:
- User logs in → Verify against `users` table → Create session/JWT
- Subsequent requests → Check session/JWT validity → Proceed

## Troubleshooting

**Connection refused:**
- Ensure your IP is whitelisted in Neon dashboard
- Check firewall settings
- Verify credentials are correct

**SSL certificate error:**
- Neon requires SSL connections
- Already configured in `config/database.php`
- If issues persist, check your PHP's SSL ca bundle

**Timeout errors:**
- Free tier databases sleep after 7 days of inactivity
- Access the database to wake it

## Support

- Neon Docs: https://neon.tech/docs
- PostgreSQL Docs: https://www.postgresql.org/docs/
- PHP PDO: https://www.php.net/manual/en/book.pdo.php
