<?php 
include '../includes/config.php'; 
include '../includes/functions.php'; 

// Mag-generate ta og ID daan para ipakita sa form
$generated_id = generateTrackingID();

if (isset($_POST['submit_order'])) {
    $tid = $_POST['tracking_id'];
    $name = $conn->real_escape_string($_POST['passenger_name']);
    // Gibutangan nato og "N/A" ang passport sa database kay gitangtang man nato sa form
    $passport = "N/A"; 
    $dest = $conn->real_escape_string($_POST['destination']);
    $date = $_POST['travel_date'];

    // 1. I-save sa travel_orders
    $sql = "INSERT INTO travel_orders (tracking_id, passenger_name, passport_no, destination, travel_date, current_office) 
            VALUES ('$tid', '$name', '$passport', '$dest', '$date', 'PBMO')";

    if ($conn->query($sql)) {
        // 2. I-record ang unang agi sa Indexing History
        logStatusHistory($conn, $tid, 'PESO', 'Order Created', 'Initial encoding and forwarded to PBMO for budget clearance.');
        
        header("Location: dashboard.php?msg=Success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Travel Order | BEPO PESO</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="form-wrapper glass-card" style="max-width: 600px; margin: 40px auto;">
            <header style="margin-bottom: 30px;">
                <h2 style="font-size: 1.5rem;">
                    <i class="ri-add-circle-fill" style="color: var(--neon);"></i> 
                    New <span style="color:var(--neon)">Travel Order</span>
                </h2>
            </header>
            
            <form method="POST">
                <div class="input-group">
                    <label style="color: var(--neon);">System Generated Tracking ID</label>
                    <input type="text" name="tracking_id" value="<?= $generated_id ?>" 
                           style="background: rgba(0, 242, 255, 0.05); border: 1px dashed var(--neon); color: var(--neon); font-weight: bold; letter-spacing: 1px;" readonly>
                </div>

                <div class="input-group">
                    <label>Passenger Full Name</label>
                    <input type="text" name="passenger_name" placeholder="Juan Dela Cruz" required>
                </div>

                <div class="input-group">
                    <label>Destination</label>
                    <input type="text" name="destination" placeholder="e.g. Manila, Philippines" required>
                </div>

                <div class="input-group">
                    <label>Target Travel Date</label>
                    <input type="date" name="travel_date" required>
                </div>

                <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 12px;">
                    <button type="submit" name="submit_order" class="btn-process">
                        SAVE & INDEX ORDER
                    </button>
                    
                    <a href="dashboard.php" style="text-align: center; color: var(--text-dim); text-decoration: none; font-size: 0.9rem; padding: 10px;">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>