<?php 
include '../includes/config.php'; 
include '../includes/functions.php'; 

$office_filter = isset($_GET['office']) ? $_GET['office'] : 'PBMO';
$view_id = isset($_GET['view_id']) ? $_GET['view_id'] : '';

// 1. Current Queue (Table sa taas)
$sql = "SELECT * FROM travel_orders WHERE current_office = '$office_filter' ORDER BY created_at DESC";
$result = $conn->query($sql);

// 2. Global Status Monitor (Table sa tunga)
$status_sql = "SELECT tracking_id, passenger_name, current_office, status FROM travel_orders ORDER BY updated_at DESC LIMIT 10";
$status_result = $conn->query($status_sql);

// 3. History Logic (Para sa table sa pinakaubos)
$history_result = null;
if (!empty($view_id)) {
    $view_id = $conn->real_escape_string($view_id);
    $history_sql = "SELECT * FROM status_history WHERE tracking_id = '$view_id' ORDER BY processed_at ASC"; 
    $history_result = $conn->query($history_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | BEPO PESO</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .section-title { font-size: 0.9rem; color: var(--neon); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .row-selected { background: rgba(0, 242, 255, 0.08) !important; border-left: 3px solid var(--neon); }
        .status-badge { font-size: 0.65rem; padding: 4px 8px; border-radius: 4px; font-weight: 800; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.1); }
        .badge-approved { background: rgba(16, 185, 129, 0.1); color: var(--success); border-color: var(--success); }
        .badge-returned { background: rgba(239, 68, 68, 0.1); color: var(--danger); border-color: var(--danger); }
        .badge-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); border-color: var(--warning); }
        .badge-completed { background: rgba(0, 242, 255, 0.1); color: var(--neon); border-color: var(--neon); }
        .clickable-id { color: var(--neon); text-decoration: none; font-weight: bold; font-family: monospace; transition: 0.3s; }
        .clickable-id:hover { text-shadow: 0 0 8px var(--neon); }
    </style>
</head>
<body>

    <div class="container">
        <header class="admin-actions">
            <div class="logo"><a href="dashboard.php" style="text-decoration: none; color: inherit;">BEPO <span>PESO</span></a></div>
            <a href="add_record.php" class="btn-add"><i class="ri-add-line"></i> ADD NEW ORDER</a>
        </header>

        <nav class="office-tabs">
            <a href="?office=PBMO" class="<?= $office_filter=='PBMO'?'active':'' ?>">PBMO</a>
            <a href="?office=PHRMO" class="<?= $office_filter=='PHRMO'?'active':'' ?>">PHRMO</a>
            <a href="?office=PADMO" class="<?= $office_filter=='PADMO'?'active':'' ?>">PADMO</a>
            <a href="?office=GO" class="<?= $office_filter=='GO'?'active':'' ?>">GO</a>
        </nav>

        <div class="glass-card">
            <h3 class="section-title"><i class="ri-list-check"></i> Current Queue: <?= $office_filter ?></h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr><th>Tracking ID</th><th>Passenger</th><th>Travel Date</th><th style="text-align: right;">Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="<?= $view_id == $row['tracking_id'] ? 'row-selected' : '' ?>">
                                <td>
                                    <a href="?office=<?= $office_filter ?>&view_id=<?= $row['tracking_id'] ?>#history-section" class="clickable-id">
                                        <?= $row['tracking_id'] ?> <i class="ri-history-line"></i>
                                    </a>
                                </td>
                                <td><?= $row['passenger_name'] ?></td>
                                <td style="color: var(--text-dim);"><?= date('M d, Y', strtotime($row['travel_date'])) ?></td>
                                <td style="text-align: right;">
                                    <a href="process_order.php?id=<?= $row['tracking_id'] ?>&office=<?= $office_filter ?>" class="btn-process">PROCESS</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass-card" style="margin-top: 25px; background: rgba(0,0,0,0.2);">
            <h3 class="section-title" style="color: #fff;"><i class="ri-shield-check-line"></i> Overall Status Monitor</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr style="background: rgba(255,255,255,0.03);">
                            <th>Tracking ID</th>
                            <th>Passenger</th>
                            <th>Current Location</th>
                            <th style="text-align: right;">Approval Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s_row = $status_result->fetch_assoc()): ?>
                        <tr class="<?= $view_id == $s_row['tracking_id'] ? 'row-selected' : '' ?>">
                            <td>
                                <a href="?office=<?= $office_filter ?>&view_id=<?= $s_row['tracking_id'] ?>#history-section" class="clickable-id">
                                    <?= $s_row['tracking_id'] ?> <i class="ri-history-line"></i>
                                </a>
                            </td>
                            <td><?= $s_row['passenger_name'] ?></td>
                            <td><span style="font-size: 0.8rem; font-weight: bold; color: var(--neon);"><?= $s_row['current_office'] ?></span></td>
                            <td style="text-align: right;">
                                <?php 
                                    $s_badge = 'badge-pending';
                                    $s_label = $s_row['status'];
                                    if($s_row['current_office'] == 'COMPLETED') { $s_badge = 'badge-completed'; $s_label = 'Final Approved'; }
                                    else if($s_row['status'] == 'Confirmed') { $s_badge = 'badge-approved'; }
                                ?>
                                <span class="status-badge <?= $s_badge ?>"><?= $s_label ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($view_id)): ?>
        <div id="history-section" class="glass-card" style="margin-top: 25px; border-top: 2px solid var(--neon); background: rgba(0, 242, 255, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 class="section-title" style="margin-bottom: 0;"><i class="ri-time-line"></i> Transaction History: <?= $view_id ?></h3>
                <a href="?office=<?= $office_filter ?>" style="color: var(--text-dim); font-size: 1.2rem; text-decoration: none;">&times; Close</a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr><th>Date & Time</th><th>Office</th><th>Action / Remarks</th><th style="text-align: right;">Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if($history_result && $history_result->num_rows > 0): ?>
                            <?php while($log = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-size: 0.8rem; color: var(--text-dim);"><?= date('M d, Y | g:ia', strtotime($log['processed_at'])) ?></td>
                                <td><strong><?= $log['office_name'] ?></strong></td>
                                <td style="font-size: 0.85rem; font-style: italic;"><?= htmlspecialchars($log['remarks']) ?></td>
                                <td style="text-align: right;">
                                    <?php $badge_class = (strpos(strtolower($log['status_update']), 'approved') !== false) ? 'badge-approved' : 'badge-returned'; ?>
                                    <span class="status-badge <?= $badge_class ?>"><?= $log['status_update'] ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">No history records found for this ID.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>