<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// include '../includes/database.php'; // I-uncomment ni kung ready na ang DB
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User Account | BEPO PESO Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --accent: #38bdf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
        }

        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-dark); 
            color: var(--text-main); 
            padding: 40px; 
        }

        /* HEADER SECTION */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .header-left h1 {
            margin: 0;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #fff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-left p {
            margin: 5px 0 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .btn-add-top {
            background: rgba(56, 189, 248, 0.1);
            color: var(--accent);
            border: 1px solid var(--accent);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-add-top:hover {
            background: var(--accent);
            color: var(--bg-dark);
        }

        /* FORM CARD */
        .glass-form {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            padding: 14px 15px 14px 45px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .strict-note {
            display: block;
            margin-top: 10px;
            color: var(--danger);
            font-size: 0.75rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .save-footer {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-save {
            width: 100%;
            padding: 15px;
            background: var(--accent);
            color: var(--bg-dark);
            border: none;
            border-radius: 10px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(56, 189, 248, 0.4);
        }

        .soon-overlay {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .dev-badge {
            font-size: 0.7rem;
            background: rgba(255,255,255,0.05);
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="header-left">
            <h1>Add New Files User Account</h1>
            <p>Admin Control Panel / Account Management</p>
        </div>
        <button type="button" class="btn-add-top">
            <i class="ri-user-add-fill"></i> ADD USER BUTTON
        </button>
    </div>

    <div class="glass-form">
        <form action="" method="POST">
            
            <div class="form-group">
                <label>Edit Full Name</label>
                <div class="input-wrapper">
                    <i class="ri-user-follow-line"></i>
                    <input type="text" name="fullname" class="form-control" placeholder="Enter traveler full name..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Edit Username</label>
                <div class="input-wrapper">
                    <i class="ri-at-line"></i>
                    <input type="text" name="username" class="form-control" placeholder="Create unique username..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Edit Password</label>
                <div class="input-wrapper">
                    <i class="ri-lock-password-line"></i>
                    <input type="password" id="passInput" name="password" class="form-control" placeholder="•••••" required maxlength="5">
                </div>
                <span class="strict-note">
                    <i class="ri-error-warning-fill"></i> STRICT NOTE: Numeric only 1 to 5.
                </span>
            </div>

            <div class="save-footer">
                <button type="submit" class="btn-save">Save Account Data</button>
                <div class="soon-overlay">
                    <span class="dev-badge">🛠️ Coming Soon: Data will be managed in admin_manage.php</span>
                </div>
            </div>
        </form>

        <div style="text-align: center; margin-top: 20px;">
             <small style="color: #475569;">Programmer: Mark Brian Angco</small>
        </div>
    </div>

    <script>
        // JavaScript para sa Strict Password (1-5 ra ang ma-type)
        document.getElementById('passInput').addEventListener('input', function (e) {
            // Tangtangon ang dili 1 to 5
            this.value = this.value.replace(/[^1-5]/g, '');
            
            // Limit 5 chars
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
        });
    </script>
</body>
</html>