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

class SimpleAPI
{
    private mysqli $db;
    private string $requestId;
    private array $config = [
        'db_host' => 'localhost',
        'db_user' => 'silver_now',
        'db_pass' => '\aMc"v655AbF',
        'db_name' => 'erp_main',
        'auth_token' => 'a1b2c3d4-e5f6-7890-1234-567890abcdef',
        'thailand_post_token' => 'MBEyP_E9Q!WBCKWcR;NjZMJ5CrCcWKDIF5SpXcZ2WgBbB?TDExS=FlAbRgQFPMSYHeUCE5JoKZR^C7BjKNZ5MWJnT0QTZrHWNAA1'
    ];

    private array $allowedPlatforms = ['Exhibition', 'LINE', 'Facebook', 'Website', 'SilverNow', 'WalkIN'];

    public function __construct()
    {
        $this->requestId = bin2hex(random_bytes(8));
        $this->initDatabase();
    }

    private function initDatabase(): void
    {
        $this->db = new mysqli(
            $this->config['db_host'],
            $this->config['db_user'],
            $this->config['db_pass'],
            $this->config['db_name']
        );

        if ($this->db->connect_error) {
            $this->error(500, 'Database connection failed');
        }

        $this->db->set_charset('utf8mb4');
    }

    private function authenticate(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return false;
        }

