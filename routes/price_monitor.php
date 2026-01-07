<?php

class PriceMonitor
{
    private $db_host = '192.168.1.92';
    private $db_user = 'erp-user';
    private $db_pass = 'T6p$4u4vcf';
    private $db_name = 'erp_main';
    protected $connection;

    private $gold_api = [
        'url' => 'https://www.goldapi.io/api/XAG/USD',
        'headers' => ['x-access-token: goldapi-ba7qhzsm0yxfabu-io']
    ];

    private $bbl_api = [
        'url' => 'https://bbl-sea-apim-p.azure-api.net/api/ExchangeRateService/GetLatestfxrates',
        'headers' => [
            'Ocp-Apim-Subscription-Key: bf51a81566a34095b568ac0ccb3e4ee4',
            'Cache-Control: no-cache'
        ]
    ];

    protected $factor = 32.1507;
    private $min_price_gap = 100;

    protected $var_cache = [];

    // APCu Cache Keys และ TTL
    private const GOLD_CACHE_KEY = 'gold_xag_usd_price';
    private const BBL_CACHE_KEY = 'bbl_exchange_rate';
    private const CACHE_TTL = 60; // วินาที

    public function __construct()
    {
        date_default_timezone_set('Asia/Bangkok');
        $this->connectDatabase();
    }

    private function connectDatabase()
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->connection = mysqli_init();
        if (!$this->connection) {
            die("Connection failed: mysqli_init");
        }
        $this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

