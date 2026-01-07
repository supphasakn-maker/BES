<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
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
}

final class Database
{
    private static ?mysqli $connection = null;

    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            self::$connection = new mysqli(
                Config::$db_host,
                Config::$db_user,
                Config::$db_pass,
                Config::$db_name
            );

            if (self::$connection->connect_error) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed'
                ]);
                exit;
            }

            self::$connection->set_charset('utf8mb4');
        }

        return self::$connection;
    }
}

final class InsuranceAPI
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getInsuranceValues(): array
    {
        $result = [
            'success' => true,
            'data' => [
                'insure_15' => null,
                'insure_50' => null,
                'insure_150' => null
            ]
        ];

        $insuranceTypes = ['insure_15', 'insure_50', 'insure_150'];

        foreach ($insuranceTypes as $type) {
            $stmt = $this->db->prepare(
                "SELECT value FROM os_variable WHERE name = ? LIMIT 1"
            );

            if ($stmt === false) {
                $result['success'] = false;
                $result['message'] = 'Prepare statement failed';
                return $result;
            }

            $stmt->bind_param('s', $type);
            $stmt->execute();
            $queryResult = $stmt->get_result();

            if ($row = $queryResult->fetch_assoc()) {
                $result['data'][$type] = $row['value'];
            }

            $stmt->close();
        }

        return $result;
    }
}

try {
    $api = new InsuranceAPI();
    $response = $api->getInsuranceValues();

    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
