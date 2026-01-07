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


class DatabaseConfig {
    private const HOST = 'localhost';
    private const DB_NAME = 'erp_main';
    private const USERNAME = 'silver_now';
    private const PASSWORD = '\aMc"v655AbF';
    
    public static function getConnection(): ?PDO {
        try {
            $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, self::USERNAME, self::PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            return null;
        }
    }
}


class ApiResponse {
    public static function success(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function error(string $message, int $statusCode = 400): void {
        http_response_code($statusCode);
        echo json_encode([
            'error' => true,
            'message' => $message,
            'status_code' => $statusCode
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}


class SIlverBarService {
    private PDO $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function checkCode(string $code): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM `bs_packing_items` WHERE `code` = :code LIMIT 1");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return null;
        }
    }
}


class SimpleRestApi {
    private string $method;
    private array $queryParams;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->queryParams = $_GET;
    }
    
    public function handle(): void {
        
        $action = $this->queryParams['action'] ?? '';
        
        switch ($this->method) {
            case 'GET':
                $this->handleGet($action);
                break;
            default:
                ApiResponse::error('Method not allowed. Use GET method only.', 405);
        }
    }
    
    private function handleGet(string $action): void {
        switch ($action) {
            case 'check':
            case 'codes':
            case '':
                $this->checkBarCode();
                break;
            default:
                ApiResponse::error("Code parameter is required", 400);
        }
    }
    
    private function checkBarCode(): void {
        
        $code = $this->queryParams['code'] ?? '';
        
        
        if (empty($code)) {
            ApiResponse::error('Code parameter is required. Usage: ?code=A002664', 400);
        }
        
        
        $code = trim($code);
        if (strlen($code) > 50) {
            ApiResponse::error('Code is too long (max 50 characters)', 400);
        }
        
        
        $db = DatabaseConfig::getConnection();
        if (!$db) {
            ApiResponse::error('Database connection failed', 500);
        }
        
        
        $service = new SIlverBarService($db);
        $result = $service->checkCode($code);
        
        if ($result) {
            ApiResponse::success([
                'success' => true,
                'code' => $result['code'],
                'found' => true,
                'data' => $result
            ]);
        } else {
            ApiResponse::success([
                'success' => false,
                'found' => false
            ]);
        }
    }
    
}


try {
    $api = new SimpleRestApi();
    $api->handle();
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    ApiResponse::error('Internal server error', 500);
}
?>