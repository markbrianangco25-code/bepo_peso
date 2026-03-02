/**
 * BEPO PESO Tracker Logic
 */

function startTracking() {
    const trackingID = document.getElementById('trackingID').value;
    const loading = document.getElementById('loadingScreen');
    const resultArea = document.getElementById('resultArea');
    const errorArea = document.getElementById('errorArea');

    if (!trackingID) {
        alert("Palihug isulod ang imong Tracking ID.");
        return;
    }

    // 1. Ipakita ang Loading Screen
    loading.style.display = 'flex';
    resultArea.style.display = 'none';
    errorArea.style.display = 'none';

    // 2. Simulated Delay para sa "Transaction Indexing" feel (2.5 seconds)
    setTimeout(() => {
        fetch(`fetch_user_data.php?id=${trackingID}`)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none'; // Tangtangon ang loading screen

                if (data.error) {
                    errorArea.style.display = 'block';
                } else {
                    renderResult(data);
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                console.error("System Error:", err);
            });
    }, 2500);
}

function renderResult(data) {
    const resultArea = document.getElementById('resultArea');
    resultArea.style.display = 'block';

    // Update main fields
    document.getElementById('resName').innerText = data.order.passenger_name;
    document.getElementById('resOffice').innerText = data.order.current_office;

    // Timeline Rendering
    const historyList = document.getElementById('historyList');
    let timelineHTML = '';

    data.history.forEach(item => {
        timelineHTML += `
            <div class="timeline-item">
                <span class="office-tag">${item.office_name}</span> 
                <span class="time-tag"> - ${item.processed_at}</span>
                <p style="font-size: 14px; margin-top: 5px;">${item.status_update}</p>
                <i style="color: #64748b; font-size: 12px;">"${item.remarks || 'No remarks'}"</i>
            </div>
        `;
    });

    historyList.innerHTML = timelineHTML;
}