        if (!$this->connection->real_connect(
            $this->db_host,
            $this->db_user,
            $this->db_pass,
            $this->db_name
        )) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if (!$this->connection->set_charset('utf8mb4')) {
            error_log('set_charset utf8mb4 failed: ' . mysqli_error($this->connection));
        }
    }

    protected function getVariable(string $name, $default = null)
    {
        if (array_key_exists($name, $this->var_cache)) {
            return $this->var_cache[$name];
        }
        $sql = "SELECT value FROM os_variable WHERE name = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($row && isset($row['value'])) {
                $val = is_numeric($row['value']) ? floatval($row['value']) : $row['value'];
                $this->var_cache[$name] = $val;
                return $val;
            }
        }
        $this->var_cache[$name] = $default;
        return $default;
    }

    protected function getPmdcRate()
    {
        $sql = "SELECT value FROM os_variable WHERE name = 'pmdc_rate'";
        $result = mysqli_query($this->connection, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return floatval($row['value']);
        }

        return 0.2000;
    }

    public function isWorkingHours()
    {
        $current_time = new DateTime();
        $day_of_week = $current_time->format('N');
        $current_hour = (int)$current_time->format('H');
        $current_minute = (int)$current_time->format('i');
        $current_date = $current_time->format('Y-m-d');

        if ($this->isPublicHoliday($current_date)) {
            return false;
        }

        if ($day_of_week == 7) {
            if ($current_hour != 9) {
                return false;
            }
            if ($this->isSundayPriceAlreadyAnnounced($current_date)) {
                return false;
            }
            return true;
        }

        if ($day_of_week == 6) {
            return false;
        }

        if ($day_of_week >= 1 && $day_of_week <= 5) {
            $start_time = 9 * 60;
            $end_time = 17 * 60 + 30;
            $current_time_minutes = $current_hour * 60 + $current_minute;
            return ($current_time_minutes >= $start_time && $current_time_minutes <= $end_time);
        }

        return false;
    }

    protected function getGoldPrice()
    {
        // ลอง APCu Cache ก่อน
        if (function_exists('apcu_fetch')) {
            $cached = apcu_fetch(self::GOLD_CACHE_KEY, $success);
            if ($success && is_numeric($cached)) {
                echo "[Cache] ใช้ราคา Gold จาก APCu: {$cached}\n";
                return $cached;
            }
        }

        echo "[API] กำลังเรียก Gold API...\n";

        // เรียก API
        $attempts = 3;
        $lastError = null;

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->gold_api['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->gold_api['headers']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($http_code == 200 && $response) {
                $data = json_decode($response, true);

                if (!is_array($data)) {
                    $lastError = 'Invalid JSON response';
                    continue;
                }

                if (isset($data['error'])) {
                    $lastError = 'API error: ' . $data['error'];
                    continue;
                }

                if (isset($data['price']) && $data['price'] !== null) {
                    $price = $data['price'];

                    // เก็บใน APCu
                    if (function_exists('apcu_store')) {
                        apcu_store(self::GOLD_CACHE_KEY, $price, self::CACHE_TTL);
                        echo "[Cache] บันทึกราคา Gold ลง APCu (TTL: " . self::CACHE_TTL . "s)\n";
                    }

                    return $price;
                }

                $lastError = 'Price is null or not available';
                continue;
            }

            // Handle 429 (Rate Limit)
            if ($http_code == 429) {
                $lastError = 'Rate limit exceeded (429)';
                if ($attempt < $attempts - 1) {
                    $delay = pow(2, $attempt); // 1s, 2s, 4s
                    echo "[Retry] 429 detected, waiting {$delay}s...\n";
                    sleep($delay);
                    continue;
                }
            }

            $lastError = $response === false ? $curlError : "HTTP {$http_code}";

            if ($attempt < $attempts - 1) {
                usleep(500 * 1000);
            }
        }

        error_log("Gold API failed after {$attempts} attempts: {$lastError}");
        echo "[Error] Gold API failed: {$lastError}\n";
        return null;
    }

    protected function getBBLExchangeRate()
    {
        // ลอง APCu Cache ก่อน
        if (function_exists('apcu_fetch')) {
            $cached = apcu_fetch(self::BBL_CACHE_KEY, $success);
            if ($success && is_numeric($cached)) {
                echo "[Cache] ใช้อัตราแลกเปลี่ยนจาก APCu: {$cached}\n";
                return $cached;
            }
        }

        echo "[API] กำลังเรียก BBL API...\n";

        // เรียก API
        $attempts = 3;
        $lastError = null;

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->bbl_api['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->bbl_api['headers']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($http_code == 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data) && is_array($data)) {
                    foreach ($data as $rate) {
                        if (isset($rate['Family']) && $rate['Family'] === 'USD50') {
                            $exchange_rate = isset($rate['Bill_DD_TT']) ? $rate['Bill_DD_TT'] : null;

                            if ($exchange_rate !== null) {
                                // เก็บใน APCu
                                if (function_exists('apcu_store')) {
                                    apcu_store(self::BBL_CACHE_KEY, $exchange_rate, self::CACHE_TTL);
                                    echo "[Cache] บันทึกอัตราแลกเปลี่ยนลง APCu (TTL: " . self::CACHE_TTL . "s)\n";
                                }

                                return $exchange_rate;
                            }
                        }
                    }
                }
            }

            // Handle 429 (Rate Limit)
            if ($http_code == 429) {
                $lastError = 'Rate limit exceeded (429)';
                if ($attempt < $attempts - 1) {
                    $delay = pow(2, $attempt);
                    echo "[Retry] 429 detected, waiting {$delay}s...\n";
                    sleep($delay);
                    continue;
                }
            }

            $lastError = $response === false ? $curlError : "HTTP {$http_code}";

            if ($attempt < $attempts - 1) {
                usleep(500 * 1000);
            }
        }

        error_log("BBL API failed after {$attempts} attempts: {$lastError}");
        echo "[Error] BBL API failed: {$lastError}\n";
        return null;
    }

    protected function getLatestPrice()
    {
        $sql = "SELECT * , 
                (LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS_BUY' , 
                (LAG(sell) OVER (ORDER BY id)) AS 'PREVIOUS_SELL' ,
                (buy - LAG(buy) OVER (ORDER BY id)) AS 'BUY_DIFF' ,
                (sell - LAG(sell) OVER (ORDER BY id)) AS 'SELL_DIFF'
                FROM bs_announce_silver 
                WHERE status = 1 
                ORDER BY id DESC 
                LIMIT 1";

        $result = mysqli_query($this->connection, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        return null;
    }

    protected function calculateNewPrices($spot_price, $exchange_rate)
    {
        $pmdc_rate = $this->getPmdcRate();
        $base_price = ((floatval($spot_price) + floatval($pmdc_rate)) * $this->factor) * floatval($exchange_rate);

        $sell_price_raw = $base_price;
        $sell_price = $this->roundToNearestHundred($sell_price_raw);

        $change_buy = $this->getVariable('change_buy', -700);

        $buy_price = $sell_price + floatval($change_buy);

        return [
            'sell'       => $sell_price,
            'buy'        => $buy_price,
            'base'       => round($base_price, 4),
            'sell_raw'   => round($sell_price_raw, 4),
            'buy_raw'    => round($sell_price_raw + floatval($change_buy), 4),
            'pmdc_rate'  => $pmdc_rate,
            'factor'     => $this->factor,
            'change_buy' => $change_buy,
        ];
    }

    protected function roundToNearestHundred($price)
    {
        $hundreds = floor($price / 100) * 100;
        $remainder = $price - $hundreds;

        if ($remainder < 0.25) {
            return $hundreds;
        } else if ($remainder <= 50) {
            return $hundreds + 50;
        } else {
            return $hundreds + 100;
        }
    }

    protected function isPublicHoliday($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $sql = "SELECT COUNT(*) as count FROM a_public_holiday WHERE PublicHoliday = ?";
        $stmt = mysqli_prepare($this->connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            return $row['count'] > 0;
        }

        return false;
    }

    protected function isSundayPriceAlreadyAnnounced($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $sql = "SELECT COUNT(*) as count FROM bs_announce_silver WHERE date = ? AND status = 1";
        $stmt = mysqli_prepare($this->connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            return $row['count'] > 0;
        }

        return false;
    }

    protected function getLastSaturdayPrice()
    {
        $current_date = new DateTime();
        $days_back = 1;

        while ($days_back <= 7) {
            $check_date = clone $current_date;
            $check_date->sub(new DateInterval("P{$days_back}D"));

            if ($check_date->format('N') == 6) {
                $saturday_date = $check_date->format('Y-m-d');

                $sql = "SELECT * FROM bs_announce_silver 
                        WHERE date = ? AND status = 1 
                        ORDER BY id DESC LIMIT 1";

                $stmt = mysqli_prepare($this->connection, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $saturday_date);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    mysqli_stmt_close($stmt);

                    if ($row) {
                        return $row;
                    }
                }
            }
            $days_back++;
        }

        return null;
    }

    protected function checkPriceDifference($current_prices, $new_prices)
    {
        if (!$current_prices) {
            return true;
        }

        $previous_announced_sell = $current_prices['sell'];
        $new_raw_sell = $new_prices['sell_raw'];

        echo "  เปรียบเทียบราคา:\n";
        echo "    - ราคาประกาศก่อนหน้า: " . number_format($previous_announced_sell, 0) . " บาท\n";
        echo "    - ราคาดิบใหม่: " . number_format($new_raw_sell, 2) . " บาท\n";

        return $this->shouldTriggerUpdate($previous_announced_sell, $new_raw_sell);
    }

    protected function shouldTriggerUpdate($previous_announced_price, $new_raw_price)
    {
        $price_difference = abs($new_raw_price - $previous_announced_price);

        echo "  วิเคราะห์การเปลี่ยนแปลงราคา:\n";
        echo "    - ราคาประกาศก่อนหน้า: " . number_format($previous_announced_price, 0) . " บาท\n";
        echo "    - ราคาดิบใหม่: " . number_format($new_raw_price, 2) . " บาท\n";
        echo "    - ความต่าง: " . number_format($price_difference, 2) . " บาท\n";
        echo "    - เกณฑ์การประกาศ: {$this->min_price_gap} บาท\n";

        $should_announce = $price_difference >= $this->min_price_gap;

        if ($should_announce) {
            echo "    ✅ ห่างกัน >= {$this->min_price_gap} บาท → ประกาศราคา\n";
        } else {
            echo "    ❌ ห่างกัน < {$this->min_price_gap} บาท → ไม่ประกาศ\n";
        }

        return $should_announce;
    }

    protected function getNextNo($is_sunday = false)
    {
        $current_date = date('Y-m-d');

        if ($is_sunday) {
            return 1;
        }

        $sql = "SELECT COALESCE(MAX(no), 0) + 1 as next_no 
                FROM bs_announce_silver 
                WHERE date = ? 
                ORDER BY no DESC 
                LIMIT 1";

        $stmt = mysqli_prepare($this->connection, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $current_date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            return (int)$row['next_no'];
        }

        return 1;
    }

    protected function getLastInsertedNo($id)
    {
        $sql = "SELECT no FROM bs_announce_silver WHERE id = ?";
        $stmt = mysqli_prepare($this->connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            return $row ? $row['no'] : 'Unknown';
        }

        return 'Unknown';
    }

    protected function getPreviousDifference($new_buy, $new_sell, $current_prices = null)
    {
        if ($current_prices) {
            $diff_buy = $new_buy - $current_prices['buy'];
            $diff_sell = $new_sell - $current_prices['sell'];
            return "Buy: " . number_format($diff_buy, 2) . ", Sell: " . number_format($diff_sell, 2);
        }

        $sql = "SELECT buy, sell FROM bs_announce_silver WHERE status = 1 ORDER BY id DESC LIMIT 2";
        $rs = mysqli_query($this->connection, $sql);

        if ($rs && mysqli_num_rows($rs) >= 2) {
            mysqli_fetch_assoc($rs);
            $row = mysqli_fetch_assoc($rs);

            $diff_buy = $new_buy - $row['buy'];
            $diff_sell = $new_sell - $row['sell'];
            return "Buy: " . number_format($diff_buy, 2) . ", Sell: " . number_format($diff_sell, 2);
        }

        return "ไม่มีข้อมูลเปรียบเทียบ";
    }

    protected function insertNewPrice($prices, $spot_price, $exchange_rate)
    {
        $previous_prices = $this->getLatestPrice();

        $current_datetime = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d');
        $dd = date('d/m/Y');
        $time = date('H:i');

        $next_no = $this->getNextNo(false);

        $sql = "INSERT INTO bs_announce_silver 
            (no, created, date, rate_spot, rate_exchange, rate_pmdc, sell, buy, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = mysqli_prepare($this->connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "issddddd",
                $next_no,
                $current_datetime,
                $current_date,
                $spot_price,
                $exchange_rate,
                $prices['pmdc_rate'],
                $prices['sell'],
                $prices['buy']
            );

            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($result) {
                $token = "7891135995:AAEEwyoEp2_-68p0E6Y84DUEzNQKPJq-avQ";
                $chat_id = "-4734852819";

                $previous_announced_sell = $previous_prices ? $previous_prices['sell'] : 0;
                $raw_price_diff = $previous_announced_sell > 0 ? $prices['sell_raw'] - $previous_announced_sell : 0;

                $previous = $this->getPreviousDifference($prices['buy'], $prices['sell'], $previous_prices);

                $message = "ประกาศราคาแท่งเงินจากระบบ\n"
                    . "วันที่: {$dd} / {$time}\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "ราคาประกาศ (ปัดแล้ว):\n"
                    . "   ราคาขาย: " . number_format($prices['sell'], 0) . " บาท\n"
                    . "   ราคารับซื้อ: " . number_format($prices['buy'], 0) . " บาท\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "ราคาดิบ (ก่อนปัด):\n"
                    . "   ราคาขายดิบ: " . number_format($prices['sell_raw'], 2) . " บาท\n"
                    . "   ราคารับซื้อดิบ: " . number_format($prices['buy_raw'], 2) . " บาท\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "การเปรียบเทียบ:\n";

                if ($previous_prices) {
                    $message .= "   ราคาประกาศก่อนหน้า: " . number_format($previous_announced_sell, 0) . " บาท\n"
                        . "   ราคาดิบใหม่: " . number_format($prices['sell_raw'], 2) . " บาท\n"
                        . "   ความต่าง: " . number_format(abs($raw_price_diff), 2) . " บาท\n";

                    if ($raw_price_diff > 0) {
                        $message .= "   ทิศทาง: เพิ่มขึ้น\n";
                    } elseif ($raw_price_diff < 0) {
                        $message .= "   ทิศทาง: ลดลง\n";
                    } else {
                        $message .= "   ทิศทาง: ไม่เปลี่ยนแปลง\n";
                    }

                    if (abs($raw_price_diff) >= 100) {
                        $message .= "   เหตุผลประกาศ: ห่างกัน ≥ 100 บาท\n";
                    } else {
                        $message .= "   หมายเหตุ: ห่างกันน้อยกว่า 100 บาท\n";
                    }
                } else {
                    $message .= "   ครั้งแรก: ไม่มีราคาก่อนหน้า\n";
                }

                $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "รายละเอียดเพิ่มเติม:\n"
                    . "   ครั้งที่: {$next_no}\n"
                    . "   SPOT: {$spot_price} USD\n"
                    . "   EXCHANGE: {$exchange_rate} THB\n"
                    . "   PMDC: {$prices['pmdc_rate']}\n"
                    . "   Factor: {$prices['factor']}\n"
                    . "   change_buy: {$prices['change_buy']}\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "การเปลี่ยนแปลงราคาประกาศ:\n"
                    . "   {$previous}";

                $url = "https://api.telegram.org/bot{$token}/sendMessage";
                $data = [
                    'chat_id' => $chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ];

                $options = [
                    'http' => [
                        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                    ]
                ];

                $context  = stream_context_create($options);
                $telegram_result = file_get_contents($url, false, $context);

                if ($telegram_result) {
                    echo "📱 ส่ง Telegram สำเร็จ!\n";
                } else {
                    echo "❌ เกิดข้อผิดพลาดในการส่ง Telegram\n";
                }
            }

            return $result;
        }

        return false;
    }

    protected function insertSaturdayPrice()
    {
        $saturday_price = $this->getLastSaturdayPrice();

        if (!$saturday_price) {
            echo "ไม่พบราคาจากวันเสาร์ล่าสุด\n";
            return false;
        }

        $previous_prices = $this->getLatestPrice();

        $current_datetime = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d');
        $dd = date('d/m/Y');
        $time = date('H:i');

        $next_no = $this->getNextNo(true);

        $sql = "INSERT INTO bs_announce_silver 
            (no, created, date, rate_spot, rate_exchange, rate_pmdc, sell, buy, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = mysqli_prepare($this->connection, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "issddddd",
                $next_no,
                $current_datetime,
                $current_date,
                $saturday_price['rate_spot'],
                $saturday_price['rate_exchange'],
                $saturday_price['rate_pmdc'],
                $saturday_price['sell'],
                $saturday_price['buy']
            );

            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($result) {
                $token = "7891135995:AAEEwyoEp2_-68p0E6Y84DUEzNQKPJq-avQ";
                $chat_id = "-4734852819";

                $previous = $this->getPreviousDifference($saturday_price['buy'], $saturday_price['sell'], $previous_prices);

                $message = "ประกาศราคาแท่งเงิน (วันอาทิตย์)\n"
                    . "วันที่: {$dd} / {$time}\n"
                    . "ใช้ราคาจากวันเสาร์ล่าสุด\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "ราคาประกาศ:\n"
                    . "   ราคาขาย: " . number_format($saturday_price['sell'], 0) . " บาท\n"
                    . "   ราคารับซื้อ: " . number_format($saturday_price['buy'], 0) . " บาท\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "รายละเอียด:\n"
                    . "   ครั้งที่: {$next_no} (วันอาทิตย์)\n"
                    . "   SPOT: {$saturday_price['rate_spot']} USD\n"
                    . "   EXCHANGE: {$saturday_price['rate_exchange']} THB\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    . "การเปลี่ยนแปลง:\n"
                    . "   {$previous}";

                $url = "https://api.telegram.org/bot{$token}/sendMessage";
                $data = [
                    'chat_id' => $chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ];

                $options = [
                    'http' => [
                        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                    ]
                ];

                $context  = stream_context_create($options);
                file_get_contents($url, false, $context);
            }

            return $result;
        }

        return false;
    }

    public function monitorPrices()
    {
        $current_time = new DateTime();
        $day_of_week = $current_time->format('N');
        $current_date = $current_time->format('Y-m-d');
        $current_hour = (int)$current_time->format('H');

        echo "เริ่มตรวจสอบราคา: " . date('Y-m-d H:i:s') . "\n";
        echo "วัน: " . $current_time->format('l') . " (" . $day_of_week . ")\n";
        echo "เกณฑ์การประกาศ: ราคาดิบใหม่ ห่างจากราคาประกาศก่อนหน้า >= {$this->min_price_gap} บาท\n";
        echo "กฎการปัด: 0.00-0.24→คงเดิม, 0.25-50.00→+50, 50.01-99.99→+100\n";

        // แสดงสถานะ APCu
        if (function_exists('apcu_enabled') && apcu_enabled()) {
            echo "APCu Cache: ✅ เปิดใช้งาน (TTL: " . self::CACHE_TTL . "s)\n";
        } else {
            echo "APCu Cache: ❌ ปิดใช้งาน\n";
        }
        echo "\n";

        if ($this->isPublicHoliday($current_date)) {
            echo "วันหยุดนักขัตฤกษ์ - ระบบหยุดทำงาน (ไม่ประกาศราคา)\n";
            return;
        }

        if (!$this->isWorkingHours()) {
            if ($day_of_week == 6) {
                echo "วันเสาร์ - ระบบหยุดทำงาน\n";
            } elseif ($day_of_week == 7) {
                if ($current_hour != 9) {
                    echo "วันอาทิตย์ - ต้องเป็นเวลา 9:00 น. เท่านั้น (ปัจจุบัน {$current_hour}:xx น.)\n";
                } elseif ($this->isSundayPriceAlreadyAnnounced($current_date)) {
                    echo "วันอาทิตย์ - ประกาศราคาแล้ว (ครั้งที่ 1)\n";
                } else {
                    echo "วันอาทิตย์ - ยังไม่ประกาศราคา (ผิดปกติ - ควรประกาศเวลา 9:00 น.)\n";
                }
            } else {
                echo "นอกเวลาทำการ - ระบบหยุดทำงาน\n";
            }
            return;
        }

        if ($day_of_week == 7) {
            echo "วันอาทิตย์ เวลา 9:xx น. - ใช้ราคาจากวันเสาร์ล่าสุด (ครั้งที่ 1)\n";
            if ($this->insertSaturdayPrice()) {
                $new_id = mysqli_insert_id($this->connection);
                $new_no = $this->getLastInsertedNo($new_id);
                echo "ประกาศราคาจากวันเสาร์สำเร็จ!\n";
                echo "รายการใหม่: ID = {$new_id}, No = {$new_no} (ควรเป็น 1)\n";
            } else {
                echo "เกิดข้อผิดพลาดในการประกาศราคา\n";
            }
            return;
        }

        echo "วันทำงานปกติ - เริ่มตรวจสอบราคาจาก API\n\n";

        $spot_price = $this->getGoldPrice();
        if (!$spot_price) {
            echo "ไม่สามารถดึงราคา Gold API ได้\n";
            return;
        }
        echo "Spot Price: $spot_price USD\n";

        $exchange_rate = $this->getBBLExchangeRate();
        if (!$exchange_rate) {
            echo "ไม่สามารถดึงอัตราแลกเปลี่ยนจาก BBL ได้\n";
            return;
        }
        echo "Exchange Rate (USD50): $exchange_rate THB\n\n";

        $current_prices = $this->getLatestPrice();
        if ($current_prices) {
            echo "ราคาประกาศก่อนหน้า: Sell = " . number_format($current_prices['sell'], 0) . ", Buy = " . number_format($current_prices['buy'], 0) . "\n";
            echo "เวลาอัปเดตล่าสุด: {$current_prices['created']}\n";
        } else {
            echo "ไม่มีราคาก่อนหน้า - จะประกาศเป็นครั้งแรก\n";
        }

        $new_prices = $this->calculateNewPrices($spot_price, $exchange_rate);
        echo "\nราคาใหม่ที่คำนวณได้:\n";
        echo "  ราคาดิบ - Sell: " . number_format($new_prices['sell_raw'], 2) . ", Buy: " . number_format($new_prices['buy_raw'], 2) . "\n";
        echo "  ราคาประกาศ - Sell: " . number_format($new_prices['sell'], 0) . ", Buy: " . number_format($new_prices['buy'], 0) . "\n";

        echo "\nรายละเอียดการคำนวณ:\n";
        echo "  Spot Silver: $spot_price USD\n";
        echo "  PMDC Rate: {$new_prices['pmdc_rate']}\n";
        echo "  Factor: {$new_prices['factor']}\n";
        echo "  Exchange Rate: $exchange_rate THB\n";
        echo "  สูตร: ((Spot + PMDC) × Factor) × Exchange = Base Price\n";
        echo "  Base Price: " . number_format($new_prices['base'], 2) . " THB\n";

        $hundreds = floor($new_prices['sell_raw'] / 100) * 100;
        $remainder = $new_prices['sell_raw'] - $hundreds;
        echo "  การปัดราคาขาย:\n";
        echo "    - ราคาดิบ: " . number_format($new_prices['sell_raw'], 2) . "\n";
        echo "    - หลักร้อย: " . number_format($hundreds, 0) . "\n";
        echo "    - เศษ: " . number_format($remainder, 2) . "\n";

        if ($remainder < 0.25) {
            echo "    - กฎ: เศษ < 0.25 → คงเดิม → " . number_format($new_prices['sell'], 0) . "\n";
        } else if ($remainder <= 50) {
            echo "    - กฎ: 0.25 ≤ เศษ ≤ 50 → ปัดขึ้น 50 → " . number_format($new_prices['sell'], 0) . "\n";
        } else {
            echo "    - กฎ: เศษ > 50 → ปัดขึ้น 100 → " . number_format($new_prices['sell'], 0) . "\n";
        }

        echo "  Buy = Sell + change_buy ({$new_prices['change_buy']}) = " . number_format($new_prices['buy'], 0) . "\n\n";

        if ($this->checkPriceDifference($current_prices, $new_prices)) {
            echo "✅ ราคาดิบใหม่ห่างจากราคาประกาศก่อนหน้า >= {$this->min_price_gap} บาท - กำลัง Insert ข้อมูล...\n";

            if ($this->insertNewPrice($new_prices, $spot_price, $exchange_rate)) {
                $new_id = mysqli_insert_id($this->connection);
                $new_no = $this->getLastInsertedNo($new_id);
                echo "✅ Insert ข้อมูลสำเร็จ!\n";
                echo "รายการใหม่: ID = {$new_id}, No = {$new_no}\n";
                echo "ราคาที่ประกาศ: Sell = " . number_format($new_prices['sell'], 0) . ", Buy = " . number_format($new_prices['buy'], 0) . "\n";
            } else {
                echo "❌ เกิดข้อผิดพลาดในการ Insert ข้อมูล: " . mysqli_error($this->connection) . "\n";
            }
        } else {
            echo "❌ ราคาดิบใหม่ห่างจากราคาประกาศก่อนหน้า < {$this->min_price_gap} บาท - ไม่ประกาศ\n";
            if ($current_prices) {
                echo "ราคาประกาศจะยังคงเป็น: " . number_format($current_prices['sell'], 0) . " บาท\n";
            }
        }

        echo "สิ้นสุดการตรวจสอบ\n\n";
    }

    public function __destruct()
    {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}

if (isset($_GET['trigger']) || isset($_GET['web_trigger'])) {
    header('Content-Type: text/plain; charset=utf-8');

    echo "=== Silver Price Monitor Web Trigger ===\n";
    echo "เวลา: " . date('Y-m-d H:i:s') . "\n";
    echo "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
    echo "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n\n";

    try {
        $monitor = new PriceMonitor();
        $monitor->monitorPrices();
        echo "\n=== เสร็จสิ้นการทำงาน ===\n";
    } catch (Exception $e) {
        echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
        http_response_code(500);
    }

    exit;
}

if (php_sapi_name() === 'cli') {
    $monitor = new PriceMonitor();
    $monitor->monitorPrices();
}

echo "<!DOCTYPE html>
<html>
<head>
<title>Silver Price Monitor</title>
<meta charset='utf-8'>
<style>
    body { 
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
        max-width: 1000px; 
        margin: 50px auto; 
        padding: 30px; 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    .container {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .status { 
        background: linear-gradient(135deg, #e8f5e8, #d4edda); 
        padding: 20px; 
        border-radius: 10px; 
        margin: 20px 0; 
        border-left: 5px solid #28a745;
    }
    .test-btn { 
        background: linear-gradient(135deg, #007cba, #0056b3); 
        color: white; 
        padding: 12px 25px; 
        text-decoration: none; 
        border-radius: 8px; 
        display: inline-block; 
        margin: 10px 8px; 
        transition: all 0.3s;
        font-weight: 600;
    }
    .test-btn:hover { 
        background: linear-gradient(135deg, #0056b3, #004085); 
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,123,186,0.3);
    }
    pre { 
        background: #f8f9fa; 
        padding: 20px; 
        border-radius: 8px; 
        overflow-x: auto; 
        border: 1px solid #e9ecef;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }
    .new-feature { 
        background: linear-gradient(135deg, #d1ecf1, #b8daff); 
        padding: 25px; 
        border-radius: 10px; 
        margin: 20px 0; 
        border-left: 5px solid #17a2b8;
    }
    .update-info {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        padding: 25px;
        border-radius: 10px;
        margin: 20px 0;
        border-left: 5px solid #ffc107;
    }
    h1 { 
        color: #2c3e50; 
        text-align: center; 
        margin-bottom: 10px;
        font-size: 2.2em;
    }
    h3 { 
        color: #34495e; 
        margin-top: 30px;
    }
    .feature-list {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 15px 0;
    }
    .feature-list li {
        margin: 8px 0;
        padding-left: 10px;
    }
    .highlight {
        background: #fff3cd;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
        color: #856404;
    }
    .rounding-rule {
        background: #e3f2fd;
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
        border-left: 5px solid #2196f3;
    }
    .cache-info {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        padding: 25px;
        border-radius: 10px;
        margin: 20px 0;
        border-left: 5px solid #28a745;
    }
</style>
</head>
<body>
<div class='container'>
<h1>🥈 Silver Price Monitor</h1>
<div class='status'>
    <strong>ระบบพร้อมใช้งาน</strong><br>
    เวลาปัจจุบัน: " . date('Y-m-d H:i:s') . "<br>
    เกณฑ์การประกาศ: ราคาดิบใหม่ ห่างจากราคาประกาศก่อนหน้า ≥ 100 บาท<br>
    APCu Cache: " . (function_exists('apcu_enabled') && apcu_enabled() ? "✅ เปิดใช้งาน" : "❌ ปิดใช้งาน") . "
</div>

<div class='cache-info'>
    <strong>🚀 APCu Cache System</strong><br>
    <div class='feature-list'>
        <ul>
            <li><strong>Cache TTL:</strong> 60 วินาที</li>
            <li><strong>Gold API Cache:</strong> ลด API calls ได้ ~96%</li>
            <li><strong>BBL API Cache:</strong> ลด API calls ได้ ~96%</li>
            <li><strong>ป้องกัน 429 Error:</strong> Exponential backoff (1s → 2s → 4s)</li>
            <li><strong>Retry:</strong> 3 ครั้งต่อ API call</li>
        </ul>
    </div>
</div>

<div class='new-feature'>
    <strong>ระบบเปรียบเทียบราคาแบบใหม่</strong><br>
    <div class='feature-list'>
        <ul>
            <li><strong>เปรียบเทียบ:</strong> ราคาดิบใหม่ vs ราคาประกาศก่อนหน้า</li>
            <li><strong>เกณฑ์:</strong> ห่างกัน ≥ <span class='highlight'>100 บาท</span> ถึงจะประกาศ</li>
            <li><strong>Telegram:</strong> แสดงราคาดิบและการเปรียบเทียบแบบละเอียด</li>
            <li><strong>ผลลัพธ์:</strong> ลดการประกาศที่ไม่จำเป็น 70-80%</li>
        </ul>
    </div>
</div>

<div class='rounding-rule'>
    <strong>กฎการปัดราคาใหม่</strong><br>
    <div class='feature-list'>
        <ul>
            <li><strong>0.00-0.24:</strong> คงเท่าเดิม (xx,x00)</li>
            <li><strong>0.25-50.00:</strong> ปัดขึ้น 50 (xx,x50)</li>
            <li><strong>50.01-99.99:</strong> ปัดขึ้น 100 (xx,(x+1)00)</li>
        </ul>
        <strong>ตัวอย่าง:</strong> 40,228.70 → เศษ 28.70 → ปัดขึ้น 50 → <span class='highlight'>40,250</span>
    </div>
</div>

<h3>URL สำหรับ Cron Job</h3>
<pre>" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?trigger=1</pre>

</div>
</body>
</html>";
