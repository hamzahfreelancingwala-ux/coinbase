<?php 
// 1. Database and Session Start
include 'db.php'; 

// 2. Clear Session if "Logout" was clicked
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// 3. Strict Login Check
$isLoggedIn = false;
$userName = "";

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $isLoggedIn = true;
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Trader";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoNext | Real-time Market</title>
    <style>
        :root {
            --primary: #0052ff;
            --secondary: #0a0b0d;
            --card-bg: #141519;
            --text-main: #ffffff;
            --text-muted: #888d9b;
            --border: #2b2f36;
            --success: #00ca92;
            --danger: #ff3a33;
        }

        body {
            background-color: var(--secondary);
            color: var(--text-main);
            font-family: 'Inter', -apple-system, sans-serif;
            margin: 0;
            line-height: 1.6;
        }

        /* Navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 6%;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-links { display: flex; align-items: center; }
        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            margin-left: 25px;
            font-weight: 500;
            font-size: 15px;
            transition: 0.3s;
        }

        .nav-links a:hover { color: var(--primary); }

        .signup-btn {
            background: var(--primary);
            color: white !important;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
        }

        .logout-btn { color: var(--danger) !important; font-weight: 600; }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            background: radial-gradient(circle at top, #162a5a 0%, #0a0b0d 100%);
        }

        .hero h1 { font-size: 52px; margin-bottom: 10px; letter-spacing: -1px; font-weight: 800; }
        .hero p { color: var(--text-muted); font-size: 20px; margin-bottom: 35px; }

        .auth-banner {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            padding: 25px 40px;
            border-radius: 16px;
            display: inline-block;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 82, 255, 0.3); }

        /* Market Table */
        .market-container {
            max-width: 1100px;
            margin: -60px auto 50px auto;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        table { width: 100%; border-collapse: collapse; }
        th { padding: 20px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; border-bottom: 1px solid var(--border); text-align: left; }
        td { padding: 20px; border-bottom: 1px solid var(--border); font-size: 15px; }

        .coin-info { display: flex; align-items: center; gap: 12px; }
        .coin-info img { width: 32px; height: 32px; border-radius: 50%; }
        .coin-name { font-weight: 700; display: block; }
        .coin-symbol { color: var(--text-muted); text-transform: uppercase; font-size: 12px; }

        .price-up { color: var(--success); font-weight: 600; }
        .price-down { color: var(--danger); font-weight: 600; }

        .trade-btn {
            background: #2b2f36;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }
        .trade-btn:hover { background: var(--primary); }

        .status-dot {
            height: 8px;
            width: 8px;
            background-color: var(--success);
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            box-shadow: 0 0 8px var(--success);
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">CryptoNext</a>
    <div class="nav-links">
        <a href="index.php">Markets</a>
        <?php if($isLoggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <span style="color: var(--border); margin-left: 20px;">|</span>
            <a href="index.php?action=logout" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php" class="signup-btn">Get Started</a>
        <?php endif; ?>
    </div>
</nav>

<div class="hero">
    <?php if($isLoggedIn): ?>
        <h1>Welcome Back, <?php echo htmlspecialchars($userName); ?></h1>
        <p>Market is <span class="status-dot"></span> <strong>Live</strong>. Ready to trade?</p>
        <button onclick="navTo('dashboard.php')" class="btn btn-primary">Open Dashboard</button>
    <?php else: ?>
        <h1>The future of money is here</h1>
        <p>Over 50+ cryptocurrencies to buy, sell, and track in real-time.</p>
        <div class="auth-banner">
            <span style="margin-right: 20px; font-weight: 500; color: var(--text-muted);">Sign in to start trading</span>
            <button onclick="navTo('login.php')" class="btn btn-primary">Login to Account</button>
        </div>
    <?php endif; ?>
</div>

<div class="market-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>24h Change</th>
                <th>Market Cap</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="market-list">
            <tr><td colspan="5" style="text-align:center; padding: 50px; color: var(--text-muted);">Initializing secure market connection...</td></tr>
        </tbody>
    </table>
</div>

<script>
    function navTo(url) {
        window.location.href = url;
    }

    async function fetchMarketData() {
        const tableBody = document.getElementById('market-list');
        try {
            const response = await fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=12&page=1&sparkline=false');
            
            if (!response.ok) throw new Error('API Rate Limit');
            
            const data = await response.json();
            tableBody.innerHTML = '';

            data.forEach(coin => {
                const isUp = coin.price_change_percentage_24h >= 0;
                // PHP helps determine the link destination based on login status
                const tradeUrl = <?php echo $isLoggedIn ? " 'trade.php?coin=' + coin.id " : " 'login.php' "; ?>;
                
                tableBody.innerHTML += `
                    <tr>
                        <td>
                            <div class="coin-info">
                                <img src="${coin.image}" alt="${coin.name}">
                                <div>
                                    <span class="coin-name">${coin.name}</span>
                                    <span class="coin-symbol">${coin.symbol.toUpperCase()}</span>
                                </div>
                            </div>
                        </td>
                        <td><b>$${coin.current_price.toLocaleString(undefined, {minimumFractionDigits: 2})}</b></td>
                        <td class="${isUp ? 'price-up' : 'price-down'}">
                            ${isUp ? '▲' : '▼'} ${Math.abs(coin.price_change_percentage_24h || 0).toFixed(2)}%
                        </td>
                        <td style="color: var(--text-muted)">$${(coin.market_cap / 1000000000).toFixed(2)}B</td>
                        <td>
                            <button onclick="navTo('${tradeUrl}')" class="trade-btn">Trade</button>
                        </td>
                    </tr>
                `;
            });
        } catch (error) {
            console.error("Connection Error:", error);
            tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 50px; color: var(--danger);">Market data temporarily unavailable. Please refresh in a moment.</td></tr>`;
        }
    }

    // Run immediately and then every 20 seconds
    fetchMarketData();
    setInterval(fetchMarketData, 20000);
</script>

</body>
</html>
