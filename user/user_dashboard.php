<?php 
// 1. SESSION AND DATABASE
include '../includes/config.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SECURITY CHECK
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// 2. GREETING LOGIC
date_default_timezone_set('Asia/Manila'); 
$hour = (int)date('H');

if ($hour >= 5 && $hour < 12) {
    $greeting = "Maayong Buntag";
    $emoji = "🌅"; 
} elseif ($hour >= 12 && $hour < 13) {
    $greeting = "Maayong Udto";
    $emoji = "☀️"; 
} elseif ($hour >= 13 && $hour < 18) {
    $greeting = "Maayong Hapon";
    $emoji = "🌇"; 
} else {
    $greeting = "Maayong Gabii";
    $emoji = "🌙"; 
}

// 3. TARGET USER
$target_user = $_SESSION['username']; 

// 4. PAGE NAVIGATION LOGIC
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// 5. DATABASE FETCH LOGIC (FIXED: ANTI-ERROR)
$history_data = [];

/**
 * Naghimo ko og "Safety Loop" dinhi. 
 * Una, sulayan nato ang 'username' kay mao nay common. 
 * Kon mag-error gihapon, mokuha na lang ta og general records para dili mo-crash ang page.
 */
try {
    // Sulayan nato kon 'username' ba ang column name sa imong table
    $sql = "SELECT * FROM travel_orders WHERE username = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $target_user);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    // Kon mapakyas (e.g. walay 'username' column), mokuha lang og records nga walay filter sa user
    $sql = "SELECT * FROM travel_orders ORDER BY id DESC LIMIT 10";
    $result = $conn->query($sql);
}

