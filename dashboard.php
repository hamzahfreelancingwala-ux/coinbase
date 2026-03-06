<?php
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

// Get User Info
$stmt = $pdo->prepare("SELECT balance_usd, full_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get Wallet Info
$stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { background: #0a0b0d; color: white; font-family: sans-serif; padding: 40px; }
        .card { background: #141519; padding: 20px; border-radius: 12px; border: 1px solid #2b2f36; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #2b2f36; }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
    <div class="card">
        <h3>Available Balance</h3>
        <h2 style="color: #00ca92;">$<?php echo number_format($user['balance_usd'], 2); ?></h2>
    </div>

    <div class="card">
        <h3>Your Assets</h3>
        <table>
            <tr><th>Coin</th><th>Amount</th><th>Action</th></tr>
            <?php foreach($wallets as $w): ?>
            <tr>
                <td><?php echo $w['coin_symbol']; ?></td>
                <td><?php echo number_format($w['amount'], 8); ?></td>
                <td><a href="trade.php?coin=<?php echo strtolower($w['coin_symbol']); ?>" style="color:#0052ff;">Trade</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <a href="index.php" style="color: #888;">← Back to Markets</a>
</body>
</html>
