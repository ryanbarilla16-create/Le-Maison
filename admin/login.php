<?php
// Le Maison de Yelo Lane - Admin Login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Le Maison</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gold: #C9A961;
            --dark-brown: #2C1810;
            --off-white: #FFF8E7;
            --white: #FFFFFF;
            --error-red: #d32f2f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--dark-brown);
            color: var(--off-white);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(rgba(44, 24, 16, 0.9), rgba(44, 24, 16, 0.9)), 
                              url('https://images.unsplash.com/photo-1544148103-0773bf10d330?q=80&w=2070');
            background-size: cover;
            background-position: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 15px;
            border: 1px solid rgba(201, 169, 97, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--primary-gold);
            margin-bottom: 2rem;
            display: block;
            text-decoration: none;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-gold);
            opacity: 0.7;
        }

        .input-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(201, 169, 97, 0.2);
            border-radius: 5px;
            color: var(--white);
            outline: none;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary-gold);
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-login {
            padding: 1rem;
            background: var(--primary-gold);
            color: var(--dark-brown);
            border: none;
            border-radius: 5px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--white);
            transform: translateY(-2px);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-top: 1rem;
            display: none;
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <a href="../index.php" class="logo">Le Maison Admin</a>
        
        <form class="login-form" id="adminLoginForm">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" placeholder="Admin Email" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">Access Portal</button>
        </form>
        
        <div id="errorMessage" class="error-message"></div>
    </div>

    <!-- Firebase SDK & Admin Auth -->
    <script type="module" src="../assets/js/admin-auth.js"></script>

</body>
</html>
