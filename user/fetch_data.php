<?php
include '../includes/config.php';
include '../includes/functions.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $tid = $conn->real_escape_string($_GET['id']);
    
    // 1. Kuhaon ang Main Travel Order details
    $order_sql = "SELECT * FROM travel_orders WHERE tracking_id = '$tid'";
    $order_res = $conn->query($order_sql);
    
    if ($order_res->num_rows > 0) {
        $order = $order_res->fetch_assoc();
        
        // Formatting fields using our functions
        $order['formatted_date'] = formatDate($order['travel_date']);
        $order['status_badge'] = getStatusBadge($order['status']);

        // 2. Kuhaon ang Indexing History (Timeline)
        $history_sql = "SELECT * FROM status_history WHERE tracking_id = '$tid' ORDER BY processed_at DESC";
        $history_res = $conn->query($history_sql);
        $history = [];
        
        while($row = $history_res->fetch_assoc()) {
            $row['formatted_time'] = date("M d, Y - h:i A", strtotime($row['processed_at']));
            $history[] = $row;
        }

        echo json_encode([
            'success' => true,
            'order' => $order,
            'history' => $history
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tracking ID not found.']);
    }
}
?>