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
    // แนะนำให้ย้ายไป ENV ในอนาคต แต่คงค่าเดิมไว้ตามที่ให้มา
    public static string $db_host = 'localhost';
    public static string $db_user = 'silver_now';
    public static string $db_pass = '\aMc"v655AbF';
    public static string $db_name = 'erp_main';

    public static array $gold_api = [
        'url'     => 'https://www.goldapi.io/api/XAG/THB',
        'headers' => ['x-access-token: goldapi-ba7qhzsm0yxfabu-io']
    ];

    public const TROY_OUNCE_TO_GRAM = 32.1507;
    public const ADDON    = 1.8000;
    public const TAX_RATE = 0.07;
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

        // กัน connect ค้าง (วินาที)
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
            $this->conn->close(); // ปิดชัวร์ ๆ ทุกครั้ง
        }
    }

    public function getPmdcGrains(): float
    {
        $sql = "SELECT value FROM os_variable WHERE name='pmdc_grains' LIMIT 1";
        $res = $this->conn->query($sql);

        if ($res === false) {
            // ไม่ทำให้ทั้ง API ล้ม — คืน 0 และ log error ไว้
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

function roundToNearest50or00(float $price): float
{
    return round($price / 50) * 50;
}

function fetch_gold_api(): array
{
    $attempts = 2;
    $lastErr = null;

    while ($attempts-- > 0) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => Config::$gold_api['url'],
            CURLOPT_HTTPHEADER     => Config::$gold_api['headers'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10, // รวมเวลาอ่าน
            CURLOPT_CONNECTTIMEOUT => 3,  // เวลาเชื่อมต่อ
        ]);

        $raw  = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($raw !== false && $http >= 200 && $http < 300) {
            curl_close($ch);
            $data = json_decode($raw, true);
            if (is_array($data) && isset($data['price'])) {
                return $data;
            }
            $lastErr = 'GoldAPI invalid response';
        } else {
            $lastErr = $raw === false ? curl_error($ch) : ('HTTP ' . $http);
            curl_close($ch);
        }

        // หน่วงก่อนลองใหม่เล็กน้อย
        usleep(200 * 1000);
    }

    http_response_code(502);
    echo json_encode(['status' => 'error', 'message' => $lastErr ?? 'GoldAPI error'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $gold = fetch_gold_api();
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
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
