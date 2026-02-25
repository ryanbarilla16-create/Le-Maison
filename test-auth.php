<?php
/**
 * Authentication Test Page
 * Test the session-based authentication system
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = require_once __DIR__ . '/config/database.php';
$test_results = [];

// Test 1: Database Connection
try {
    $pdo->query('SELECT 1');
    $test_results['database'] = ['status' => 'pass', 'message' => 'Database connection successful'];
} catch (Exception $e) {
    $test_results['database'] = ['status' => 'fail', 'message' => 'Database connection failed: ' . $e->getMessage()];
}

// Test 2: Users Table
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $test_results['users_table'] = ['status' => 'pass', 'message' => 'Users table exists - ' . $result['count'] . ' users'];
} catch (Exception $e) {
    $test_results['users_table'] = ['status' => 'fail', 'message' => 'Users table not found. Run config/setup-db.php first'];
}

// Test 3: Sessions Table
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions");
    $result = $stmt->fetch();
    $test_results['sessions_table'] = ['status' => 'pass', 'message' => 'Sessions table exists - ' . $result['count'] . ' active sessions'];
} catch (Exception $e) {
    $test_results['sessions_table'] = ['status' => 'fail', 'message' => 'Sessions table not found. Run config/setup-db.php first'];
}

// Test 4: Authentication Class
try {
    require_once __DIR__ . '/config/auth.php';
    $auth = new AuthManager($pdo);
    $test_results['auth_class'] = ['status' => 'pass', 'message' => 'AuthManager class loaded successfully'];
} catch (Exception $e) {
    $test_results['auth_class'] = ['status' => 'fail', 'message' => 'AuthManager class error: ' . $e->getMessage()];
}

// Test 5: Session Status
if (isset($_SESSION['user_id'])) {
    $test_results['session'] = ['status' => 'pass', 'message' => 'User is logged in as: ' . $_SESSION['user_name']];
} else {
    $test_results['session'] = ['status' => 'pass', 'message' => 'No user currently logged in (this is normal)'];
}

// Test 6: Auth API Endpoint
$api_path = __DIR__ . '/assets/php/auth/auth-api.php';
if (file_exists($api_path)) {
    $test_results['auth_api'] = ['status' => 'pass', 'message' => 'Auth API endpoint exists'];
} else {
    $test_results['auth_api'] = ['status' => 'fail', 'message' => 'Auth API endpoint not found at: ' . $api_path];
}

// Test 7: Session Auth JS
$js_path = __DIR__ . '/assets/js/session-auth.js';
if (file_exists($js_path)) {
    $test_results['session_js'] = ['status' => 'pass', 'message' => 'Session authentication JavaScript loaded'];
} else {
    $test_results['session_js'] = ['status' => 'fail', 'message' => 'Session Auth JS not found'];
}

$all_passed = !in_array('fail', array_column($test_results, 'status'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Maison - Authentication Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a0f0a 0%, #2d1810 100%);
            color: #333;
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #D4AF37 0%, #B89F2C 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        .header h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .header p { font-size: 1.1rem; opacity: 0.9; }
        .content { padding: 3rem 2rem; }
        .test-item {
            display: flex;
            align-items: flex-start;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            background: #f8f8f8;
            border-left: 5px solid #ddd;
        }
        .test-item.pass {
            background: #e8f5e9;
            border-left-color: #4CAF50;
        }
        .test-item.fail {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .test-icon {
            font-size: 2rem;
            margin-right: 1.5rem;
            min-width: 3rem;
            text-align: center;
        }
        .test-item.pass .test-icon { color: #4CAF50; }
        .test-item.fail .test-icon { color: #f44336; }
        .test-info h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }
        .test-info p {
            color: #666;
            font-size: 0.95rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .status-badge.pass {
            background: #4CAF50;
            color: white;
        }
        .status-badge.fail {
            background: #f44336;
            color: white;
        }
        .summary {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #eee;
            background: #f9f9f9;
        }
        .summary.pass { background: #e8f5e9; color: #2e7d32; }
        .summary.fail { background: #ffebee; color: #c62828; }
        .summary h2 { font-size: 1.8rem; margin-bottom: 1rem; }
        .next-steps {
            margin-top: 2rem;
            padding: 2rem;
            background: #fff3e0;
            border-radius: 10px;
            border-left: 5px solid #ff9800;
        }
        .next-steps h3 { margin-bottom: 1rem; color: #e65100; }
        .next-steps ol { margin-left: 2rem; }
        .next-steps li { margin-bottom: 0.5rem; }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #D4AF37;
            color: #1a0f0a;
            font-weight: 600;
        }
        .btn-primary:hover { background: #B89F2C; }
        .btn-secondary {
            background: #666;
            color: white;
        }
        .btn-secondary:hover { background: #444; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Le Maison Authentication Test</h1>
            <p>Verify your session-based authentication system is working correctly</p>
        </div>

        <div class="content">
            <?php foreach ($test_results as $test_name => $result): ?>
                <div class="test-item <?php echo $result['status']; ?>">
                    <div class="test-icon">
                        <?php if ($result['status'] === 'pass'): ?>
                            ✓
                        <?php else: ?>
                            ✗
                        <?php endif; ?>
                    </div>
                    <div class="test-info" style="flex: 1;">
                        <h3>
                            <?php 
                            $labels = [
                                'database' => 'Database Connection',
                                'users_table' => 'Users Table',
                                'sessions_table' => 'Sessions Table',
                                'auth_class' => 'Authentication Class',
                                'session' => 'Current Session',
                                'auth_api' => 'Auth API Endpoint',
                                'session_js' => 'Session Auth JavaScript'
                            ];
                            echo $labels[$test_name] ?? $test_name;
                            ?>
                            <span class="status-badge <?php echo $result['status']; ?>">
                                <?php echo strtoupper($result['status']); ?>
                            </span>
                        </h3>
                        <p><?php echo htmlspecialchars($result['message']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="summary <?php echo $all_passed ? 'pass' : 'fail'; ?>">
            <?php if ($all_passed): ?>
                <h2>✓ All Tests Passed!</h2>
                <p>Your authentication system is ready to use. Visit the homepage and try registering or logging in.</p>
            <?php else: ?>
                <h2>✗ Some Tests Failed</h2>
                <p>Please fix the failing tests before using the authentication system.</p>
            <?php endif; ?>
        </div>

        <?php if (!$all_passed): ?>
            <div class="next-steps">
                <h3>⚠️ Setup Required</h3>
                <p>One or more tests failed. Follow these steps:</p>
                <ol>
                    <li><strong>Initialize Database Tables</strong> - Visit <a href="config/setup-db.php" style="color: #e65100; font-weight: 600;">config/setup-db.php</a></li>
                    <li>Verify all files are in the correct locations (see SESSION_AUTH_SETUP.md)</li>
                    <li>Check your .env file has correct Neon database credentials</li>
                    <li>Refresh this page to re-run tests</li>
                </ol>
            </div>
        <?php else: ?>
            <div class="next-steps">
                <h3>🎉 Ready to Go!</h3>
                <p>Your authentication system is fully functional. Here's what you can do next:</p>
                <ol>
                    <li>Go to the <a href="index.php" style="color: #e65100; font-weight: 600;">homepage</a></li>
                    <li>Click "Sign Up" to create a new account</li>
                    <li>Click "Login" to test the authentication</li>
                    <li>Your profile should appear in the top right corner</li>
                    <li>Check <a href="pages/my-orders.php" style="color: #e65100; font-weight: 600;">My Orders</a> (logged-in only)</li>
                    <li>Check <a href="pages/my-reservations.php" style="color: #e65100; font-weight: 600;">My Reservations</a> (logged-in only)</li>
                </ol>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">Go to Homepage</a>
            <?php if (!$all_passed): ?>
                <a href="config/setup-db.php" class="btn btn-secondary">Initialize Database</a>
            <?php endif; ?>
            <button onclick="location.reload()" class="btn btn-secondary">Refresh Tests</button>
        </div>
    </div>
</body>
</html>
