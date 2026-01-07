<?php

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

define('API_KEY', 'QxZyWvUt8SrPnOlMkJiHgFeD');
define('API_SECRET', 'r4Nd0mStR1n9_ExAmPl3!7z');

$host = '192.168.1.92';
$dbname = 'erp_main';
$user = 'erp-user';
$pass = 'T6p$4u4vcf';

function authenticate(): bool
{
    if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
        return false;
    }
    $sentApiKey    = (string)$_SERVER['PHP_AUTH_USER'];
    $sentApiSecret = (string)$_SERVER['PHP_AUTH_PW'];
    return hash_equals(API_KEY, $sentApiKey) && hash_equals(API_SECRET, $sentApiSecret);
}

mysqli_report(MYSQLI_REPORT_OFF);
try {
    $link = mysqli_init();
    if (!$link) {
        throw new Exception('mysqli_init failed');
    }
    $link->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

    if (!$link->real_connect($host, $user, $pass, $dbname)) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }
    if (!$link->set_charset('utf8mb4')) {
        error_log('set_charset utf8mb4 failed: ' . mysqli_error($link));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
    exit;
}

register_shutdown_function(function () use (&$link) {
    if ($link instanceof mysqli) {
        @$link->close();
    }
});

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod !== 'GET' && $requestMethod !== 'OPTIONS') {
    if (!authenticate()) {
        header('WWW-Authenticate: Basic realm="API Authentication"');
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

switch ($requestMethod) {
    case 'GET':
        $sql = "SELECT *,
                       (LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS',
                       (buy - LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS_PRICE'
                FROM bs_announce_silver
                WHERE status = 1
                ORDER BY id DESC
                LIMIT 1";
        try {
            $result = $link->query($sql);
            if ($result === false) {
                throw new Exception("Database query failed: " . $link->error);
            }
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            http_response_code(200);
            echo json_encode($data);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
        break;

    case 'POST':
        http_response_code(201);
        echo json_encode(['message' => 'POST request handled']);
        break;

    case 'PUT':
        http_response_code(200);
        echo json_encode(['message' => 'PUT request handled']);
        break;

    case 'DELETE':
        http_response_code(200);
        echo json_encode(['message' => 'DELETE request handled']);
        break;

    case 'OPTIONS':
        http_response_code(200);
        exit;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
