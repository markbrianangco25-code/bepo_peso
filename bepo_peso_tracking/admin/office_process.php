<?php
include '../includes/config.php';

if (isset($_POST['update_status'])) {
    $tid = $_POST['tracking_id'];
    $current = $_POST['current_office'];
    $remarks = $conn->real_escape_string($_POST['remarks']);

    // Routing Logic
    $next_office = '';
    $final_status = 'Pending';

    switch($current) {
        case 'PBMO': $next_office = 'PHRMO'; break;
        case 'PHRMO': $next_office = 'PADMO'; break;
        case 'PADMO': $next_office = 'GO'; break;
        case 'GO': 
            $next_office = 'COMPLETED'; 
            $final_status = 'Confirmed';
            break;
    }

    // 1. Update Main Table
    $conn->query("UPDATE travel_orders SET current_office = '$next_office', status = '$final_status' WHERE tracking_id = '$tid'");

    // 2. Insert into Indexing History
    $status_msg = "Approved and forwarded to $next_office";
    $conn->query("INSERT INTO status_history (tracking_id, office_name, status_update, remarks) 
                  VALUES ('$tid', '$current', '$status_msg', '$remarks')");

    header("Location: dashboard.php?office=$current&success=1");
}
?>