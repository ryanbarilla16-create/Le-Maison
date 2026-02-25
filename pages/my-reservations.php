<?php
// My Reservations - Customer Reservation Tracking
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Le Maison de Yelo Lane</title>
    
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
            --beige: #EFE6D5;
            --white: #FFFFFF;
            --glass: rgba(255, 255, 255, 0.9);
            --shadow: 0 10px 30px rgba(44, 24, 16, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Homepage Consistent Background */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?q=80&w=2070&auto=format&fit=crop');
            
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding: 2rem;
        }

        h1, h2, .res-id {
            font-family: 'Playfair Display', serif;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .header h1 {
            font-size: 3.5rem;
            color: #D4AF37; /* Gold */
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .header p {
            color: #f0f0f0;
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2rem;
            color: var(--dark-brown);
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            padding: 0.8rem 1.5rem;
            background: var(--white);
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .back-link:hover {
            background: var(--primary-gold);
            color: white;
            transform: translateX(-5px);
        }

        .reservations-grid {
            display: grid;
            gap: 2.5rem;
        }

        /* Transparent Card Style */
        .reservation-card {
            background-color: transparent !important;
            border: 1px solid #D4AF37 !important;
            border-radius: 0px;
            padding: 0;
            box-shadow: none !important;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .reservation-card:hover {
            transform: translateY(-5px);
            background: rgba(0,0,0,0.2) !important;
        }

        .res-header {
            padding: 1.5rem 2rem;
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .res-id {
            font-size: 1.4rem;
            color: #D4AF37;
            font-weight: 700;
        }

        .res-status {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-pending { background: #FFF3CD; color: #856404; border: 1px solid #FFEeba; }
        .status-confirmed { background: #D1E7DD; color: #0F5132; border: 1px solid #BADBCC; }
        .status-cancelled { background: #F8D7DA; color: #721C24; border: 1px solid #F5C6CB; }
        .status-completed { background: #D4EDDA; color: #155724; border: 1px solid #C3E6CB; }

        .res-body {
            padding: 2rem;
        }

        .res-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #ccc;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 1.1rem;
        }

        .meta-value i {
            color: var(--primary-gold);
        }

        .notes-section {
            background: rgba(201, 169, 97, 0.1);
            padding: 1rem;
            border-left: 4px solid var(--primary-gold);
            border-radius: 4px;
            margin-top: 1rem;
        }

        .notes-label {
            font-weight: 700;
            color: #D4AF37;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notes-text {
            color: #eee;
            font-style: italic;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--glass);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--primary-gold);
            margin-bottom: 1.5rem;
            opacity: 0.3;
        }

        .empty-state h2 {
            font-size: 2rem;
            color: var(--dark-brown);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .btn-primary {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, var(--dark-brown), #1a0f0a);
            color: var(--primary-gold);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-gold);
            color: var(--dark-brown);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(201, 169, 97, 0.3);
        }

        .loading {
            text-align: center;
            padding: 3rem;
        }

        .loading i {
            font-size: 3rem;
            color: var(--primary-gold);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .header h1 { font-size: 2.5rem; }
            .res-meta { grid-template-columns: 1fr; gap: 1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="header">
            <h1>My Reservations</h1>
            <p>View your table bookings</p>
        </div>

        <div id="reservationsContainer">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading your reservations...</p>
            </div>
        </div>
    </div>


</body>
</html>

