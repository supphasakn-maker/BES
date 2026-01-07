<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

date_default_timezone_set('Asia/Bangkok');

final class Config
{
    public static string $db_host = '192.168.1.92';
    public static string $db_user = 'erp-user';
    public static string $db_pass = 'T6p$4u4vcf';
    public static string $db_name = 'erp_main';

    public static array $gold_api = [
        'url'     => 'https://www.goldapi.io/api/XAG/THB',
        'headers' => ['x-access-token: goldapi-ba7qhzsm0yxfabu-io']
    ];

    public const TROY_OUNCE_TO_GRAM = 32.1507;
    public const ADDON    = 0.0000;
    public const TAX_RATE = 0.07;

    public const SERVICE_START_HOUR   = 9;
    public const SERVICE_START_MINUTE = 0;
    public const SERVICE_END_HOUR     = 17;
    public const SERVICE_END_MINUTE   = 30;

    public const CACHE_TTL = 60; // วินาที
}

function check_service_window(): array
{
    $tz   = new DateTimeZone('Asia/Bangkok');
    $now  = new DateTime('now', $tz);

    $start = (clone $now)->setTime(Config::SERVICE_START_HOUR, Config::SERVICE_START_MINUTE, 0);
    $end   = (clone $now)->setTime(Config::SERVICE_END_HOUR,   Config::SERVICE_END_MINUTE,   59);

    if ($now >= $start && $now <= $end) {
        return [true, ''];
    }

    if ($now < $start) {
        $nextOpen = $start;
    } else {
        $nextOpen = (clone $now)->modify('+1 day')->setTime(Config::SERVICE_START_HOUR, Config::SERVICE_START_MINUTE, 0);
    }

    return [false, $nextOpen->format(DateTime::ATOM)];
}

final class DB
{
    private mysqli $conn;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->conn = mysqli_init();
        if (!$this->conn) {
            $this->fail('DB_INIT_ERROR', 'mysqli_init failed');
        }

        $this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

        if (!$this->conn->real_connect(
            Config::$db_host,
            Config::$db_user,
            Config::$db_pass,
            Config::$db_name
        )) {
            $this->fail('DB_CONNECT_ERROR', mysqli_connect_error());
        }

        $this->conn->set_charset('utf8mb4');
    }

    public function __destruct()
    {
        if (isset($this->conn)) {
            $this->conn->close();
        }
    }

    public function getPmdcGrains(): float
    {
        $sql = "SELECT value FROM os_variable WHERE name='pmdc_grains' LIMIT 1";
        $res = $this->conn->query($sql);

        if ($res === false) {
            error_log('getPmdcGrains query error: ' . $this->conn->error);
            return 0.0;
        }

        $row = $res->fetch_assoc();
        $res->free();

        return isset($row['value']) ? (float)$row['value'] : 0.0;
    }

    private function fail(string $code, string $msg, int $http = 500): void
    {
        http_response_code($http);
        echo json_encode(['status' => 'error', 'code' => $code, 'message' => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

final class GoldAPIClient
{
    private const CACHE_KEY = 'gold_xag_thb_price';

    public static function getPrice(): array
    {
        if (function_exists('apcu_fetch')) {
            $cached = apcu_fetch(self::CACHE_KEY, $success);
            if ($success && is_array($cached)) {
                return $cached;
            }
        }

        // เรียก API
        $data = self::fetchFromAPI();

        // เก็บใน APCu
        if (function_exists('apcu_store')) {
            apcu_store(self::CACHE_KEY, $data, Config::CACHE_TTL);
        }

        return $data;
    }

    private static function fetchFromAPI(): array
    {
        $maxRetries = 3;
        $lastError = null;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => Config::$gold_api['url'],
                CURLOPT_HTTPHEADER     => Config::$gold_api['headers'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 3,
            ]);

            $raw  = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Success
            if ($raw !== false && $http >= 200 && $http < 300) {
                $data = json_decode($raw, true);

                if (!is_array($data)) {
                    $lastError = 'Invalid JSON response from GoldAPI';
                    continue;
                }

                if (isset($data['error'])) {
                    $lastError = 'GoldAPI error: ' . $data['error'];
                    continue;
                }

                if (!isset($data['price']) || $data['price'] === null) {
                    $lastError = 'GoldAPI warming up or price not available';
                    continue;
                }

                return $data;
            }

            // Handle 429 (Rate Limit)
            if ($http === 429) {
                $lastError = 'Rate limit exceeded (429)';
                if ($attempt < $maxRetries - 1) {
                    $delay = pow(2, $attempt); // 1s, 2s, 4s
                    sleep($delay);
                    continue;
                }
            }

            // Error อื่นๆ
            $lastError = $raw === false
                ? ('cURL error: ' . $curlError)
                : ('HTTP ' . $http);

            if ($attempt < $maxRetries - 1) {
                usleep(500 * 1000);
            }
        }

        // ล้มเหลวทั้งหมด
        http_response_code(502);
        echo json_encode([
            'status' => 'error',
            'code' => 'GOLDAPI_ERROR',
            'message' => $lastError ?? 'Failed to fetch gold price'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function roundToNearest50or00(float $price): float
{
    return round($price / 50) * 50;
}

// ---------- MAIN ----------
try {
    [$ok, $nextOpenISO] = check_service_window();
    if (!$ok) {
        http_response_code(403);
        echo json_encode([
            'status'        => 'closed',
            'message'       => 'ให้บริการเฉพาะเวลา 09:00–17:30 (Asia/Bangkok)',
            'next_open_at'  => $nextOpenISO
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $gold = GoldAPIClient::getPrice();
    $basePrice = (float)$gold['price'];

    $db = new DB();
    $pmdc = $db->getPmdcGrains();

    $price_calc = (($basePrice + Config::ADDON) * Config::TROY_OUNCE_TO_GRAM) + $pmdc;
    $price = roundToNearest50or00($price_calc);

    $tax = $price * Config::TAX_RATE;
    $include_tax = roundToNearest50or00($price + $tax);

    echo json_encode([
        'price'        => (float)$price,
        'tax'          => round($tax, 2),
        'include_tax'  => (float)$include_tax
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'code' => 'INTERNAL_ERROR',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
