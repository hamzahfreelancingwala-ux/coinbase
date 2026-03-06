<?php 
// 1. Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$coin_id = isset($_GET['coin']) ? htmlspecialchars($_GET['coin']) : 'bitcoin'; 
$message = "";

// 2. TRADING LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $coin_symbol = strtoupper($_POST['coin_symbol']);
        $price = (float)$_POST['current_price'];
        
        if ($price <= 0) throw new Exception("Price not loaded. Please wait.");

        if (isset($_POST['buy_order'])) {
            $usd_amount = (float)$_POST['usd_amount'];
            $crypto_amount = $usd_amount / $price;

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT balance_usd FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$user_id]);
            if ($stmt->fetchColumn() < $usd_amount) throw new Exception("Insufficient USD!");

            $pdo->prepare("UPDATE users SET balance_usd = balance_usd - ? WHERE id = ?")->execute([$usd_amount, $user_id]);
            
            $check = $pdo->prepare("SELECT id FROM wallets WHERE user_id = ? AND coin_symbol = ?");
            $check->execute([$user_id, $coin_symbol]);
            if ($check->rowCount() > 0) {
                $pdo->prepare("UPDATE wallets SET amount = amount + ? WHERE user_id = ? AND coin_symbol = ?")->execute([$crypto_amount, $user_id, $coin_symbol]);
            } else {
                $pdo->prepare("INSERT INTO wallets (user_id, coin_symbol, amount) VALUES (?, ?, ?)")->execute([$user_id, $coin_symbol, $crypto_amount]);
            }
            $pdo->commit();
            $message = "success|Bought " . round($crypto_amount, 6) . " $coin_symbol";
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "error|" . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trade <?php echo ucfirst($coin_id); ?></title>
    <script src="https://unpkg.com/lightweight-charts@3.8.0/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        :root { --primary: #0052ff; --bg: #0a0b0d; --card: #141519; --text: #fff; --border: #2b2f36; --success: #00ca92; --danger: #ff3a33; }
        body { background: var(--bg); color: var(--text); font-family: sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: 12px; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #000; border: 1px solid var(--border); color: #fff; border-radius: 6px; box-sizing: border-box; }
        .btn { width: 100%; padding: 15px; background: var(--primary); border: none; color: #fff; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 10px; }
        .btn:disabled { background: #2b2f36; opacity: 0.5; cursor: not-allowed; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; text-align: center; }
        .alert-success { background: #00ca9222; color: var(--success); border: 1px solid var(--success); }
        .alert-error { background: #ff3a3322; color: var(--danger); border: 1px solid var(--danger); }
        #chart { height: 450px; width: 100%; background: #141519; }
        .price-up { color: var(--success); }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin:0;"><?php echo strtoupper($coin_id); ?> / USD</h2>
            <div id="price-display" style="font-size: 28px; font-weight: bold;">$0.00</div>
        </div>
        <div id="chart"></div>
    </div>

    <div>
        <?php if($message): $m = explode('|', $message); ?>
            <div class="alert alert-<?php echo $m[0]; ?>"><?php echo $m[1]; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-top:0;">Buy Order</h3>
            <form method="POST">
                <input type="hidden" name="coin_symbol" value="<?php echo $coin_id; ?>">
                <input type="hidden" name="current_price" id="hidden_price">
                
                <label style="font-size: 12px; color: #888;">Amount to Buy (USD)</label>
                <input type="number" name="usd_amount" id="buy_input" placeholder="0.00" step="0.01" required>
                
                <p style="font-size: 14px; color: #888;">Estimated: <span id="buy_calc" style="color:var(--primary);">0.00</span></p>
                <button type="submit" name="buy_order" id="buyBtn" class="btn" disabled>Loading Price...</button>
            </form>
        </div>
    </div>
</div>

<script>
    const coin = "<?php echo $coin_id; ?>";
    const priceDiv = document.getElementById('price-display');
    const buyBtn = document.getElementById('buyBtn');
    let currentPrice = 0;

    // Initialize Chart
    const chart = LightweightCharts.createChart(document.getElementById('chart'), {
        layout: { backgroundColor: '#141519', textColor: '#d1d4dc' },
        grid: { vertLines: { color: '#2b2f36' }, horzLines: { color: '#2b2f36' } },
        priceScale: { borderColor: '#2b2f36' },
        timeScale: { borderColor: '#2b2f36' },
    });
    const lineSeries = chart.addLineSeries({ color: '#0052ff', lineWidth: 3 });

    async function loadData() {
        try {
            // Fetch Chart Data
            const chartRes = await fetch(`https://api.coingecko.com/api/v3/coins/${coin}/market_chart?vs_currency=usd&days=1`);
            const chartData = await chartRes.json();
            const points = chartData.prices.map(p => ({ time: p[0] / 1000, value: p[1] }));
            lineSeries.setData(points);
            chart.timeScale().fitContent();

            // Fetch Live Price
            const priceRes = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${coin}&vs_currencies=usd`);
            const priceData = await priceRes.json();
            currentPrice = priceData[coin].usd;

            // Update UI
            priceDiv.innerText = '$' + currentPrice.toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('hidden_price').value = currentPrice;
            
            buyBtn.disabled = false;
            buyBtn.innerText = "Place Buy Order";
        } catch (e) {
            console.log("Retrying...");
            priceDiv.innerText = "Updating...";
            setTimeout(loadData, 5000); // Retry after 5 seconds if API is busy
        }
    }

    // Calculation logic
    document.getElementById('buy_input').oninput = function() {
        if(currentPrice > 0) {
            document.getElementById('buy_calc').innerText = (this.value / currentPrice).toFixed(6) + " " + coin.toUpperCase();
        }
    };

    loadData();
    setInterval(loadData, 30000); // Refresh every 30s
</script>

</body>
</html>
