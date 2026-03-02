<?php 
include '../includes/config.php'; 

// DELETE LOGIC
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM travel_orders WHERE id = $id");
    header("Location: manage.php?success=Record Deleted");
    exit();
}

$result = $conn->query("SELECT * FROM travel_orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin | Clean Data</title>
    <style>
        body { background: #0f172a; color: #fff; font-family: sans-serif; padding: 40px; }
        table { width: 100%; border-collapse: collapse; background: #1e293b; }
        th, td { padding: 12px; border: 1px solid #334155; text-align: left; }
        th { background: #334155; color: #38bdf8; }
        .btn-del { color: #f87171; text-decoration: none; font-weight: bold; border: 1px solid #f87171; padding: 5px 10px; border-radius: 4px; }
        .btn-del:hover { background: #f87171; color: #fff; }
    </style>
</head>
<body>
    <h2>Data Management (Clean Records)</h2>
    <table>
        <tr><th>Tracking ID</th><th>Passenger</th><th>Actions</th></tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['tracking_id'] ?></td>
            <td><?= $row['passenger_name'] ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('Delete this record?')">DELETE</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>