<?php 
include 'includes/database.php'; 

// DELETE LOGIC
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM travel_orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        header("Location: admin_manage.php?status=deleted");
    }
    exit();
}

$all_data = $conn->query("SELECT * FROM travel_orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEPO Admin | Data Management</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --danger: #ef4444;
            --bg: #f8fafc;
            --text: #1e293b;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text); margin: 0; padding: 40px 20px; }

        .admin-window { 
            background: white; max-width: 1000px; margin: auto; padding: 30px; 
            border-radius: 12px; box-shadow: 0 10px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0; position: relative;
        }

        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; color: #64748b; padding: 15px; text-align: left; font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; }

        .btn-delete { 
            background: #fee2e2; color: var(--danger); text-decoration: none; 
            padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;
        }

        /* COMING SOON SECTION STYLE */
        .coming-soon-banner {
            margin-top: 30px;
            padding: 20px;
            background: #eff6ff;
            border: 1px dashed #3b82f6;
            border-radius: 10px;
            text-align: center;
        }

        .coming-soon-banner i { color: #3b82f6; font-size: 1.5rem; }
        .coming-soon-banner h4 { margin: 10px 0 5px 0; color: #1e3a8a; }
        .coming-soon-banner p { margin: 0; font-size: 0.85rem; color: #60a5fa; font-weight: 500; }

        .badge-dev {
            background: #3b82f6; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem; vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="admin-window">
    <div class="header-section">
        <div>
            <h2><i class="ri-shield-keyhole-line"></i> Admin Control Panel</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin: 5px 0 0 0;">Manage and purge system records.</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tracking ID</th>
                <th>Passenger Name</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $all_data->fetch_assoc()): ?>
            <tr>
                <td style="font-weight: bold; color: var(--primary);"><?= $row['tracking_id'] ?></td>
                <td><?= $row['passenger_name'] ?></td>
                <td style="text-align: right;">
                    <a href="admin_manage.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this record?')">
                        <i class="ri-delete-bin-line"></i> DELETE
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="coming-soon-banner">
        <i class="ri-lock-password-line"></i>
        <h4>Security Module <span class="badge-dev">IN DEVELOPMENT</span></h4>
        <p>For User / Admin Login Database Integration — Coming Soon</p>
    </div>
</div>

</body>
</html>