<?php 
include 'db.php'; 
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    if($stmt->execute([$name, $email, $pass])){
        echo "<script>window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | CryptoNext</title>
    <style>
        body { background: #0a0b0d; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .auth-card { background: #141519; padding: 40px; border-radius: 15px; width: 350px; border: 1px solid #2b2f36; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .auth-card h2 { margin-top: 0; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #0a0b0d; border: 1px solid #2b2f36; color: white; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0052ff; border: none; color: white; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .link { text-align: center; margin-top: 15px; font-size: 14px; color: #888; }
        .link a { color: #0052ff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2>Create Account</h2>
        <form method="POST">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Get Started</button>
        </form>
        <div class="link">Already have an account? <a href="login.php">Sign In</a></div>
    </div>
</body>
</html>
