<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEPO PESO | Portal Selection</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .portal-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            padding: 20px;
        }
        .portal-card {
            flex: 1;
            max-width: 400px;
            text-align: center;
            padding: 50px 30px;
            cursor: pointer;
            transition: transform 0.3s, border-color 0.3s;
            text-decoration: none;
            color: white;
        }
        .portal-card:hover {
            transform: translateY(-10px);
            border-color: var(--neon);
            box-shadow: 0 0 30px rgba(0, 242, 255, 0.2);
        }
        .portal-card i {
            font-size: 60px;
            color: var(--neon);
            margin-bottom: 20px;
            display: block;
        }
        .portal-card h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        .portal-card p {
            color: var(--text-muted);
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="portal-wrapper">
        <a href="user/index.php" class="portal-card glass-card">
            <i class="ri-user-search-line"></i>
            <h2>Traveler Portal</h2>
            <p>Track your Travel Order real-time. Check history from PBMO, PHRMO, PADMO, and GO.</p>
        </a>

        <a href="admin/dashboard.php" class="portal-card glass-card">
            <i class="ri-admin-line"></i>
            <h2>Admin/Staff Portal</h2>
            <p>Management hub for encoding, indexing, and office routing (Budget to Governor's Approval).</p>
        </a>

        <a href="admin_manage.php" class="portal-card glass-card">
            <i class="ri-database-2-line"></i> <h2>Database Management</h2>
            <p>Management hub for encoding, monitoring, and cleaning the system database records.</p>
        </a>
    </div>

    <div style="position: absolute; bottom: 20px; width: 100%; text-align: center; color: var(--text-muted); font-size: 12px;">
        &copy; 2026 BEPO PESO - Travel Order Tracking System
    </div>

</body>
</html>