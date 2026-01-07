<?php
global $os;
$Tiktok = $os->load_variable("Tiktok");
$Shopee = $os->load_variable("Shopee");
$Lazada = $os->load_variable("Lazada");

// ฟังก์ชันปัดราคาขึ้น
function roundPriceUp($price)
{
    $lastDigit = $price % 10;

    if ($lastDigit == 0 || $lastDigit == 5) {
        return $price;
    } else if ($lastDigit <= 5) {
        return $price + (5 - $lastDigit);
    } else {
        return $price + (10 - $lastDigit);
    }
}

// ดึงข้อมูลจาก API
$api_url = "https://www.bowinsgroup.com/ipn/proxy_bwd.php";
$api_data = @file_get_contents($api_url);
$gold_prices = [];
$api_info = [];

if ($api_data) {
    $decoded = json_decode($api_data, true);
    if ($decoded && $decoded['success']) {
        $gold_prices = $decoded['data'];
        $api_info = [
            'timestamp' => $decoded['timestamp'],
            'gold_price' => $decoded['api_data']['gold_price'] ?? 0,
            'exchange_rate' => $decoded['api_data']['exchange_rate'] ?? 0
        ];
    }
}

// ถ้าไม่มีข้อมูลจาก API ให้ใช้ข้อมูลจำลอง
if (empty($gold_prices)) {
    $gold_prices = [
        "15" => 890,
        "50" => 2630,
        "150" => 7030
    ];
    $api_info = [
        'timestamp' => date('Y-m-d H:i:s'),
        'gold_price' => 36.42,
        'exchange_rate' => 32.93
    ];
}
?>

