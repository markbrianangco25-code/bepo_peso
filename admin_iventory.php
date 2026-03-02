<?php
// admin_inventory.php

// 1. SESSION AND DATABASE CONNECTION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. GREETING LOGIC
date_default_timezone_set('Asia/Manila');
$hour = (int)date('H');
if ($hour >= 5 && $hour < 12) { $greeting = "Maayong Buntag"; $emoji = "🌅"; }
elseif ($hour >= 12 && $hour < 13) { $greeting = "Maayong Udto"; $emoji = "☀️"; }
elseif ($hour >= 13 && $hour < 18) { $greeting = "Maayong Hapon"; $emoji = "🌇"; }
else { $greeting = "Maayong Gabii"; $emoji = "🌙"; }

// 3. PAGE NAVIGATION
$page = 'inventory';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory | BEPO PESO</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; height: 100vh; overflow: hidden; }
        
        .sidebar { width: 260px; background: #1e293b; border-right: 1px solid #334155; padding: 20px; display: flex; flex-direction: column; z-index: 10; }
        .sidebar .logo { font-weight: 800; font-size: 1.2rem; color: #38bdf8; margin-bottom: 40px; text-decoration: none; }
        .sidebar .logo span { color: #fff; }
        .menu-item { display: flex; align-items: center; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .menu-item:hover, .menu-item.active { background: #334155; color: white; border-left: 4px solid #38bdf8; }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .title-bar { background: #1e293b; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; }

        .section-body { padding: 30px; display: flex; flex-direction: column; align-items: center; gap: 20px; }
        .form-card { 
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            padding: 40px; 
            border-radius: 24px; 
            max-width: 800px; 
            width: 100%; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .form-header { text-align: center; margin-bottom: 30px; }
        .form-header h2 { color: #38bdf8; margin: 0; font-size: 1.8rem; text-transform: uppercase; }
        .form-header p { color: #94a3b8; font-size: 0.9rem; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group label { color: #38bdf8; font-size: 0.8rem; font-weight: bold; }
        .input-group input, .input-group textarea {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid #334155;
            padding: 12px;
            border-radius: 8px;
            color: white;
            outline: none;
            transition: 0.3s;
        }
        .input-group input:focus { border-color: #38bdf8; box-shadow: 0 0 10px rgba(56, 189, 248, 0.2); }

        .item-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .item-table th { background: #334155; color: #38bdf8; padding: 10px; font-size: 0.8rem; text-align: left; }
        .item-table td { padding: 8px; border-bottom: 1px solid #334155; }
        .item-table input { background: transparent; border: 1px solid rgba(255,255,255,0.1); color: white; padding: 5px; border-radius: 4px; }

        .btn-save {
            background: #38bdf8;
            color: #0f172a;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            width: 100%;
            margin-top: 30px;
            font-size: 1rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-save:hover { background: #0ea5e9; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(56, 189, 248, 0.4); }

        /* COMING SOON SECTION STYLE */
        .coming-soon-box {
            margin-top: 20px;
            padding: 15px;
            border-top: 1px dashed rgba(56, 189, 248, 0.3);
            text-align: center;
            width: 100%;
        }
        .soon-badge {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid rgba(239, 68, 68, 0.3);
            display: inline-block;
            margin-bottom: 10px;
        }
        .soon-text {
            color: #94a3b8;
            font-size: 0.85rem;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="logo">BEPO <span>PESO</span></a>
        <a href="admin_dashboard.php" class="menu-item"><i class="ri-dashboard-line"></i> Dashboard</a>
        <a href="admin_inventory.php" class="menu-item active"><i class="ri-archive-line"></i> Inventory/RIS</a>
        <a href="../logout.php" class="menu-item logout" style="margin-top: auto; color: #ef4444;"><i class="ri-logout-box-r-line"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="title-bar">
            <div class="breadcrumb">Admin / Inventory / Add New RIS</div>
            <div class="time-box" style="text-align: right;">
                <h4 style="margin:0; color: #38bdf8;"><?= $greeting ?></h4>
                <p style="margin:0; font-size: 0.8rem; color: #94a3b8;"><?= date('F j, Y') ?></p>
            </div>
        </div>

        <div class="section-body">
            <div class="form-card">
                <div class="form-header">
                    <h2>Requisition and Issue Slip</h2>
                    <p>(E-PROCUREMENT SYSTEM)</p>
                </div>

                <form action="save_inventory.php" method="POST">
                    <div class="form-grid">
                        <div class="input-group">
                            <label>RIS NO.</label>
                            <input type="text" name="ris_no" placeholder="e.g. 2026-001" required>
                        </div>
                        <div class="input-group">
                            <label>FUND CLUSTER</label>
                            <input type="text" name="fund_cluster" value="Office Supplies Expenses">
                        </div>
                        <div class="input-group">
                            <label>RESPONSIBILITY CENTER CODE</label>
                            <input type="text" name="resp_center" value="07-151-00-8000-08">
                        </div>
                        <div class="input-group">
                            <label>DIVISION</label>
                            <input type="text" name="division" placeholder="Enter Division">
                        </div>
                        
                        <div class="full-width">
                            <label style="color: #38bdf8; font-weight: bold; font-size: 0.8rem;">REQUISITION ITEMS</label>
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th>STOCK NO.</th>
                                        <th>UNIT</th>
                                        <th>DESCRIPTION</th>
                                        <th>QTY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="stock_no[]" style="width: 100%;"></td>
                                        <td><input type="text" name="unit[]" style="width: 100%;"></td>
                                        <td><input type="text" name="description[]" style="width: 100%;"></td>
                                        <td><input type="number" name="qty[]" style="width: 100%;"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="input-group full-width">
                            <label>PURPOSES</label>
                            <textarea name="purpose" rows="2" placeholder="State the purpose of request..."></textarea>
                        </div>

                        <div class="input-group">
                            <label>REQUESTED BY</label>
                            <input type="text" name="requested_by" placeholder="Printed Name">
                        </div>
                        <div class="input-group">
                            <label>DESIGNATION</label>
                            <input type="text" name="designation" placeholder="Position">
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="ri-save-3-line"></i> SAVE TRANSACTION
                    </button>
                </form>

                <div class="coming-soon-box">
                    <div class="soon-badge">Coming Soon</div>
                    <div class="soon-text">
                        <i class="ri-printer-line"></i> Direct PDF Export & Digital Signature Approval System
                    </div>
                </div>
            </div>
            
            <footer style="color: #475569; font-size: 0.8rem;">&copy; 2026 BEPO PESO Inventory System</footer>
        </div>
    </div>
</body>
</html>