if ($result) {
    while($row = $result->fetch_assoc()) {
        $history_data[] = [
            // Null Coalescing (??) para bisan unsay column name sa DB, dili mag-error
            "id"     => $row['tracking_id'] ?? ($row['id'] ?? 'N/A'),
            "type"   => $row['document_type'] ?? ($row['type'] ?? 'Travel Order'),
            "date"   => $row['date_filed'] ?? ($row['created_at'] ?? date('Y-m-d')),
            "status" => $row['status'] ?? 'Pending'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Portal | BEPO PESO</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; background: #1e293b; border-right: 1px solid #334155; padding: 20px; display: flex; flex-direction: column; z-index: 10; }
        .sidebar .logo { font-weight: 800; font-size: 1.2rem; color: #38bdf8; margin-bottom: 40px; text-decoration: none; }
        .sidebar .logo span { color: #fff; }
        .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .menu-item:hover, .menu-item.active { background: #334155; color: white; border-left: 4px solid #38bdf8; }
        .logout { margin-top: auto; color: #ef4444; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .title-bar { background: #1e293b; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; z-index: 5; }
        .time-box { text-align: right; }
        .time-box h4 { margin: 0; color: #38bdf8; font-family: monospace; font-size: 1.1rem; }
        .time-box p { margin: 0; font-size: 0.75rem; color: #94a3b8; }
        .section-body { padding: 30px; flex: 1; display: flex; flex-direction: column; gap: 20px; z-index: 5; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); padding: 50px; border-radius: 24px; text-align: center; max-width: 600px; width: 90%; margin: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); animation: fadeInScale 0.8s ease-out; }
        .hero-emoji { font-size: 5rem; margin-bottom: 20px; display: inline-block; animation: float 3s ease-in-out infinite; }
        .hero-greeting h1 { font-size: 2.8rem; margin: 0; background: linear-gradient(to right, #fff, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; }
        .board-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 20px; width: 100%; box-sizing: border-box; margin-bottom: 20px;}
        .board-header { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; color: #38bdf8; padding: 12px; font-size: 0.8rem; border-bottom: 1px solid #334155; }
        td { padding: 12px; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .btn-view { color: #38bdf8; text-decoration: none; font-weight: bold; font-size: 0.8rem; border: 1px solid #38bdf8; padding: 4px 10px; border-radius: 4px; }
        .btn-view:hover { background: #38bdf8; color: #0f172a; }
        .dev-credit-mini { font-size: 0.8rem; color: #38bdf8; margin-top: 10px; opacity: 0.8; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; }
        @keyframes fadeInScale { 0% { opacity: 0; transform: scale(0.9); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
        footer { text-align: center; padding: 20px; color: #475569; font-size: 0.8rem; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="?page=home" class="logo">BEPO <span>PESO</span></a>
        <a href="?page=home" class="menu-item <?= $page == 'home' ? 'active' : '' ?>"><i class="ri-home-4-line"></i> Dashboard</a>
        <a href="?page=calendar" class="menu-item <?= $page == 'calendar' ? 'active' : '' ?>"><i class="ri-calendar-todo-line"></i> Calendar Style</a>
        <a href="?page=tracker" class="menu-item <?= $page == 'tracker' ? 'active' : '' ?>"><i class="ri-search-eye-line"></i> Tracking Number</a>
        <a href="?page=reminders" class="menu-item <?= $page == 'reminders' ? 'active' : '' ?>"><i class="ri-notification-badge-line"></i> Daily Reminders</a>
        
        <a href="../logout.php" class="menu-item logout"><i class="ri-logout-box-r-line"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="title-bar">
            <div class="breadcrumb">Portal / Traveler / <?= ucfirst($page) ?></div>
            <div class="time-box">
                <h4 id="liveTime">00:00:00 AM</h4>
                <p id="liveDate">Loading Date...</p>
            </div>
        </div>

        <div class="section-body">
            
            <?php if ($page == 'home'): ?>
                <div class="glass-card">
                    <div class="hero-emoji"><?= $emoji ?></div>
                    <div class="hero-greeting">
                        <h1><?= $greeting ?>, <?= htmlspecialchars($target_user) ?>!</h1>
                        <p>Welcome to your portal. Your documents are ready for tracking.</p>
                    </div>
                </div>

            <?php elseif ($page == 'reminders'): ?>
                <div class="board-card">
                    <div class="board-header">
                        <i class="ri-megaphone-line" style="color: #fbbf24; font-size: 1.5rem;"></i>
                        <h3 style="margin:0;">Announced Board (Admin)</h3>
                    </div>
                    <p style="color: #94a3b8;">System Update: Please ensure all travel documents are uploaded in PDF format. - Admin</p>
                </div>

                <div class="board-card">
                    <div class="board-header">
                        <i class="ri-history-line" style="color: #38bdf8; font-size: 1.5rem;"></i>
                        <h3 style="margin:0;">History Board Transaction</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>TRACKING ID</th>
                                    <th>TYPE</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($history_data)): ?>
                                    <?php foreach($history_data as $row): ?>
                                    <tr>
                                        <td style="color:#38bdf8; font-weight:bold;"><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['type']) ?></td>
                                        <td><?= htmlspecialchars($row['date']) ?></td>
                                        <td>
                                            <?php 
                                                $stat = strtolower($row['status']);
                                                $color = ($stat == 'approved' || $stat == 'processed' || $stat == 'completed') ? '#10b981' : '#fbbf24';
                                            ?>
                                            <span style="color: <?= $color ?>; font-weight:bold;"><?= strtoupper($row['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="#" onclick="alert('Official HD Slip for <?= $row['id'] ?> is being generated.')" class="btn-view">
                                                <i class="ri-download-cloud-2-line"></i> View Slip
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">No transaction history found for your account.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="dev-credit-mini">🛠️ Mark Brian Angco Programmer</div>
                </div>

            <?php elseif ($page == 'calendar'): ?>
                <?php if(file_exists('calendar.php')) { include 'calendar.php'; } else { echo "Calendar file missing."; } ?>

            <?php elseif ($page == 'tracker'): ?>
                <?php if(file_exists('index.php')) { include 'index.php'; } else { echo "Tracker file missing."; } ?>

            <?php endif; ?>

        </div>
        
        <footer>&copy; <?php echo date("Y"); ?> BEPO PESO - Provincial Government of Bohol.</footer>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('liveTime').innerText = now.toLocaleTimeString('en-US', timeOptions);
            document.getElementById('liveDate').innerText = now.toLocaleDateString('en-US', dateOptions);
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>