<style>
    .gold-price-container {
        background: #ffffff;
        padding: 30px;
        margin: 20px 0;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .gold-title {
        text-align: center;
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .api-info {
        text-align: center;
        color: #718096;
        font-size: 14px;
        margin-bottom: 30px;
        padding: 15px;
        background: #f7fafc;
        border-radius: 8px;
        border-left: 4px solid #00204E;
    }

    .api-info .highlight {
        font-weight: 600;
        color: #00204E;
    }

    .price-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .price-table thead {
        background: #00204E;
        color: white;
    }

    .price-table th {
        padding: 18px 15px;
        text-align: center;
        font-weight: 600;
        font-size: 15px;
        border-right: 1px solid rgba(255, 255, 255, 0.15);
    }

    .price-table th:last-child {
        border-right: none;
    }

    .price-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e2e8f0;
        background: white;
    }

    .price-table tbody tr:hover {
        background: #f8f9fa;
    }

    .price-table tbody tr:last-child {
        border-bottom: none;
    }

    .price-table td {
        padding: 18px 15px;
        text-align: center;
        vertical-align: middle;
        font-size: 15px;
    }

    .platform-cell {
        font-weight: 700;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .platform-icon {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        color: white;
        background: #00204E;
    }

    .rate-cell {
        font-weight: 700;
        font-size: 16px;
        color: #2d3748;
    }

    .base-price-cell {
        font-weight: 600;
        color: #718096;
        font-size: 14px;
    }

    .final-price-cell {
        font-weight: 700;
        font-size: 16px;
        color: #00204E;
    }

    .weight-header {
        background: #00204E;
        color: white;
        font-weight: 600;
    }

    .refresh-btn {
        display: block;
        margin: 20px auto 0;
        padding: 12px 24px;
        background: #00204E;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .refresh-btn:hover {
        background: #003366;
        transform: translateY(-1px);
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .price-table.loading tbody tr {
        animation: pulse 1.5s infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .gold-price-container {
            padding: 20px;
            margin: 10px;
            overflow-x: auto;
        }

        .price-table {
            min-width: 600px;
        }

        .price-table th,
        .price-table td {
            padding: 12px 8px;
            font-size: 14px;
        }

        .platform-cell {
            flex-direction: column;
            gap: 5px;
            font-size: 14px;
        }

        .platform-icon {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }

        .final-price-cell {
            font-size: 15px;
        }

        .gold-title {
            font-size: 24px;
        }
    }
</style>

<div class="gold-price-container">

    <div class="api-info">
        <div>Last Updated: <span class="highlight" id="last-updated"><?php echo $api_info['timestamp']; ?></span></div>
        <div>Gold Price: <span class="highlight">$<?php echo number_format($api_info['gold_price'], 2); ?></span> | Exchange Rate: <span class="highlight">₿<?php echo number_format($api_info['exchange_rate'], 2); ?></span></div>
        <div class="notification-message" id="notification-message" style="display: none; margin-top: 10px; padding: 8px 12px; border-radius: 4px; font-weight: 600; transition: all 0.3s ease;"></div>
    </div>

    <table class="price-table" id="priceTable">
        <thead>
            <tr>
                <th>Platform</th>
                <th>Rate</th>
                <th class="weight-header">15g </th>
                <th class="weight-header">50g </th>
                <th class="weight-header">150g </th>
            </tr>
        </thead>
        <tbody>
            <!-- TikTok Row -->
            <tr>
                <td class="platform-cell">
                    <div class="platform-icon">TT</div>
                    <span>TikTok</span>
                </td>
                <td class="rate-cell">+<?php echo $Tiktok; ?>%</td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['15']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['15']; ?>" data-rate="<?php echo $Tiktok; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['15'] * (1 + $Tiktok / 100))); ?>
                    </div>
                </td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['50']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['50']; ?>" data-rate="<?php echo $Tiktok; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['50'] * (1 + $Tiktok / 100))); ?>
                    </div>
                </td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['150']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['150']; ?>" data-rate="<?php echo $Tiktok; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['150'] * (1 + $Tiktok / 100))); ?>
                    </div>
                </td>
            </tr>

            <!-- Shopee Row -->
            <tr>
                <td class="platform-cell">
                    <div class="platform-icon">SP</div>
                    <span>Shopee</span>
                </td>
                <td class="rate-cell">+<?php echo $Shopee; ?>%</td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['15']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['15']; ?>" data-rate="<?php echo $Shopee; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['15'] * (1 + $Shopee / 100))); ?>
                    </div>
                </td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['50']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['50']; ?>" data-rate="<?php echo $Shopee; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['50'] * (1 + $Shopee / 100))); ?>
                    </div>
                </td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['150']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['150']; ?>" data-rate="<?php echo $Shopee; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['150'] * (1 + $Shopee / 100))); ?>
                    </div>
                </td>
            </tr>

            <!-- Lazada Row -->
            <tr>
                <td class="platform-cell">
                    <div class="platform-icon">LZ</div>
                    <span>Lazada</span>
                </td>
                <td class="rate-cell">+<?php echo $Lazada; ?>%</td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['15']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['15']; ?>" data-rate="<?php echo $Lazada; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['15'] * (1 + $Lazada / 100))); ?>
                    </div>
                </td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['50']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['50']; ?>" data-rate="<?php echo $Lazada; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['50'] * (1 + $Lazada / 100))); ?>
                    </div>
                </td>
                <td>
                    <div class="base-price-cell">Base: ₿<?php echo number_format($gold_prices['150']); ?></div>
                    <div class="final-price-cell" data-base="<?php echo $gold_prices['150']; ?>" data-rate="<?php echo $Lazada; ?>">
                        ₿<?php echo number_format(roundPriceUp($gold_prices['150'] * (1 + $Lazada / 100))); ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <button class="refresh-btn" onclick="refreshPrices()">🔄 Refresh Prices</button>
</div>

