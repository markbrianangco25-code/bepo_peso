<?php
/**
 * BEPO PESO System Functions
 */

// 1. Function para sa Pag-generate og Unique Tracking ID
function generateTrackingID() {
    $year = date("Y");
    // Nag-generate og 5-character random string (Alphanumeric)
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
    return "BEPO-" . $year . "-" . $random;
}

// 2. Function para sa Color-Coding sa Status
function getStatusBadge($status) {
    switch ($status) {
        case 'Confirmed':
            return '<span class="status-badge" style="background: rgba(16,185,129,0.2); color: #10b981;">Confirmed</span>';
        case 'Pending':
            return '<span class="status-badge" style="background: rgba(245,158,11,0.2); color: #f59e0b;">Pending</span>';
        case 'Cancelled':
            return '<span class="status-badge" style="background: rgba(239,68,68,0.2); color: #ef4444;">Cancelled</span>';
        default:
            return '<span class="status-badge">' . $status . '</span>';
    }
}

// 3. Function para sa Pag-identify sa Sunod nga Opisina (The Route)
function getNextOffice($currentOffice) {
    $route = [
        'PBMO'  => 'PHRMO',
        'PHRMO' => 'PADMO',
        'PADMO' => 'GO',
        'GO'    => 'COMPLETED'
    ];
    
    return isset($route[$currentOffice]) ? $route[$currentOffice] : 'COMPLETED';
}

// 4. Function para sa Pag-record sa History Log (Indexing)
function logStatusHistory($conn, $tracking_id, $office, $update, $remarks = "") {
    $tid = $conn->real_escape_string($tracking_id);
    $off = $conn->real_escape_string($office);
    $upd = $conn->real_escape_string($update);
    $rem = $conn->real_escape_string($remarks);
    
    $sql = "INSERT INTO status_history (tracking_id, office_name, status_update, remarks) 
            VALUES ('$tid', '$off', '$upd', '$rem')";
            
    return $conn->query($sql);
}

// 5. Function para sa Nice Date Format
function formatDate($date) {
    return date("M d, Y", strtotime($date));
}
?>