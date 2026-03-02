<?php 
include '../includes/config.php'; 
include '../includes/functions.php'; 

// 1. Siguradoon nato nga naay ID ug Office filter gikan sa URL
$id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
$office_filter = isset($_GET['office']) ? $_GET['office'] : 'PBMO';

// 2. Kuhaon ang Main Data sa Travel Order
$order = null;
if (!empty($id)) {
    $sql = "SELECT * FROM travel_orders WHERE tracking_id = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    }
}

// Kon walay nakit-an nga order, i-redirect balik sa dashboard para dili mag-error
if (!$order) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Order | BEPO PESO</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .info-group { margin-bottom: 15px; }
        .info-group label { color: var(--neon); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; }
        .info-group p { font-size: 1.1rem; font-weight: 600; color: #fff; margin: 5px 0 0 0; }
        .status-pill { padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .status-approve { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid var(--success); }
        .status-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid var(--warning); }
    </style>
</head>
<body>

    <div class="container">
        <header class="admin-actions">
            <div class="logo">
                <a href="dashboard.php" style="text-decoration: none; color: inherit;">
                    BEPO <span>PESO</span>
                </a>
            </div>
            <a href="dashboard.php?office=<?= $office_filter ?>" class="btn-cancel" style="border: 1px solid rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 12px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: var(--text-dim);">
                <i class="ri-arrow-left-s-line"></i> Back to List
            </a>
        </header>

        <div class="glass-card">
            <div class="detail-grid">
                <div class="info-group">
                    <label>Tracking Number</label>
                    <p style="font-family: monospace; color: var(--neon); font-size: 1.4rem;">#<?= htmlspecialchars($order['tracking_id']) ?></p>
                </div>
                <div class="info-group">
                    <label>Passenger Name</label>
                    <p><?= htmlspecialchars($order['passenger_name']) ?></p>
                </div>
                <div class="info-group">
                    <label>Destination</label>
                    <p><i class="ri-map-pin-2-line" style="color: var(--danger);"></i> <?= htmlspecialchars($order['destination']) ?></p>
                </div>
                <div class="info-group">
                    <label>Date Travel</label>
                    <p><i class="ri-calendar-line" style="color: var(--neon-blue);"></i> <?= formatDate($order['travel_date']) ?></p>
                </div>
            </div>
        </div>

        <div class="glass-card" style="margin-top: 20px;">
            <h3 style="font-size: 0.9rem; margin-bottom: 20px; color: var(--text-dim); text-transform: uppercase; display: flex; align-items: center; gap: 10px;">
                <i class="ri-git-commit-line"></i> Approval Trail
            </h3>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date | Time</th>
                            <th>Office</th>
                            <th>Remarks</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>-</td>
                            <td><strong style="color: var(--neon);"><?= htmlspecialchars($order['current_office']) ?></strong></td>
                            <td style="font-style: italic; color: var(--text-dim); font-size: 0.85rem;">Waiting for document review...</td>
                            <td style="text-align: right;">
                                <span class="status-pill status-pending">Pending</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 25px;">
                <form action="update_status.php" method="POST">
                    <input type="hidden" name="tracking_id" value="<?= htmlspecialchars($order['tracking_id']) ?>">
                    <input type="hidden" name="current_office" value="<?= htmlspecialchars($office_filter) ?>">
                    
                    <label style="color: var(--text-dim); font-size: 0.8rem;">UPDATE REMARKS FOR: <span style="color: var(--neon);"><?= htmlspecialchars($office_filter) ?></span></label>
                    <textarea name="remarks" placeholder="Add comment or status update..." rows="3" style="width: 100%; margin-top: 10px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; padding: 10px; outline: none;"></textarea>
                    
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <button type="submit" name="action" value="return" class="btn-cancel" style="flex: 1; margin: 0; background: rgba(239, 68, 68, 0.1); color: #ff4d4d; border: 1px solid rgba(239, 68, 68, 0.2); padding: 12px; border-radius: 8px; cursor: pointer;">
                            <i class="ri-reply-line"></i> RETURN / REJECT
                        </button>
                        <button type="submit" name="action" value="approve" class="btn-add" style="flex: 2; padding: 12px; border-radius: 8px; cursor: pointer; background: var(--neon); color: black; font-weight: bold; border: none;">
                            <i class="ri-check-double-line"></i> APPROVE & FORWARD
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer style="text-align: center; margin-top: 40px; padding-bottom: 40px; color: var(--text-dim); font-size: 0.75rem; opacity: 0.6;">
        &copy; 2026 BEPO PESO Tracking System
    </footer>

</body>
</html>