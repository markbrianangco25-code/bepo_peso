<?php
// 1. Sugdan ang session para ma-track ang nalogin nga user
session_start();

// 2. PHP Logic para sa Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // TEMPORARY BYPASS PARA NI BOSS MARK
    // Maski unsa nga username basta ang password kay 12345
    if ($password === "12345") {
        
        // I-set nato ang imong Full Name sa session
        // Mao ni ang basahon sa user_dashboard.php
        $_SESSION['username'] = "Mark Brian Angco"; 
        
        // I-redirect sa user folder/dashboard
        header("Location: user/user_dashboard.php");
        exit();
    } else {
        // Kon sayop, pakit-on og error message
        $error_msg = "Incorrect Password! For testing, use: 12345";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal | BEPO PESO</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary: #38bdf8;
            --bg: #0f172a;
            --card: #1e293b;
            --text-muted: #94a3b8;
        }

        body {
            margin: 0; padding: 0; font-family: 'Inter', sans-serif;
            background-color: var(--bg); height: 100vh;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; color: white;
        }

        .auth-container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 400px;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            transition: 0.5s ease-in-out;
        }

        .form-box { transition: 0.5s; }
        .hidden { display: none; }

        h2 { margin: 0; font-size: 1.8rem; font-weight: 800; text-align: center; }
        h2 span { color: var(--primary); }
        p.subtitle { text-align: center; color: var(--text-muted); font-size: 0.9rem; margin: 10px 0 30px; }

        .form-group { margin-bottom: 18px; text-align: left; }
        .form-group label { display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }

        .input-box { position: relative; display: flex; align-items: center; }
        .input-box i.main-icon { position: absolute; left: 15px; color: #64748b; }
        .input-box i.view-pass { position: absolute; right: 15px; color: #64748b; cursor: pointer; }
        .input-box i.view-pass:hover { color: var(--primary); }

        .form-control {
            width: 100%; padding: 12px 40px 12px 45px;
            background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px; color: #fff; font-size: 1rem; transition: 0.3s; box-sizing: border-box;
        }
        .form-control:focus { outline: none; border-color: var(--primary); }

        .flex-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; margin-bottom: 20px; }
        .flex-row label { display: flex; align-items: center; gap: 5px; color: var(--text-muted); cursor: pointer; }
        .flex-row a { color: var(--primary); text-decoration: none; font-weight: 600; }

        .btn-main {
            width: 100%; padding: 14px; background: var(--primary); color: #0f172a;
            border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-main:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(56, 189, 248, 0.3); }

        .switch-text { text-align: center; margin-top: 25px; font-size: 0.9rem; color: var(--text-muted); }
        .switch-text span { color: var(--primary); cursor: pointer; font-weight: 700; }

        .strict-note { color: #ef4444; font-size: 0.7rem; font-weight: bold; margin-top: 5px; display: block; }

        /* PHP Error Alert Style */
        .php-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .footer-divider {
            margin-top: 30px; padding-top: 20px; 
            border-top: 1px solid rgba(255,255,255,0.05); 
            text-align: center;
        }
        .dev-credit {
            margin-top: 15px; display: flex; align-items: center; 
            justify-content: center; gap: 8px;
        }
        .line { width: 30px; height: 1px; background: rgba(56, 189, 248, 0.2); }
    </style>
</head>
<body>

    <div class="auth-container" id="container">
        
        <div id="loginForm" class="form-box">
            <h2>BEPO <span>PESO</span></h2>
            <p class="subtitle">Travel Document Tracking System</p>

            <?php if(isset($error_msg)): ?>
                <div class="php-error"><?= $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <i class="ri-user-3-line main-icon"></i>
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <i class="ri-lock-password-line main-icon"></i>
                        <input type="password" id="loginPass" name="password" class="form-control" placeholder="••••••••" required>
                        <i class="ri-eye-off-line view-pass" onclick="togglePass('loginPass', this)"></i>
                    </div>
                </div>

                <div class="flex-row">
                    <label><input type="checkbox"> Remember me</label>
                    <a href="javascript:void(0)" onclick="forgotPass()">Forgot Password?</a>
                </div>

                <button type="submit" name="login_btn" class="btn-main">Login Account</button>
            </form>

            <p class="switch-text">Don't have an account? <span onclick="switchForm('reg')">Sign Up</span></p>
        </div>

        <div id="registerForm" class="form-box hidden">
            <h2>Register <span>User</span></h2>
            <p class="subtitle">Create new traveler account</p>

            <form action="process_register.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-box">
                        <i class="ri-user-heart-line main-icon"></i>
                        <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <i class="ri-at-line main-icon"></i>
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <i class="ri-shield-keyhole-line main-icon"></i>
                        <input type="password" id="regPass" name="password" class="form-control" placeholder="•••••" required maxlength="5">
                        <i class="ri-eye-off-line view-pass" onclick="togglePass('regPass', this)"></i>
                    </div>
                    <span class="strict-note">STRICT: 1-5 Numeric Only</span>
                </div>

                <button type="submit" class="btn-main">Submit Registration</button>
            </form>

            <p class="switch-text">Already have an account? <span onclick="switchForm('login')">Sign In</span></p>
        </div>

        <div class="footer-divider">
            <div style="font-size: 0.85rem; color: var(--primary); font-weight: 700; margin-bottom: 5px;">
                BEPO PESO Tracking System
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;">
                &copy; <?php echo date("Y"); ?> Provincial Government of Bohol.<br>
                All Rights Reserved.
            </div>
        </div>
    </div>

    <script>
        function switchForm(type) {
            const login = document.getElementById('loginForm');
            const reg = document.getElementById('registerForm');
            if(type === 'reg') {
                login.classList.add('hidden');
                reg.classList.remove('hidden');
            } else {
                reg.classList.add('hidden');
                login.classList.remove('hidden');
            }
        }

        function togglePass(id, icon) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
            } else {
                input.type = "password";
                icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
            }
        }

        function forgotPass() {
            alert("Please contact admin.\nReason: Nakalimutan ang password.");
        }

        // Numeric validation for register pass
        document.getElementById('regPass').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^1-5]/g, '');
        });
    </script>
</body>
</html>