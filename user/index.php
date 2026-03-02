<?php include '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track My Travel Order | BEPO PESO</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* BALIK SA ORIGINAL NGA DESIGN STYLES */
        .tracker-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.85rem; }
        .tracker-table th { text-align: left; padding: 10px; color: var(--text-muted); border-bottom: 1px solid rgba(255,255,255,0.1); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; }
        .tracker-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: top; }
        .status-badge { font-size: 0.65rem; padding: 3px 7px; border-radius: 4px; font-weight: 800; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.1); }
        .badge-approved { background: rgba(16, 185, 129, 0.1); color: var(--success); border-color: var(--success); }
        .badge-returned { background: rgba(239, 68, 68, 0.1); color: var(--danger); border-color: var(--danger); }
        
        /* Style para sa Close Button */
        .btn-close-tracker { 
            display: block; width: 100%; margin-top: 25px; padding: 12px; 
            background: rgba(255, 255, 255, 0.05); color: var(--text-muted); 
            border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; 
            cursor: pointer; font-size: 0.8rem; font-weight: bold; transition: 0.3s;
        }
        .btn-close-tracker:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: #ef4444; }
    </style>
</head>
<body>

    <div id="loadingScreen" class="loading-overlay">
        <div class="spinner"></div>
        <h3 style="margin-top: 20px; color: var(--neon);">Indexing Transaction...</h3>
        <p style="color: var(--text-muted);">Verifying Office Records</p>
    </div>

    <div class="user-container" style="max-width: 700px; margin: 80px auto; padding: 0 20px;">
        <div class="glass-card" style="text-align: center;">
            <div class="logo">BEPO <span>PESO</span></div>
            <h2>Travel Order Tracker</h2>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Enter your tracking number to see the current office status.</p>

            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="text" id="trackInput" placeholder="e.g. BEPO-2026-X782" 
                       style="flex-grow: 1; padding: 15px; border-radius: 10px; border: 1px solid rgba(0,242,255,0.2); background: rgba(0,0,0,0.2); color: white; outline: none;">
                <button onclick="handleTrack()" class="btn-process" style="padding: 0 25px; cursor: pointer;">TRACK</button>
            </div>

            <div id="resultArea" style="display: none; text-align: left; margin-top: 40px;">
                <div style="border-bottom: 1px solid rgba(0, 242, 255, 0.3); padding-bottom: 15px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 id="resID" style="color: var(--neon); margin: 0;">---</h3>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin: 5px 0 0 0;">Official Travel Record</p>
                    </div>
                    <div id="resBadge"></div>
                </div>

                <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px; margin-bottom: 25px; border-left: 3px solid var(--neon);">
                    <p><strong>Passenger:</strong> <span id="resName">---</span></p>
                    <p><strong>Destination:</strong> <span id="resDest">---</span></p>
                    <p><strong>Current Office:</strong> <span id="resOffice" style="color: var(--neon); font-weight: bold;">---</span></p>
                </div>

                <h4 style="margin-bottom: 15px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
                    <i class="ri-history-line"></i> Transaction History
                </h4>
                
                <div class="table-container" style="overflow-x: auto;">
                    <table class="tracker-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Office</th>
                                <th>Remarks</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody"></tbody>
                    </table>
                </div>

                <button onclick="closeTracker()" class="btn-close-tracker">
                    <i class="ri-close-circle-line"></i> DONE VIEWING
                </button>
            </div>

            <div id="errorArea" style="display: none; color: #ef4444; margin-top: 20px; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 8px;">
                <i class="ri-error-warning-line"></i> Tracking ID not found. Please verify and try again.
            </div>
        </div>
    </div>

    <script>
    function closeTracker() {
        document.getElementById('resultArea').style.display = 'none';
        document.getElementById('errorArea').style.display = 'none';
        document.getElementById('trackInput').value = ''; 
        document.getElementById('trackInput').focus(); 
    }

    function handleTrack() {
        // Gigamitan og .trim() para malikayan ang error sa extra spaces
        const id = document.getElementById('trackInput').value.trim();
        const loader = document.getElementById('loadingScreen');
        const results = document.getElementById('resultArea');
        const error = document.getElementById('errorArea');

        if(!id) return alert("Please enter a Tracking ID");

        loader.style.display = 'flex';
        results.style.display = 'none';
        error.style.display = 'none';

        setTimeout(() => {
            fetch('fetch_data.php?id=' + encodeURIComponent(id))
            .then(res => res.json())
            .then(data => {
                loader.style.display = 'none';
                if(data.success && data.order) {
                    results.style.display = 'block';
                    document.getElementById('resID').innerText = data.order.tracking_id;
                    document.getElementById('resName').innerText = data.order.passenger_name;
                    document.getElementById('resDest').innerText = data.order.destination;
                    document.getElementById('resOffice').innerText = data.order.current_office;

                    // MAIN BADGE LOGIC (Force APPROVED if COMPLETED)
                    if(data.order.current_office === 'COMPLETED') {
                        document.getElementById('resBadge').innerHTML = '<span class="status-badge badge-approved">APPROVED</span>';
                    } else {
                        // Siguroha nga ang data.order.status_badge naay sulod gikan sa fetch_data.php
                        document.getElementById('resBadge').innerHTML = data.order.status_badge || '<span class="status-badge">PENDING</span>';
                    }

                    let html = '';
                    if(data.history && data.history.length > 0) {
                        data.history.forEach(h => {
                            let displayStatus = h.status_update;
                            let badgeClass = h.status_update.toLowerCase().includes('returned') ? 'badge-returned' : 'badge-approved';

                            // Logic para ma-standardize ang 'COMPLETED' ngadto sa 'APPROVED'
                            if(displayStatus.toLowerCase().includes('completed') || displayStatus.toLowerCase().includes('approved')) {
                                displayStatus = 'APPROVED';
                                badgeClass = 'badge-approved';
                            }

                            html += `
                                <tr>
                                    <td style="color: var(--text-muted); font-size: 0.75rem;">${h.formatted_time}</td>
                                    <td><strong style="color: #fff;">${h.office_name}</strong></td>
                                    <td style="font-style: italic; color: var(--text-dim); font-size: 0.8rem;">${h.remarks || 'No remarks'}</td>
                                    <td style="text-align: right;">
                                        <span class="status-badge ${badgeClass}">${displayStatus}</span>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="4" style="text-align:center; padding:20px; color:var(--text-muted);">No history records found.</td></tr>';
                    }
                    document.getElementById('historyTableBody').innerHTML = html;
                } else {
                    error.style.display = 'block';
                }
            })
            .catch(err => {
                loader.style.display = 'none';
                console.error(err);
                error.style.display = 'block';
                error.innerHTML = '<i class="ri-error-warning-line"></i> Connection error. Please check your database.';
            });
        }, 1200);
    }
    </script>
</body>
</html>