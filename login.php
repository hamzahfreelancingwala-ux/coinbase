<?php 
// 1. db.php must contain session_start() or add it here
include 'db.php'; 

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Search for user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set Session Variables
        $_SESSION['user_id'] = $user['id'];
        
        // Note: Check if your DB column is 'fullname' or 'full_name'
        $_SESSION['user_name'] = $user['fullname'] ?? $user['full_name']; 
        
        // UPDATED: Redirecting to index.php instead of dashboard.php
        echo "<script>window.location.href='index.php';</script>";
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CryptoNext Pro</title>
    <style>
        :root {
            --primary: #0052ff;
            --bg: #0a0b0d;
            --card: #141519;
            --text: #ffffff;
            --border: #2b2f36;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Inter', -apple-system, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: var(--card);
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .logo-area { text-align: center; margin-bottom: 30px; }
        .logo-area a {
            font-size: 28px;
            font-weight: bold;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: -1px;
        }

        h2 { text-align: center; margin-bottom: 10px; font-weight: 600; }
        .subtitle { text-align: center; color: #888; font-size: 14px; margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #b2b2b2; }

        input {
            width: 100%;
            padding: 14px;
            background: #000;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: white;
            font-size: 16px;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus { outline: none; border-color: var(--primary); }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-login:hover { filter: brightness(1.1); }
        .btn-login:active { transform: scale(0.98); }

        .error-msg {
            background: rgba(255, 58, 51, 0.1);
            color: #ff3a33;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(255, 58, 51, 0.3);
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #888;
        }

        .footer-links a { color: var(--primary); text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-area">
        <a href="index.php">CryptoNext</a>
    </div>
    
    <h2>Sign In</h2>
    <p class="subtitle">Enter your details to access your account</p>

    <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@email.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        
        <button type="submit" class="btn-login">Sign In</button>
    </form>

    <div class="footer-links">
        Don't have an account? <a href="signup.php">Create one</a>
    </div>
</div>

</body>
</html>
