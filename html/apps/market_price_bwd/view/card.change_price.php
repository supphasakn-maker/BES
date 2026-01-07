<?php
global $os;
$Tiktok = $os->load_variable("Tiktok");
$Shopee = $os->load_variable("Shopee");
$Lazada = $os->load_variable("Lazada");
?>

<style>
    .market-price-container {
        background: #ffffff;
        padding: 30px;
        margin: 20px 0;
        border-radius: 20px;
    }

    .market-title {
        text-align: center;
        color: #2d3748;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 30px;
    }

    .price-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .price-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 30px 25px;
        text-align: center;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .price-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .platform-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: bold;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .tiktok-icon {
        background: linear-gradient(135deg, #ff0050, #ff4444);
    }

    .shopee-icon {
        background: linear-gradient(135deg, #ee4d2d, #ff6b35);
    }

    .lazada-icon {
        background: linear-gradient(135deg, #0f146d, #1a237e);
    }

    .platform-name {
        font-size: 18px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    .price-input-container {
        position: relative;
        margin-bottom: 20px;
    }

    .editable-price {
        border: none;
        background: linear-gradient(135deg, #f7fafc, #edf2f7);
        text-align: center;
        font-size: 36px;
        font-weight: 700;
        width: 100%;
        padding: 15px 20px;
        border-radius: 12px;
        color: #2d3748;
        transition: all 0.3s ease;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        outline: none;
    }

    .editable-price:focus {
        background: linear-gradient(135deg, #e6fffa, #bee3f8);
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5), inset 0 2px 4px rgba(0, 0, 0, 0.06);
        transform: scale(1.02);
    }

    .editable-price:hover {
        background: linear-gradient(135deg, #edf2f7, #e2e8f0);
    }

    .currency-symbol {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 24px;
        font-weight: 600;
        color: #718096;
        pointer-events: none;
    }

    .save-indicator {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #48bb78, #38a169);
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        font-size: 14px;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        animation: pulse 2s infinite;
        z-index: 10;
    }

    .save-indicator.loading {
        background: linear-gradient(135deg, #4299e1, #3182ce);
        animation: spin 1s linear infinite;
        box-shadow: 0 4px 12px rgba(66, 153, 225, 0.4);
    }

    .save-indicator.error {
        background: linear-gradient(135deg, #f56565, #e53e3e);
        animation: shake 0.5s ease-in-out;
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.4);
    }

    .last-updated {
        font-size: 12px;
        color: #718096;
        margin-top: 10px;
        font-style: italic;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #48bb78, #38a169);
        color: white;
        margin-top: 10px;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .status-badge.show {
        opacity: 1;
        animation: slideUp 0.3s ease;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(10px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .price-card {
        animation: fadeIn 0.6s ease forwards;
    }

    .price-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .price-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .price-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .market-price-container {
            background: #ffffff;
            padding: 20px;
            margin: 10px;
            border-radius: 20px;
        }

        .price-cards {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .editable-price {
            font-size: 28px;
            padding: 12px 16px;
        }

        .market-title {
            font-size: 24px;
        }
    }
</style>

<div class="market-price-container">
    <div class="price-cards">
        <div class="price-card">
            <div class="platform-name">TikTok</div>
            <div class="price-input-container">
                <div class="currency-symbol">₿</div>
                <input type="number"
                    class="editable-price"
                    id="tiktok_price"
                    value="<?php echo $Tiktok; ?>"
                    step="0.001"
                    data-platform="Tiktok"
                    data-decimal="3"
                    onchange="savePrice(this);"
                    oninput="handleInput(this);"
                    placeholder="0.000">
                <div class="save-indicator" id="tiktok_indicator">✓</div>
            </div>
            <div class="status-badge" id="tiktok_status">Live Rate</div>
            <div class="last-updated" id="tiktok_updated">Last updated: Now</div>
        </div>

        <div class="price-card">
            <div class="platform-name">Shopee</div>
            <div class="price-input-container">
                <div class="currency-symbol">₿</div>
                <input type="number"
                    class="editable-price"
                    id="shopee_price"
                    value="<?php echo $Shopee; ?>"
                    step="0.01"
                    data-platform="Shopee"
                    data-decimal="2"
                    onchange="savePrice(this);"
                    oninput="handleInput(this);"
                    placeholder="0.00">
                <div class="save-indicator" id="shopee_indicator">✓</div>
            </div>
            <div class="status-badge" id="shopee_status">Live Rate</div>
            <div class="last-updated" id="shopee_updated">Last updated: Now</div>
        </div>

        <div class="price-card">
            <div class="platform-name">Lazada</div>
            <div class="price-input-container">
                <div class="currency-symbol">₿</div>
                <input type="number"
                    class="editable-price"
                    id="lazada_price"
                    value="<?php echo $Lazada; ?>"
                    step="0.01"
                    data-platform="Lazada"
                    data-decimal="2"
                    onchange="savePrice(this);"
                    oninput="handleInput(this);"
                    placeholder="0.00">
                <div class="save-indicator" id="lazada_indicator">✓</div>
            </div>
            <div class="status-badge" id="lazada_status">Live Rate</div>
            <div class="last-updated" id="lazada_updated">Last updated: Now</div>
        </div>
    </div>
</div>

<script>
    let saveTimeouts = {};
    let originalValues = {};

    // Initialize original values
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.editable-price').forEach(input => {
            originalValues[input.id] = input.value;
        });
    });

    function handleInput(input) {
        const platform = input.dataset.platform.toLowerCase();
        const indicator = document.getElementById(platform + '_indicator');
        const statusBadge = document.getElementById(platform + '_status');

        // Hide indicator and status during typing
        if (indicator) indicator.style.display = 'none';
        if (statusBadge) statusBadge.classList.remove('show');

        // Clear existing timeout
        if (saveTimeouts[platform]) {
            clearTimeout(saveTimeouts[platform]);
        }

        // Set new timeout for auto-save
        saveTimeouts[platform] = setTimeout(() => {
            if (input.value !== originalValues[input.id]) {
                savePrice(input);
            }
        }, 1500);
    }

    function savePrice(input) {
        const platform = input.dataset.platform;
        const platformLower = platform.toLowerCase();
        const value = parseFloat(input.value);
        const indicator = document.getElementById(platformLower + '_indicator');
        const statusBadge = document.getElementById(platformLower + '_status');
        const updatedText = document.getElementById(platformLower + '_updated');

        if (isNaN(value) || value < 0) {
            showError(input, 'กรุณาใส่ตัวเลขที่ถูกต้อง');
            return;
        }

        // Show loading state
        showLoading(indicator, statusBadge, 'Updating...');

        const formData = new FormData();
        formData.append('platform', platform);
        formData.append('price', value);

        fetch('apps/market_price_bwd/xhr/save_market_price.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(responseText => {
                try {
                    const data = JSON.parse(responseText);

                    if (data.success) {
                        // Update with server value
                        if (data.value !== undefined) {
                            const decimal = parseInt(input.dataset.decimal);
                            input.value = parseFloat(data.value).toFixed(decimal);
                            originalValues[input.id] = input.value;
                        }

                        showSuccess(indicator, statusBadge, updatedText);
                    } else {
                        showError(input, data.msg || 'ไม่สามารถบันทึกข้อมูลได้');
                    }
                } catch (e) {
                    showError(input, 'เกิดข้อผิดพลาดในการแปลงข้อมูล');
                }
            })
            .catch(error => {
                showError(input, 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
            });
    }

    function showLoading(indicator, statusBadge, message) {
        if (indicator) {
            indicator.innerHTML = '⟳';
            indicator.className = 'save-indicator loading';
            indicator.style.display = 'flex';
        }

        if (statusBadge) {
            statusBadge.textContent = message;
            statusBadge.style.background = 'linear-gradient(135deg, #4299e1, #3182ce)';
            statusBadge.classList.add('show');
        }
    }

    function showSuccess(indicator, statusBadge, updatedText) {
        if (indicator) {
            indicator.innerHTML = '✓';
            indicator.className = 'save-indicator';
            indicator.style.display = 'flex';

            setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
        }

        if (statusBadge) {
            statusBadge.textContent = 'Updated';
            statusBadge.style.background = 'linear-gradient(135deg, #48bb78, #38a169)';
            statusBadge.classList.add('show');

            setTimeout(() => {
                statusBadge.textContent = 'Live Rate';
                statusBadge.classList.remove('show');
            }, 2000);
        }

        if (updatedText) {
            const now = new Date();
            updatedText.textContent = `Last updated: ${now.toLocaleTimeString()}`;
        }
    }

    function showError(input, message) {
        const platform = input.dataset.platform.toLowerCase();
        const indicator = document.getElementById(platform + '_indicator');
        const statusBadge = document.getElementById(platform + '_status');

        if (indicator) {
            indicator.innerHTML = '✗';
            indicator.className = 'save-indicator error';
            indicator.style.display = 'flex';

            setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
        }

        if (statusBadge) {
            statusBadge.textContent = 'Error';
            statusBadge.style.background = 'linear-gradient(135deg, #f56565, #e53e3e)';
            statusBadge.classList.add('show');

            setTimeout(() => {
                statusBadge.textContent = 'Live Rate';
                statusBadge.style.background = 'linear-gradient(135deg, #48bb78, #38a169)';
                statusBadge.classList.remove('show');
            }, 3000);
        }

        // Show toast notification
        showToast(message, 'error');
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? 'linear-gradient(135deg, #f56565, #e53e3e)' : 'linear-gradient(135deg, #4299e1, #3182ce)'};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 1000;
        font-weight: 600;
        max-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add CSS for toast animations
    const style = document.createElement('style');
    style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
    document.head.appendChild(style);
</script>