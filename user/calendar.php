<?php
// Set timezone
date_default_timezone_set('Asia/Manila');

// Kuhaon ang kasamtangan nga Bulan ug Tuig
$month = isset($_GET['m']) ? $_GET['m'] : date('m');
$year = isset($_GET['y']) ? $_GET['y'] : date('Y');

// Mga detalye para sa table
$first_day = mktime(0, 0, 0, $month, 1, $year);
$title = date('F Y', $first_day);
$day_of_week = date('D', $first_day);
$days_in_month = cal_days_in_month(0, $month, $year);

// Mapping sa unang adlaw sa bulan
$days_mapping = ['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6];
$blank = $days_mapping[$day_of_week];

// Navigation links
$base_url = "?page=calendar";
$prev_m = $month - 1; $prev_y = $year;
if ($prev_m == 0) { $prev_m = 12; $prev_y--; }
$next_m = $month + 1; $next_y = $year;
if ($next_m == 13) { $next_m = 1; $next_y++; }

// --- HOLIDAY LOGIC ---
function getPhilippineHoliday($d, $m, $y) {
    $date = sprintf("%02d-%02d", $m, $d);
    
    // Listahan sa Fixed Holidays sa Pilipinas
    $holidays = [
        "01-01" => "New Year's Day",
        "02-25" => "EDSA Revolution",
        "04-09" => "Araw ng Kagitingan",
        "05-01" => "Labor Day",
        "06-12" => "Independence Day",
        "08-21" => "Ninoy Aquino Day",
        "08-31" => "National Heroes Day", // (Simplified to last Monday)
        "11-01" => "All Saints' Day",
        "11-02" => "All Souls' Day",
        "11-30" => "Bonifacio Day",
        "12-08" => "Immaculate Conception",
        "12-25" => "Christmas Day",
        "12-30" => "Rizal Day",
        "12-31" => "New Year's Eve"
    ];

    // Note: Ang Holy Week (Maundy Thursday/Good Friday) mausab kada tuig. 
    // Para sa 2026: April 2 ug April 3
    if ($y == 2026) {
        if ($m == 4 && $d == 2) return "Maundy Thursday";
        if ($m == 4 && $d == 3) return "Good Friday";
    }

    return isset($holidays[$date]) ? $holidays[$date] : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .calendar-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.5s ease-in-out;
        }

        .calendar-container {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            box-sizing: border-box;
            flex: 1;
        }

        .cal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .cal-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -1px;
        }

        .cal-nav {
            text-decoration: none;
            color: #38bdf8;
            font-size: 1.8rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(56, 189, 248, 0.1);
            transition: 0.3s;
        }

        .cal-nav:hover {
            background: #38bdf8;
            color: #0f172a;
            transform: translateY(-2px);
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 15px;
            text-align: center;
        }

        .day-name {
            color: #64748b;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 15px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 10px;
        }

        .day-cell {
            aspect-ratio: 1.5 / 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .day-cell:hover:not(.empty) {
            border-color: #38bdf8;
            background: rgba(56, 189, 248, 0.1);
            transform: scale(1.05);
            color: #38bdf8;
            z-index: 2;
        }

        .today {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 20px rgba(56, 189, 248, 0.3);
            border: none !important;
            font-weight: 800;
        }

        /* Holiday Style */
        .holiday {
            border-color: rgba(239, 68, 68, 0.5) !important;
            color: #f87171 !important;
        }
        
        .holiday-name {
            font-size: 0.55rem;
            text-transform: uppercase;
            color: #ef4444;
            margin-top: 5px;
            font-weight: 700;
            position: absolute;
            bottom: 8px;
            width: 90%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .empty { 
            background: transparent; 
            border: none;
            cursor: default;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="calendar-wrapper">
    <div class="calendar-container">
        <div class="cal-header">
            <a href="<?= $base_url ?>&m=<?= $prev_m ?>&y=<?= $prev_y ?>" class="cal-nav">
                <i class="ri-arrow-left-s-line"></i>
            </a>
            <h2><?= $title ?></h2>
            <a href="<?= $base_url ?>&m=<?= $next_m ?>&y=<?= $next_y ?>" class="cal-nav">
                <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>

        <div class="cal-grid">
            <div class="day-name">Sun</div>
            <div class="day-name">Mon</div>
            <div class="day-name">Tue</div>
            <div class="day-name">Wed</div>
            <div class="day-name">Thu</div>
            <div class="day-name">Fri</div>
            <div class="day-name">Sat</div>

            <?php for($i=0; $i<$blank; $i++) echo '<div class="day-cell empty"></div>'; ?>

            <?php 
            for($day = 1; $day <= $days_in_month; $day++) {
                $is_today = ($day == date('j') && $month == date('m') && $year == date('Y'));
                $holiday_name = getPhilippineHoliday($day, $month, $year);
                
                $class = 'day-cell';
                if ($is_today) $class .= ' today';
                if ($holiday_name) $class .= ' holiday';
                
                echo "<div class='$class'>";
                echo "<span>$day</span>";
                if ($holiday_name) {
                    echo "<span class='holiday-name'>$holiday_name</span>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>