<script>
    // ฟังก์ชันปัดราคาขึ้น JavaScript
    function roundPriceUp(price) {
        const lastDigit = price % 10;

        if (lastDigit === 0 || lastDigit === 5) {
            return price;
        } else if (lastDigit <= 5) {
            return price + (5 - lastDigit);
        } else {
            return price + (10 - lastDigit);
        }
    }

    async function refreshPrices() {
        const refreshBtn = document.querySelector('.refresh-btn');
        const priceTable = document.getElementById('priceTable');

        // Show loading state
        refreshBtn.textContent = '🔄 Refreshing...';
        refreshBtn.classList.add('loading');
        priceTable.classList.add('loading');

        try {
            // Fetch both gold prices and platform rates
            const [goldResponse, ratesResponse] = await Promise.all([
                fetch('https://www.bowinsgroup.com/ipn/proxy_bwd.php'),
                fetch('apps/market_price_bwd/xhr/get_current_rates.php')
            ]);

            const goldData = await goldResponse.json();
            const ratesData = await ratesResponse.json();

            if (goldData.success && goldData.data && ratesData.success) {
                // Update API info
                document.getElementById('last-updated').textContent = goldData.timestamp;

                // Update rates in table
                const rows = document.querySelectorAll('#priceTable tbody tr');
                const platforms = ['Tiktok', 'Shopee', 'Lazada'];

                rows.forEach((row, index) => {
                    const platform = platforms[index];
                    const newRate = ratesData.rates[platform];

                    // Update rate display
                    const rateCell = row.querySelector('.rate-cell');
                    rateCell.textContent = '+' + newRate + '%';

                    // Update all price calculations for this row
                    const finalPriceCells = row.querySelectorAll('.final-price-cell');
                    finalPriceCells.forEach((priceElement, cellIndex) => {
                        // Determine weight and get new base price
                        let newBasePrice, weight;
                        if (cellIndex === 0) { // 15g column
                            newBasePrice = goldData.data['15'];
                            weight = '15';
                        } else if (cellIndex === 1) { // 50g column
                            newBasePrice = goldData.data['50'];
                            weight = '50';
                        } else if (cellIndex === 2) { // 150g column
                            newBasePrice = goldData.data['150'];
                            weight = '150';
                        }

                        // Calculate new price with updated rate and round up
                        const calculatedPrice = newBasePrice * (1 + newRate / 100);
                        const finalPrice = roundPriceUp(calculatedPrice);

                        // Update display with animation
                        priceElement.style.transform = 'scale(1.05)';
                        setTimeout(() => {
                            priceElement.textContent = '₿' + new Intl.NumberFormat().format(finalPrice);
                            priceElement.dataset.base = newBasePrice;
                            priceElement.dataset.rate = newRate;

                            // Update base price display
                            const basePriceElement = priceElement.parentNode.querySelector('.base-price-cell');
                            if (basePriceElement) {
                                basePriceElement.textContent = 'Base: ₿' + new Intl.NumberFormat().format(newBasePrice);
                            }

                            priceElement.style.transform = 'scale(1)';
                        }, 200);
                    });
                });

                showNotification('Prices and rates updated successfully! 🎉', 'success');
            } else {
                showNotification('Failed to fetch latest data', 'error');
            }
        } catch (error) {
            console.error('Error refreshing data:', error);
            showNotification('Error connecting to APIs', 'error');
        } finally {
            // Remove loading state
            setTimeout(() => {
                refreshBtn.textContent = '🔄 Refresh Prices';
                refreshBtn.classList.remove('loading');
                priceTable.classList.remove('loading');
            }, 1000);
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.getElementById('notification-message');

        // Set message and style
        notification.textContent = message;
        notification.style.display = 'block';

        if (type === 'success') {
            notification.style.background = '#00204E';
            notification.style.color = 'white';
        } else if (type === 'error') {
            notification.style.background = '#dc3545';
            notification.style.color = 'white';
        } else {
            notification.style.background = '#f8f9fa';
            notification.style.color = '#2d3748';
            notification.style.border = '1px solid #e2e8f0';
        }

        // Auto hide after 3 seconds
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    // Auto refresh every 5 minutes
    setInterval(refreshPrices, 300000);
</script>