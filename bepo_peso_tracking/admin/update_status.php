<?php
include '../includes/config.php';
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $tracking_id = $conn->real_escape_string($_POST['tracking_id']);
    $current_office = $conn->real_escape_string($_POST['current_office']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $action = $_POST['action']; 

    // Workflow setup
    $workflow = ['PBMO', 'PHRMO', 'PADMO', 'GO'];
    $current_index = array_search($current_office, $workflow);

    if ($action == 'approve') {
        $status_update = "Approved & Forwarded";
        // Sunod nga opisina
        if ($current_index !== false && isset($workflow[$current_index + 1])) {
            $next_office = $workflow[$current_index + 1];
        } else {
            $next_office = 'COMPLETED'; 
        }
    } else {
        $status_update = "Returned to Previous";
        // Balik sa una
        $next_office = ($current_index > 0) ? $workflow[$current_index - 1] : 'PBMO';
    }

    // 1. Update Travel Order Table
    $update_sql = "UPDATE travel_orders 
                   SET current_office = '$next_office', 
                       updated_at = NOW() 
                   WHERE tracking_id = '$tracking_id'";

    if ($conn->query($update_sql)) {
        // 2. Insert into status_history (Basado sa imong SQL Table)
        $log_sql = "INSERT INTO status_history (tracking_id, office_name, status_update, remarks) 
                    VALUES ('$tracking_id', '$current_office', '$status_update', '$remarks')";
        
        $conn->query($log_sql);

        // Redirect balik
        header("Location: dashboard.php?office=$current_office&status=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: dashboard.php");
    exit();
}