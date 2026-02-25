<?php
session_start();

// Ensure the user actually went through the login process
if (!isset($_SESSION['2fa_expected_pin'])) {
    header('Location: index.php');
    exit();
}

$error = '';

// Handle PIN Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_pin = $_POST['pin'] ?? '';
    
    // BACKEND VALIDATION USING PHP
    if ($entered_pin === $_SESSION['2fa_expected_pin']) {
        $_SESSION['2fa_verified'] = true;
        
        // Role-based redirection
        $role = $_SESSION['2fa_role'] ?? 'customer';
        
        if ($role === 'admin' || $role === 'super_admin') {
            header('Location: admin/dashboard.php');
        } elseif ($role === 'cashier') {
            header('Location: admin/cashier_dashboard.php');
        } elseif ($role === 'inventory') {
            header('Location: admin/inventory_dashboard.php');
        } elseif ($role === 'rider') {
            header('Location: rider/portal.php');
        } else {
            // Customer
            header('Location: index.php');
        }
        exit();
    } else {
        $error = 'Incorrect PIN. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2-Step Verification - Le Maison</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
            --light-bg: #F7F5F2;
            --white: #ffffff;
            --text-dark: #2D2A26;
            --text-muted: #8C8278;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(44, 24, 16, 0.8), rgba(44, 24, 16, 0.9)), url('https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: var(--text-dark);
        }

        .verification-card {
            background: var(--white);
            padding: 3rem 2.5rem;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 450px;
            text-align: center;
            position: relative;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: var(--dark-brown);
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .icon-container {
            width: 70px;
            height: 70px;
            background: rgba(201, 169, 97, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--primary-gold);
            font-size: 2rem;
        }

        .pin-input-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 2rem;
        }

        .pin-digit {
            width: 50px;
            height: 60px;
            font-size: 2rem;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-weight: 600;
            color: var(--dark-brown);
            outline: none;
            transition: all 0.3s ease;
        }

        .pin-digit:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 10px rgba(201, 169, 97, 0.2);
        }

        .error-message {
            color: #d9534f;
            background: #fdf0f0;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: <?php echo empty($error) ? 'none' : 'block'; ?>;
        }

        .btn-submit {
            background: var(--dark-brown);
            color: var(--primary-gold);
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            background: var(--primary-gold);
            color: var(--dark-brown);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="verification-card">
        <div class="icon-container">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h2>2-Step Verification</h2>
        <p>Please enter the 4-digit PIN you set during registration to continue.</p>

        <?php if (!empty($error)): ?>
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Hidden input to store the actual combined PIN -->
            <input type="hidden" name="pin" id="actualPin">
            
            <div class="pin-input-container">
                <input type="password" class="pin-digit" maxlength="1" required>
                <input type="password" class="pin-digit" maxlength="1" required>
                <input type="password" class="pin-digit" maxlength="1" required>
                <input type="password" class="pin-digit" maxlength="1" required>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">Verify & Login</button>
        </form>
    </div>

    <!-- JavaScript to handle focus and input building -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.pin-digit');
            const hiddenPin = document.getElementById('actualPin');
            
            // Auto focus first input
            inputs[0].focus();

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    input.value = input.value.replace(/[^0-9]/g, ''); // Ensure only numbers
                    
                    if (input.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    updateHiddenInput();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && input.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            function updateHiddenInput() {
                let currentPin = '';
                inputs.forEach(i => currentPin += i.value);
                hiddenPin.value = currentPin;
            }

            document.querySelector('form').addEventListener('submit', (e) => {
                updateHiddenInput();
                if (hiddenPin.value.length !== 4) {
                    e.preventDefault();
                    alert("Please enter a valid 4-digit PIN.");
                }
            });
        });
    </script>
</body>
</html>