        $token = substr($authHeader, 7);
        return hash_equals($this->config['auth_token'], $token);
    }

    private function success(string $message, $data = null, array $meta = []): void
    {
        http_response_code(200);
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => date('c'),
            'request_id' => $this->requestId
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($meta) {
            $response['meta'] = $meta;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function error(int $code, string $message, array $details = []): void
    {
        http_response_code($code);
        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'timestamp' => date('c'),
                'request_id' => $this->requestId
            ]
        ];

        if ($details) {
            $response['error']['details'] = $details;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ---------- Utilities ----------

    private function parseTrackingNumbers(string $trackingString): array
    {
        if (empty($trackingString)) {
            return [];
        }

        $trackingNumbers = array_map('trim', explode(',', $trackingString));
        return array_filter($trackingNumbers, function ($tracking) {
            return !empty($tracking) && strlen($tracking) > 5;
        });
    }

    private function buildPlatformWhitelist(): array
    {
        $placeholders = implode(',', array_fill(0, count($this->allowedPlatforms), '?'));
        $where = "o.platform IN ($placeholders)";
        $params = $this->allowedPlatforms;
        return [$where, $params];
    }

    // ---------- Thailand Post API ----------

    private function authenticateThailandPost(): ?string
    {
        try {
            $url = "https://trackapi.thailandpost.co.th/post/api/v1/authenticate/token";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Token ' . $this->config['thailand_post_token'],
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode([])
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response === false || !empty($error)) {
                error_log("Thailand Post Auth Error: " . $error);
                return null;
            }

            if ($httpCode !== 200) {
                error_log("Thailand Post Auth HTTP Error: " . $httpCode . " - " . $response);
                return null;
            }

            $data = json_decode($response, true);

            if (!$data || !isset($data['token'])) {
                error_log("Thailand Post Auth Response Error: " . $response);
                return null;
            }

            return $data['token'];
        } catch (Exception $e) {
            error_log("Thailand Post Auth Exception: " . $e->getMessage());
            return null;
        }
    }

    private function trackWithThailandPost(string $trackingNumber, string $accessToken): ?array
    {
        try {
            $url = "https://trackapi.thailandpost.co.th/post/api/v1/track";

            $attempts = [
                [
                    'headers' => [
                        'Authorization: Bearer ' . $accessToken,
                        'Content-Type: application/json'
                    ],
                    'data' => json_encode([
                        "status" => "all",
                        "language" => "TH",
                        "barcode" => [$trackingNumber]
                    ])
                ],
                [
                    'headers' => [
                        'Authorization: Token ' . $accessToken,
                        'Content-Type: application/json'
                    ],
                    'data' => json_encode([
                        "status" => "all",
                        "language" => "TH",
                        "barcode" => [$trackingNumber]
                    ])
                ],
                [
                    'headers' => [
                        'Authorization: Bearer ' . $accessToken,
                        'Content-Type: application/json'
                    ],
                    'data' => json_encode([
                        "barcode" => $trackingNumber,
                        "language" => "TH"
                    ])
                ],
                [
                    'headers' => [
                        'Authorization: Token ' . $this->config['thailand_post_token'],
                        'Content-Type: application/json'
                    ],
                    'data' => json_encode([
                        "status" => "all",
                        "language" => "TH",
                        "barcode" => [$trackingNumber]
                    ])
                ]
            ];

            foreach ($attempts as $index => $attempt) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTPHEADER => $attempt['headers'],
                    CURLOPT_POSTFIELDS => $attempt['data']
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($response !== false && empty($error) && $httpCode === 200) {
                    $data = json_decode($response, true);

                    if ($data && isset($data['response']['items'])) {
                        $trackingEvents = [];
                        $latestStatus = '';
                        $latestStatusDescription = '';

                        if (isset($data['response']['items'][$trackingNumber])) {
                            $items = $data['response']['items'][$trackingNumber];

                            usort($items, function ($a, $b) {
                                $timeA = strtotime($a['status_date'] ?? '');
                                $timeB = strtotime($b['status_date'] ?? '');
                                return $timeB - $timeA;
                            });

                            foreach ($items as $item) {
                                $trackingEvents[] = [
                                    'date' => $item['status_date'] ?? '',
                                    'description' => $item['status_description'] ?? '',
                                    'detail' => $item['status_detail'] ?? '',
                                    'location' => $item['location'] ?? '',
                                    'status_code' => $item['status'] ?? '',
                                    'postcode' => $item['postcode'] ?? ''
                                ];
                            }

                            if (!empty($items)) {
                                $latestItem = $items[0];
                                $latestStatusDescription = $latestItem['status_description'] ?? '';
                                $latestStatusCode = $latestItem['status'] ?? '';

                                switch ($latestStatusCode) {
                                    case '103':
                                        $latestStatus = 'processing';
                                        break;
                                    case '201':
                                    case '202':
                                    case '301':
                                        $latestStatus = 'in_transit';
                                        break;
                                    case '401':
                                        $latestStatus = 'out_for_delivery';
                                        break;
                                    case '501':
                                    case '502':
                                        $latestStatus = 'delivered';
                                        break;
                                    default:
                                        $desc = strtolower($latestStatusDescription);
                                        if (
                                            strpos($desc, 'นำจ่าย') !== false ||
                                            strpos($desc, 'delivered') !== false ||
                                            strpos($desc, 'ได้รับแล้ว') !== false
                                        ) {
                                            $latestStatus = 'delivered';
                                        } elseif (
                                            strpos($desc, 'ออกจัดส่ง') !== false ||
                                            strpos($desc, 'out for delivery') !== false
                                        ) {
                                            $latestStatus = 'out_for_delivery';
                                        } elseif (
                                            strpos($desc, 'ระหว่างทาง') !== false ||
                                            strpos($desc, 'ถึงที่ทำการ') !== false ||
                                            strpos($desc, 'ออกจากที่ทำการ') !== false ||
                                            strpos($desc, 'in transit') !== false
                                        ) {
                                            $latestStatus = 'in_transit';
                                        } elseif (
                                            strpos($desc, 'รับฝาก') !== false ||
                                            strpos($desc, 'accept') !== false
                                        ) {
                                            $latestStatus = 'processing';
                                        } else {
                                            $latestStatus = 'tracked';
                                        }
                                        break;
                                }
                            }
                        }

                        if (empty($trackingEvents)) {
                            $latestStatus = 'unknown';
                            $latestStatusDescription = 'ไม่ทราบสถานะ';
                        }

                        return [
                            'tracking_number' => $trackingNumber,
                            'status' => $latestStatus,
                            'status_description' => $latestStatusDescription,
                            'events' => $trackingEvents,
                            'last_updated' => date('c'),
                            'source' => 'thailand_post_api'
                        ];
                    }
                }
            }

            return [
                'tracking_number' => $trackingNumber,
                'status' => 'all_methods_failed',
                'status_description' => 'ลองทุกวิธีแล้ว ไม่สามารถติดตามพัสดุได้',
                'events' => [],
                'last_updated' => date('c'),
                'source' => 'thailand_post_api'
            ];
        } catch (Exception $e) {
            return [
                'tracking_number' => $trackingNumber,
                'status' => 'exception',
                'status_description' => 'Exception: ' . $e->getMessage(),
                'events' => [],
                'last_updated' => date('c'),
                'source' => 'thailand_post_api'
            ];
        }
    }

    private function getTrackingInfo(string $trackingString): array
    {
        if (empty($trackingString)) {
            return [];
        }

        $trackingNumbers = $this->parseTrackingNumbers($trackingString);

        if (empty($trackingNumbers)) {
            return [];
        }

        if (count($trackingNumbers) === 1) {
            $result = $this->getSingleTrackingInfo($trackingNumbers[0]);
            return $result ? [$result] : [];
        }

        $allTrackingInfo = [];
        $accessToken = $this->authenticateThailandPost();

        if (!$accessToken) {
            foreach ($trackingNumbers as $trackingNumber) {
                $allTrackingInfo[] = [
                    'tracking_number' => $trackingNumber,
                    'status' => 'auth_failed',
                    'status_description' => 'ไม่สามารถ authenticate กับ Thailand Post API ได้',
                    'events' => [],
                    'last_updated' => date('c'),
                    'source' => 'error'
                ];
            }
            return $allTrackingInfo;
        }

        foreach ($trackingNumbers as $trackingNumber) {
            $trackingData = $this->trackWithThailandPost($trackingNumber, $accessToken);

            if ($trackingData) {
                $allTrackingInfo[] = $trackingData;
            } else {
                $allTrackingInfo[] = [
                    'tracking_number' => $trackingNumber,
                    'status' => 'track_failed',
                    'status_description' => 'Authentication สำเร็จ แต่ไม่สามารถติดตามพัสดุได้',
                    'events' => [],
                    'last_updated' => date('c'),
                    'source' => 'error'
                ];
            }
        }

        return $allTrackingInfo;
    }

    private function getSingleTrackingInfo(string $trackingNumber): ?array
    {
        if (empty($trackingNumber)) {
            return null;
        }

        $accessToken = $this->authenticateThailandPost();

        if (!$accessToken) {
            return [
                'tracking_number' => $trackingNumber,
                'status' => 'auth_failed',
                'status_description' => 'ไม่สามารถ authenticate กับ Thailand Post API ได้',
                'events' => [],
                'last_updated' => date('c'),
                'source' => 'error'
            ];
        }

        $trackingData = $this->trackWithThailandPost($trackingNumber, $accessToken);

        if ($trackingData) {
            return $trackingData;
        }

        return [
            'tracking_number' => $trackingNumber,
            'status' => 'track_failed',
            'status_description' => 'Authentication สำเร็จ แต่ไม่สามารถติดตามพัสดุได้',
            'events' => [],
            'last_updated' => date('c'),
            'source' => 'error'
        ];
    }

    // ---------- Queries (BWD main + sub) ----------

    private function getSubOrdersWithProducts(int $mainOrderId): array
    {
        $sql = "
            SELECT 
                sub.id, sub.amount, sub.total, sub.net, sub.product_id, sub.product_type,
                COALESCE(p.name, 'ไม่ระบุสินค้า') as product_name,
                COALESCE(pt.name, 'ไม่ระบุประเภท') as product_type_name
            FROM bs_orders_bwd sub
            LEFT JOIN bs_products_bwd p ON sub.product_id = p.id
            LEFT JOIN bs_products_type pt ON sub.product_type = pt.id
            WHERE sub.parent = ? AND sub.status > 0
            ORDER BY sub.id ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Sub orders query preparation failed: " . $this->db->error);
            return [];
        }

        $stmt->bind_param('i', $mainOrderId);
        if (!$stmt->execute()) {
            error_log("Sub orders query execution failed: " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        $subOrders = [];

        while ($row = $result->fetch_assoc()) {
            $subOrders[] = [
                'sub_order_id' => (int)$row['id'],
                'amount' => (float)$row['amount'],
                'total' => (float)$row['total'],
                'net' => (float)$row['net'],
                'product_id' => $row['product_id'] ? (int)$row['product_id'] : null,
                'product_name' => $row['product_name'],
                'product_type_name' => $row['product_type_name'],
                'has_product_id' => !empty($row['product_id']),
                'has_product_type' => !empty($row['product_type'])
            ];
        }

        $stmt->close();
        return $subOrders;
    }

    private function queryOrders(string $where = '', array $params = []): array
    {
        $sql = "
            SELECT 
                o.id, o.code, o.customer_name, o.phone, o.shipping_address,
                o.Tracking as tracking, o.date, o.amount, o.total, o.net, o.status,
                o.delivery_date, o.created, o.updated, o.product_id, o.product_type,
                o.platform,
                COALESCE(p.name, 'ไม่ระบุสินค้า') as product_name,
                COALESCE(pt.name, 'ไม่ระบุประเภท') as product_type_name,
                (SELECT COUNT(*) FROM bs_orders_bwd sub WHERE sub.parent = o.id AND sub.status > 0) as sub_order_count,
                (SELECT COALESCE(SUM(sub.amount), 0) FROM bs_orders_bwd sub WHERE sub.parent = o.id AND sub.status > 0) as sub_amount_total,
                (SELECT COALESCE(SUM(sub.total), 0) FROM bs_orders_bwd sub WHERE sub.parent = o.id AND sub.status > 0) as sub_total_total,
                (SELECT COALESCE(SUM(sub.net), 0) FROM bs_orders_bwd sub WHERE sub.parent = o.id AND sub.status > 0) as sub_net_total
            FROM bs_orders_bwd o
            LEFT JOIN bs_products_bwd p ON o.product_id = p.id  
            LEFT JOIN bs_products_type pt ON o.product_type = pt.id
            WHERE o.status > 0 
              AND (o.parent IS NULL OR o.parent = 0)
              " . ($where ? " AND $where" : "") . "
            ORDER BY o.id DESC 
            LIMIT 10";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            $this->error(500, 'Query preparation failed: ' . $this->db->error);
        }

        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $this->error(500, 'Query execution failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $subOrderCount  = (int)$row['sub_order_count'];
            $subAmountTotal = (float)$row['sub_amount_total'];
            $subTotalTotal  = (float)$row['sub_total_total'];
            $subNetTotal    = (float)$row['sub_net_total'];

            $finalAmount = $subOrderCount > 0 ? (float)$row['amount'] + $subAmountTotal : (float)$row['amount'];
            $finalTotal  = $subOrderCount > 0 ? (float)$row['total'] + $subTotalTotal : (float)$row['total'];
            $finalNet    = $subOrderCount > 0 ? (float)$row['net'] + $subNetTotal : (float)$row['net'];

            $orderData = [
                'id' => (int)$row['id'],
                'code' => $row['code'],
                'customer_name' => $row['customer_name'],
                'phone' => $row['phone'],
                'shipping_address' => $row['shipping_address'],
                'tracking' => $row['tracking'] ?: '',
                'date' => $row['date'],
                'amount' => $finalAmount,
                'total' => $finalTotal,
                'net' => $finalNet,
                'status' => (int)$row['status'],
                'delivery_date' => $row['delivery_date'],
                'created' => $row['created'],
                'updated' => $row['updated'],
                'platform' => $row['platform'] ?: 'Unknown',
                'sub_order_count' => $subOrderCount,
                'has_sub_orders' => $subOrderCount > 0
            ];

            $allProducts = [];

            if (!empty($row['product_id'])) {
                $allProducts[] = [
                    'source' => 'main_order',
                    'order_id' => (int)$row['id'],
                    'product_id' => (int)$row['product_id'],
                    'product_name' => $row['product_name'],
                    'product_type_name' => $row['product_type_name'],
                    'amount' => (float)$row['amount'],
                    'total' => (float)$row['total'],
                    'net' => (float)$row['net']
                ];
            }

            if ($subOrderCount > 0) {
                $subOrders = $this->getSubOrdersWithProducts((int)$row['id']);
                foreach ($subOrders as $subOrder) {
                    $allProducts[] = [
                        'source' => 'sub_order',
                        'order_id' => $subOrder['sub_order_id'],
                        'product_id' => $subOrder['product_id'],
                        'product_name' => $subOrder['product_name'],
                        'product_type_name' => $subOrder['product_type_name'],
                        'amount' => $subOrder['amount'],
                        'total' => $subOrder['total'],
                        'net' => $subOrder['net']
                    ];
                }
            }

            $orderData['all_products'] = $allProducts;
            $orderData['products_count'] = count($allProducts);

            $productSummary = [];
            foreach ($allProducts as $product) {
                $name = $product['product_name'] ?? 'ไม่ระบุ';
                if (!isset($productSummary[$name])) {
                    $productSummary[$name] = [
                        'product_name' => $name,
                        'product_type_name' => $product['product_type_name'] ?? '',
                        'quantity' => 0,
                        'total_amount' => 0,
                        'sources' => []
                    ];
                }
                $productSummary[$name]['quantity']++;
                $productSummary[$name]['total_amount'] += $product['amount'];
                if (!in_array($product['source'], $productSummary[$name]['sources'])) {
                    $productSummary[$name]['sources'][] = $product['source'];
                }
            }
            $orderData['product_summary'] = array_values($productSummary);

            if (!empty($allProducts)) {
                $mainProducts = array_filter($allProducts, fn($p) => $p['source'] === 'main_order');
                $primary = !empty($mainProducts) ? reset($mainProducts) : $allProducts[0];

                $orderData['product_name'] = $primary['product_name'];
                $orderData['product_type_name'] = $primary['product_type_name'];
                $orderData['primary_product_source'] = $primary['source'];
            } else {
                $orderData['product_name'] = 'ไม่มีสินค้า';
                $orderData['product_type_name'] = 'ไม่ระบุ';
                $orderData['primary_product_source'] = 'none';
            }

            if (!empty($row['tracking'])) {
                $trackingInfo = $this->getTrackingInfo($row['tracking']);
                if (!empty($trackingInfo)) {
                    $orderData['tracking_info'] = $trackingInfo;

                    $statusSummary = [];
                    foreach ($trackingInfo as $track) {
                        $status = $track['status'] ?? 'unknown';
                        $statusSummary[$status] = ($statusSummary[$status] ?? 0) + 1;
                    }
                    $orderData['tracking_summary'] = $statusSummary;
                }
            } else {
                $orderData['tracking_info'] = [];
                $orderData['tracking_summary'] = ['no_tracking' => 1];
            }

            $data[] = $orderData;
        }

        $stmt->close();
        return $data;
    }


    private function queryLegacyOrdersByPhone(string $phonePattern, string $minDeliveryDate = '2025-08-01'): array
    {
        $p1 = $phonePattern;
        $clean = preg_replace('/[^0-9+]/', '', $phonePattern);
        $p2 = "%" . preg_replace('/^\+66/', '0', $clean) . "%";
        $p3 = "%" . preg_replace('/^0/', '+66', $clean) . "%";

        $sql = "
            SELECT 
                o.id,
                o.code,
                c.contact AS customer_name,
                c.phone AS phone,
                c.shipping_address,
                o.Tracking AS tracking,
                o.date,
                o.amount,
                o.total,
                o.net,
                o.status,
                o.delivery_date,
                o.created,
                o.updated
            FROM bs_orders o
            LEFT OUTER JOIN bs_customers c ON o.customer_id = c.id
            WHERE o.status > 0
              AND o.product_id = 2
              AND o.delivery_date IS NOT NULL
              AND o.delivery_date > ?
              AND (
                  c.phone LIKE ?
                  OR REPLACE(REPLACE(REPLACE(c.phone, '-', ''), ' ', ''), '+66', '0') LIKE ?
                  OR REPLACE(REPLACE(REPLACE(c.phone, '-', ''), ' ', ''), '0', '+66') LIKE ?
              )
            ORDER BY o.delivery_date DESC
            LIMIT 50
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            $this->error(500, 'Query preparation failed: ' . $this->db->error);
        }

        $stmt->bind_param('ssss', $minDeliveryDate, $p1, $p2, $p3);
        if (!$stmt->execute()) {
            $this->error(500, 'Query execution failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $tracking = $row['tracking'] ?: '';
            $orderData = [
                'id' => (int)$row['id'],
                'code' => $row['code'],
                'customer_name' => $row['customer_name'] ?: 'ไม่ระบุ',
                'phone' => $row['phone'] ?: '',
                'shipping_address' => $row['shipping_address'] ?: '',
                'tracking' => $tracking,
                'date' => $row['date'],
                'amount' => (float)$row['amount'],
                'total' => (float)$row['total'],
                'net' => (float)$row['net'],
                'status' => (int)$row['status'],
                'delivery_date' => $row['delivery_date'],
                'created' => $row['created'],
                'updated' => $row['updated'],

                'platform' => 'Legacy',
                'sub_order_count' => 0,
                'has_sub_orders' => false,
                'all_products' => [],
                'products_count' => 0,
                'product_summary' => [],
                'product_name' => 'SILVER 1 KILO',
                'product_type_name' => 'ไม่ระบุ',
                'primary_product_source' => 'legacy'
            ];

            if (!empty($tracking)) {
                $trackingInfo = $this->getTrackingInfo($tracking);
                $orderData['tracking_info'] = $trackingInfo;

                $statusSummary = [];
                foreach ($trackingInfo as $track) {
                    $status = $track['status'] ?? 'unknown';
                    $statusSummary[$status] = ($statusSummary[$status] ?? 0) + 1;
                }
                $orderData['tracking_summary'] = $statusSummary ?: ['tracked' => 1];
            } else {
                $orderData['tracking_info'] = [];
                $orderData['tracking_summary'] = ['no_tracking' => 1];
            }

            $data[] = $orderData;
        }

        $stmt->close();
        return $data;
    }

    // ---------- Debug ----------

    private function debugProductsIssue(): array
    {
        $mainWithProductsSql = "
            SELECT COUNT(*) as count
            FROM bs_orders_bwd o
            WHERE o.status > 0 AND (o.parent IS NULL OR o.parent = 0)
              AND o.product_id IS NOT NULL";
        $result1 = $this->db->query($mainWithProductsSql);
        $mainWithProducts = $result1->fetch_assoc()['count'];

        $mainWithoutProductsSql = "
            SELECT COUNT(*) as count
            FROM bs_orders_bwd o
            WHERE o.status > 0 AND (o.parent IS NULL OR o.parent = 0)
              AND o.product_id IS NULL";
        $result2 = $this->db->query($mainWithoutProductsSql);
        $mainWithoutProducts = $result2->fetch_assoc()['count'];

        $subWithProductsSql = "
            SELECT COUNT(*) as count
            FROM bs_orders_bwd o
            WHERE o.status > 0 AND o.parent IS NOT NULL AND o.parent > 0
              AND o.product_id IS NOT NULL";
        $result3 = $this->db->query($subWithProductsSql);
        $subWithProducts = $result3->fetch_assoc()['count'];

        $subWithoutProductsSql = "
            SELECT COUNT(*) as count
            FROM bs_orders_bwd o
            WHERE o.status > 0 AND o.parent IS NOT NULL AND o.parent > 0
              AND o.product_id IS NULL";
        $result4 = $this->db->query($subWithoutProductsSql);
        $subWithoutProducts = $result4->fetch_assoc()['count'];

        $problemCasesSql = "
            SELECT 
                main.id as main_id,
                main.code as main_code,
                main.product_id as main_product_id,
                p_main.name as main_product_name,
                COUNT(sub.id) as sub_count,
                GROUP_CONCAT(sub.product_id) as sub_product_ids,
                GROUP_CONCAT(p_sub.name SEPARATOR ' | ') as sub_product_names
            FROM bs_orders_bwd main
            LEFT JOIN bs_products_bwd p_main ON main.product_id = p_main.id
            LEFT JOIN bs_orders_bwd sub ON main.id = sub.parent AND sub.status > 0
            LEFT JOIN bs_products_bwd p_sub ON sub.product_id = p_sub.id
            WHERE main.status > 0 
              AND (main.parent IS NULL OR main.parent = 0)
            GROUP BY main.id, main.code, main.product_id, p_main.name
            HAVING (main_product_id IS NULL AND sub_count > 0) 
               OR (main_product_id IS NOT NULL AND sub_count > 0)
            ORDER BY main.id DESC
            LIMIT 10";
        $problemCases = [];
        $result5 = $this->db->query($problemCasesSql);
        while ($row = $result5->fetch_assoc()) {
            $problemCases[] = [
                'main_id' => $row['main_id'],
                'main_code' => $row['main_code'],
                'main_product_id' => $row['main_product_id'],
                'main_product_name' => $row['main_product_name'],
                'sub_count' => (int)$row['sub_count'],
                'sub_product_ids' => $row['sub_product_ids'],
                'sub_product_names' => $row['sub_product_names'],
                'issue_type' => $row['main_product_id'] ? 'has_both_main_and_sub' : 'main_empty_has_sub'
            ];
        }

        return [
            'statistics' => [
                'main_orders_with_products' => (int)$mainWithProducts,
                'main_orders_without_products' => (int)$mainWithoutProducts,
                'sub_orders_with_products' => (int)$subWithProducts,
                'sub_orders_without_products' => (int)$subWithoutProducts,
                'total_main_orders' => (int)$mainWithProducts + (int)$mainWithoutProducts,
                'total_sub_orders' => (int)$subWithProducts + (int)$subWithoutProducts
            ],
            'issue_analysis' => [
                'main_only_orders' => (int)$mainWithProducts . ' orders มี Main Product เท่านั้น',
                'sub_only_orders' => (int)$mainWithoutProducts . ' orders ไม่มี Main Product (อาจมีแต่ Sub)',
                'mixed_orders' => 'Orders ที่มีทั้ง Main และ Sub Products'
            ],
            'problem_cases' => $problemCases,
            'fix_status' => 'แก้ไขแล้ว - API จะแสดงทั้ง Main และ Sub Products'
        ];
    }


    public function handleRequest(): void
    {
        if (!empty($_GET['phone'])) {
            $phone = trim($_GET['phone']);
            $clean = preg_replace('/[^0-9+]/', '', $phone);
            $pattern = "%$clean%";

            $wherePhone = "(
                o.phone LIKE ? OR 
                REPLACE(REPLACE(REPLACE(o.phone, '-', ''), ' ', ''), '+66', '0') LIKE ? OR
                REPLACE(REPLACE(REPLACE(o.phone, '-', ''), ' ', ''), '0', '+66') LIKE ?
            )";

            [$wherePlat, $paramsPlat] = $this->buildPlatformWhitelist();
            $where = "$wherePhone AND $wherePlat";

            $params = [$pattern, $pattern, $pattern];
            $params = array_merge($params, $paramsPlat);

            // 1) BWD
            $bwd = $this->queryOrders($where, $params);
            // 2) Legacy
            $legacy = $this->queryLegacyOrdersByPhone($pattern, '2025-08-01');

            $combined = array_merge($bwd, $legacy);
            usort($combined, function ($a, $b) {
                $aKey = $a['delivery_date'] ?: ($a['updated'] ?: $a['created']);
                $bKey = $b['delivery_date'] ?: ($b['updated'] ?: $b['created']);
                return strcmp($bKey, $aKey); // DESC
            });

            $combined = array_slice($combined, 0, 50);

            echo json_encode($combined, JSON_UNESCAPED_UNICODE);
            exit;
        }

        // RESTful
        if (!$this->authenticate()) {
            $this->error(401, 'Authentication required');
        }

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'track_only':
                $tracking = $_GET['tracking'] ?? '';
                if (empty($tracking)) {
                    $this->error(400, 'Tracking number is required');
                }

                $trackingInfo = $this->getTrackingInfo($tracking);

                $this->success('Tracking information retrieved', $trackingInfo, [
                    'tracking_input' => $tracking,
                    'tracking_count' => count($trackingInfo),
                    'source' => 'thailand_post_api'
                ]);
                break;

            case 'test_auth':
                $accessToken = $this->authenticateThailandPost();
                $this->success('Thailand Post authentication test', [
                    'auth_success' => !empty($accessToken),
                    'access_token_length' => $accessToken ? strlen($accessToken) : 0,
                    'access_token_preview' => $accessToken ? substr($accessToken, 0, 20) . '...' : null
                ]);
                break;

            case 'search':
                $type = $_GET['type'] ?? '';
                $value = $_GET['value'] ?? '';

                if ($type === 'phone') {
                    $clean = preg_replace('/[^0-9+]/', '', $value);
                    $pattern = "%$clean%";

                    $wherePhone = "(
                        o.phone LIKE ? OR 
                        REPLACE(REPLACE(REPLACE(o.phone, '-', ''), ' ', ''), '+66', '0') LIKE ? OR
                        REPLACE(REPLACE(REPLACE(o.phone, '-', ''), ' ', ''), '0', '+66') LIKE ?
                    )";

                    [$wherePlat, $paramsPlat] = $this->buildPlatformWhitelist();
                    $where = "$wherePhone AND $wherePlat";

                    $params = [$pattern, $pattern, $pattern];
                    $params = array_merge($params, $paramsPlat);

                    $bwd = $this->queryOrders($where, $params);
                    $legacy = $this->queryLegacyOrdersByPhone($pattern, $_GET['min_delivery'] ?? '2025-08-01');

                    $combined = array_merge($bwd, $legacy);
                    usort($combined, function ($a, $b) {
                        $aKey = $a['delivery_date'] ?: ($a['updated'] ?: $a['created']);
                        $bKey = $b['delivery_date'] ?: ($b['updated'] ?: $b['created']);
                        return strcmp($bKey, $aKey);
                    });
                    $combined = array_slice($combined, 0, 50);

                    $this->success('Orders found (merged sources)', $combined, [
                        'search_type' => 'phone',
                        'search_value' => $value,
                        'platform_filter_bwd' => $this->allowedPlatforms,
                        'min_delivery_legacy' => $_GET['min_delivery'] ?? '2025-08-01',
                        'from_sources' => [
                            'orders_bwd' => count($bwd),
                            'orders_legacy' => count($legacy)
                        ],
                        'total_records' => count($combined),
                        'note' => 'รวมข้อมูลจาก bs_orders_bwd และ bs_orders+bs_customers; มี tracking จาก Thailand Post ถ้ามีเลข'
                    ]);
                } else {
                    $this->error(400, 'Invalid search type');
                }
                break;

            case 'debug_products':
                $debugData = $this->debugProductsIssue();
                $this->success('Products debug information', $debugData);
                break;

            default:
                $this->success('API Information', null, [
                    'version' => '5.0 - Phone Search + Legacy Merge',
                    'fixes' => [
                        'main_product_display' => 'แสดง Product ของ Order หลัก',
                        'complete_products' => 'รวม Main + Sub Products',
                        'primary_product_logic' => 'ใช้ Main Product เป็น Primary ถ้ามี',
                        'amount_calculation' => 'รวม amount จาก Main + Sub Orders',
                        'phone_search_platform_filter' => 'ค้นหาเบอร์กรองเฉพาะ platform: Exhibition, LINE, Facebook, Website, SilverNow, WalkIN (ฝั่ง BWD)',
                        'legacy_merge' => 'ค้นหาจาก bs_orders + bs_customers และรวมผล'
                    ],
                    'endpoints' => [
                        'Legacy shortcut' => 'api.php?phone={phone}',
                        'Search by phone' => 'api.php?action=search&type=phone&value={phone}&min_delivery=2025-08-01',
                        'Track package' => 'api.php?action=track_only&tracking={tracking}',
                        'Debug products' => 'api.php?action=debug_products',
                        'Test authentication' => 'api.php?action=test_auth'
                    ]
                ]);
        }
    }

    public function __destruct()
    {
        $this->db?->close();
    }
}

// Run the API
(new SimpleAPI())->handleRequest();
