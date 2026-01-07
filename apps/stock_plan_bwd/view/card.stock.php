<?php

class GeneralStockReportManager
{
    private $dbc;
    private $reportDisplayStartDate;
    private $reportDisplayEndDate;
    private $productTypeIds;
    private $debug = true;

    const SECONDS_IN_DAY = 86400;
    const DATA_START_DATE = "2025-12-10";

    private $initialTotalInStock = [
        1 => 926,
        4 => 333,
        5 => 4,
        6 => 206,
        7 => 2,
        2 => 380,
        3 => 2,
        9 => 111,
        10 => 173,
        8 => 244
    ];

    private $initialStockBalance = [
        1 => 822,
        4 => 330,
        5 => 2,
        6 => 132,
        7 => 0,
        2 => 369,
        3 => 0,
        9 => 111,
        10 => 166,
        8 => 235
    ];

    private $initialShowcaseBalance = [
        1 => 0,
        4 => 0,
        5 => 0,
        6 => 0,
        7 => 0,
        2 => 0,
        3 => 0,
        9 => 0,
        10 => 0,
        8 => 0
    ];

    private $initialCustomerDepositBalance = [
        1 => 76,
        4 => 3,
        5 => 0,
        6 => 74,
        7 => 0,
        2 => 4,
        3 => 0,
        9 => 0,
        10 => 7,
        8 => 6
    ];

    private $initialDamagedBalance = [
        1 => 28,
        4 => 0,
        5 => 2,
        6 => 0,
        7 => 2,
        2 => 7,
        3 => 2,
        9 => 0,
        10 => 0,
        8 => 3
    ];

    public function __construct($dbc)
    {
        $this->dbc = $dbc;
        $this->productTypeIds = [1, 4, 5, 6, 7, 2, 3, 9, 10, 8];
        $this->initializeDates();

        if (!$this->dbc) {
            $this->debugLog("ERROR: Database connection is null during __construct.");
            throw new Exception("Database connection object is required.");
        }
    }

    private function debugLog($message)
    {
        if ($this->debug) {
            error_log("GeneralStockReport Debug: " . $message);
            echo "<!-- GeneralStockReport Debug: " . htmlspecialchars($message) . " -->\n";
        }
    }

    private function initializeDates()
    {
        $this->reportDisplayStartDate = strtotime("2025-12-10");
        $this->reportDisplayEndDate = strtotime("today");

        if ($this->reportDisplayStartDate > $this->reportDisplayEndDate) {
            $this->reportDisplayStartDate = $this->reportDisplayEndDate;
        }

        $this->debugLog("Report Display Dates initialized - Start: " . date('Y-m-d', $this->reportDisplayStartDate) .
            ", End: " . date('Y-m-d', $this->reportDisplayEndDate));
    }

    private function getProducts()
    {
        $this->debugLog("Fetching products...");
        $products = array();
        if (empty($this->productTypeIds)) {
            return array();
        }
        $productIdsString = implode(',', $this->productTypeIds);

        try {
            $sql = "SELECT id, name FROM bs_products_type WHERE id IN (" . $productIdsString . ") ORDER BY FIELD(id, " . $productIdsString . ")";
            $rst = $this->dbc->Query($sql);
            while ($row = $this->dbc->Fetch($rst)) {
                $products[] = $row;
            }
            $this->debugLog("Total products found: " . count($products));
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching products: " . $e->getMessage());
        }
        return $products;
    }

    private function generateDateRange()
    {
        $dates = array();
        for ($time = $this->reportDisplayStartDate; $time <= $this->reportDisplayEndDate; $time += self::SECONDS_IN_DAY) {
            $dates[] = $time;
        }
        return $dates;
    }

    private function fetchAllData()
    {
        $this->debugLog("Fetching all stock data in bulk...");
        $data = array(
            'showcase' => array(),
            'damaged' => array(),
            'stock_in' => array(),
            'customer_deposit_in' => array(),
            'customer_deposit_out' => array(),
            'customer_deposit_orders' => array(),
            'new_shipment' => array(),
            'luckgems_shipment' => array(),
            'luckgems_retail_shipment' => array(),
            'safe_withdrawal' => array() 
        );

        if (empty($this->productTypeIds)) {
            return $data;
        }
        $productIdsString = implode(',', $this->productTypeIds);

        $fullStartDate = date("Y-m-d", strtotime(self::DATA_START_DATE));
        $fullEndDate = date("Y-m-d", $this->reportDisplayEndDate);

        try {
            $sqlShowcase = "SELECT product_type, date, SUM(amount) AS total_amount
                          FROM bs_stock_adjusted_bwd
                          WHERE product_type IN (" . $productIdsString . ")
                          AND type_id = 3
                          AND date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                          GROUP BY product_type, date";
            $rst = $this->dbc->Query($sqlShowcase);
            while ($row = $this->dbc->Fetch($rst)) {
                $data['showcase'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching showcase data: " . $e->getMessage());
        }

        try {
            $sqlDamaged = "SELECT product_type, date, SUM(amount) AS total_amount
                          FROM bs_stock_adjusted_bwd
                          WHERE product_type IN (" . $productIdsString . ")
                          AND type_id = 2
                          AND date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                          GROUP BY product_type, date";
            $rst = $this->dbc->Query($sqlDamaged);
            while ($row = $this->dbc->Fetch($rst)) {
                $data['damaged'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching damaged data: " . $e->getMessage());
        }

        try {
            $sqlSafeWithdrawal = "SELECT product_type, date, SUM(amount) AS total_amount
                          FROM bs_stock_adjusted_bwd
                          WHERE product_type IN (" . $productIdsString . ")
                          AND type_id = 3
                          AND date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                          GROUP BY product_type, date";
            $rst = $this->dbc->Query($sqlSafeWithdrawal);
            while ($row = $this->dbc->Fetch($rst)) {
                $data['safe_withdrawal'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching safe withdrawal data: " . $e->getMessage());
        }

        try {
            $sqlStockIn = "SELECT product_type, DATE(submited) AS date, SUM(amount) AS total_amount
                          FROM bs_stock_bwd
                          WHERE product_type IN (" . $productIdsString . ")
                          AND DATE(submited) BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                          GROUP BY product_type, DATE(submited)";
            $rst = $this->dbc->Query($sqlStockIn);
            while ($row = $this->dbc->Fetch($rst)) {
                if (!isset($data['stock_in'][$row['date']])) {
                    $data['stock_in'][$row['date']] = array();
                }
                $data['stock_in'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching stock in data: " . $e->getMessage());
        }

        try {
            $sqlCustomerDeposit = "SELECT product_type, created, accept_date, amount
                     FROM bs_orders_bwd
                     WHERE product_type IN (" . $productIdsString . ")
                     AND status > 0
                     AND (accept_date IS NULL OR DATEDIFF(accept_date, created) >= 1)";
            $rst = $this->dbc->Query($sqlCustomerDeposit);
            while ($row = $this->dbc->Fetch($rst)) {
                $data['customer_deposit_orders'][] = array(
                    'product_type' => $row['product_type'],
                    'created' => $row['created'],
                    'accept_date' => $row['accept_date'],
                    'amount' => (float)$row['amount']
                );

                if ($row['created'] && $row['created'] >= $fullStartDate && $row['created'] <= $fullEndDate) {
                    $createdDate = date('Y-m-d', strtotime($row['created'])); 

                    if (!isset($data['customer_deposit_in'][$createdDate])) {
                        $data['customer_deposit_in'][$createdDate] = array();
                    }
                    if (!isset($data['customer_deposit_in'][$createdDate][$row['product_type']])) {
                        $data['customer_deposit_in'][$createdDate][$row['product_type']] = 0;
                    }
                    $data['customer_deposit_in'][$createdDate][$row['product_type']] += (float)$row['amount'];
                }

                if ($row['accept_date'] && $row['accept_date'] >= $fullStartDate && $row['accept_date'] <= $fullEndDate) {
                    if (!isset($data['customer_deposit_out'][$row['accept_date']])) {
                        $data['customer_deposit_out'][$row['accept_date']] = array();
                    }
                    if (!isset($data['customer_deposit_out'][$row['accept_date']][$row['product_type']])) {
                        $data['customer_deposit_out'][$row['accept_date']][$row['product_type']] = 0;
                    }
                    $data['customer_deposit_out'][$row['accept_date']][$row['product_type']] += (float)$row['amount'];
                }
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching customer deposit data: " . $e->getMessage());
        }

        try {
            $sqlNewShipment = "SELECT product_type, delivery_date, SUM(amount) AS total_amount
                 FROM bs_orders_bwd
                 WHERE product_type IN (" . $productIdsString . ")
                 AND status > 0
                 AND delivery_date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                 AND orderable_type != 'receive_at_luckgems'
                 AND DATE(accept_date) = DATE(created)
                 GROUP BY product_type, delivery_date";
            $rst = $this->dbc->Query($sqlNewShipment);
            while ($row = $this->dbc->Fetch($rst)) {
                if (!isset($data['new_shipment'][$row['delivery_date']])) {
                    $data['new_shipment'][$row['delivery_date']] = array();
                }
                $data['new_shipment'][$row['delivery_date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching new shipment data: " . $e->getMessage());
        }

        try {
            $sqlLuckGemsShipment = "SELECT product_type, delivery_date, SUM(amount) AS total_amount
							 FROM bs_orders_bwd
							 WHERE product_type IN (" . $productIdsString . ")
							 AND status > 0
							 AND orderable_type = 'receive_at_luckgems'
							 AND platform != 'LuckGems'
							 AND delivery_date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
							 GROUP BY product_type, delivery_date";
            $rst = $this->dbc->Query($sqlLuckGemsShipment);
            while ($row = $this->dbc->Fetch($rst)) {
                if (!isset($data['luckgems_shipment'][$row['delivery_date']])) {
                    $data['luckgems_shipment'][$row['delivery_date']] = array();
                }
                $data['luckgems_shipment'][$row['delivery_date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching LuckGems shipment data: " . $e->getMessage());
        }

        try {
            $sqlLuckGemsRetail = "SELECT product_type, date, SUM(amount) AS total_amount
							 FROM bs_stock_adjusted_bwd
							 WHERE product_type IN (" . $productIdsString . ")
							 AND type_id = 9
							 AND remark LIKE '%ส่ง%'
							 AND date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
							 GROUP BY product_type, date";
            $rst = $this->dbc->Query($sqlLuckGemsRetail);
            while ($row = $this->dbc->Fetch($rst)) {
                if (!isset($data['luckgems_retail_shipment'][$row['date']])) {
                    $data['luckgems_retail_shipment'][$row['date']] = array();
                }
                $data['luckgems_retail_shipment'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
            }
        } catch (Exception $e) {
            $this->debugLog("ERROR fetching LuckGems retail shipment data: " . $e->getMessage());
        }

        return $data;
    }

    private function calculateInitialBalance($allData)
    {
        $showcaseBalances = array();
        $damagedBalances = array();
        $customerDepositBalances = array();

        $initialBalanceEndDate = date("Y-m-d", $this->reportDisplayStartDate - self::SECONDS_IN_DAY);

        foreach ($this->productTypeIds as $productTypeId) {
            $showcaseBalance = isset($this->initialShowcaseBalance[$productTypeId]) ? $this->initialShowcaseBalance[$productTypeId] : 0;
            $startCalculationDate = strtotime(self::DATA_START_DATE) + self::SECONDS_IN_DAY;
            for ($time = $startCalculationDate; $time <= strtotime($initialBalanceEndDate); $time += self::SECONDS_IN_DAY) {
                $date = date("Y-m-d", $time);
                $dailyShowcase = isset($allData['showcase'][$date][$productTypeId]) ? $allData['showcase'][$date][$productTypeId] : 0;
                $showcaseBalance += $dailyShowcase;
            }
            $showcaseBalances[$productTypeId] = $showcaseBalance;

            $damagedBalance = isset($this->initialDamagedBalance[$productTypeId]) ? $this->initialDamagedBalance[$productTypeId] : 0;
            for ($time = $startCalculationDate; $time <= strtotime($initialBalanceEndDate); $time += self::SECONDS_IN_DAY) {
                $date = date("Y-m-d", $time);
                $dailyDamaged = isset($allData['damaged'][$date][$productTypeId]) ? $allData['damaged'][$date][$productTypeId] : 0;
                $damagedBalance += $dailyDamaged;
            }
            $damagedBalances[$productTypeId] = $damagedBalance;

            $customerDepositAmount = isset($this->initialCustomerDepositBalance[$productTypeId]) ? $this->initialCustomerDepositBalance[$productTypeId] : 0;
            for ($time = $startCalculationDate; $time <= strtotime($initialBalanceEndDate); $time += self::SECONDS_IN_DAY) {
                $date = date("Y-m-d", $time);
                $depositIn = isset($allData['customer_deposit_in'][$date][$productTypeId]) ? $allData['customer_deposit_in'][$date][$productTypeId] : 0;
                $customerDepositAmount += $depositIn;
                $depositOut = isset($allData['customer_deposit_out'][$date][$productTypeId]) ? $allData['customer_deposit_out'][$date][$productTypeId] : 0;
                $customerDepositAmount -= $depositOut;
            }
            $customerDepositBalances[$productTypeId] = $customerDepositAmount;
        }

        return array(
            'showcase' => $showcaseBalances,
            'damaged' => $damagedBalances,
            'customer_deposit' => $customerDepositBalances
        );
    }

    private function getDayColorStyle($date)
    {
        $dayNumber = date("w", $date);
        $dayColors = [
            '0' => '#ffebee',
            '1' => '#fffde7',
            '2' => '#fce4ec',
            '3' => '#e8f5e8',
            '4' => '#fff3e0',
            '5' => '#e1f5fe',
            '6' => '#f3e5f5'
        ];
        return isset($dayColors[$dayNumber]) ? $dayColors[$dayNumber] : '#ffffff';
    }

    public function generateReport()
    {
        try {
            $products = $this->getProducts();
            if (empty($products)) {
                throw new Exception("No products found for the specified IDs.");
            }

            $allData = $this->fetchAllData();
            $initialBalance = $this->calculateInitialBalance($allData);
            $dates = $this->generateDateRange();

            return array(
                'products' => $products,
                'dates' => $dates,
                'initial_balance' => $initialBalance,
                'all_data' => $allData,
                'movements' => array(),
                'product_type_ids' => $this->productTypeIds
            );
        } catch (Exception $e) {
            $this->debugLog("FATAL ERROR in generateReport: " . $e->getMessage());
            throw $e;
        }
    }

    public function renderReport()
    {
        try {
            $reportData = $this->generateReport();
            ob_start();
?>
            <!DOCTYPE html>
            <html lang="th">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>ระบบจัดการสต็อกทั่วไป - Stock Bowins</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
                <style>
                    * {
                        box-sizing: border-box;
                    }

                    body {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        min-height: 100vh;
                        padding: 20px 0;
                    }

                    .main-container {
                        background: white;
                        border-radius: 20px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                        overflow: hidden;
                        margin: 0 auto;
                        max-width: 98%;
                    }

                    .controls-section {
                        padding: 25px 35px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                    }

                    .controls-section h2 {
                        margin: 0;
                        font-weight: 700;
                        font-size: 1.8rem;
                    }

                    .controls-section .date-info {
                        background: rgba(255, 255, 255, 0.2);
                        padding: 8px 15px;
                        border-radius: 20px;
                        display: inline-block;
                    }

                    .table-wrapper {
                        padding: 0;
                    }

                    .card {
                        border: none;
                        margin: 0;
                    }

                    .card-body {
                        padding: 0;
                        overflow-x: auto;
                        overflow-y: visible;
                    }

                    .table {
                        margin-bottom: 0;
                        border-collapse: separate;
                        border-spacing: 0;
                        width: 100%;
                    }

                    .table td,
                    .table th {
                        border: 1px solid #dee2e6;
                        padding: 18px 20px;
                        text-align: center;
                        vertical-align: middle;
                        font-size: 1rem;
                        white-space: nowrap;
                        min-width: 100px;
                    }

                    .table {
                        margin-bottom: 0;
                        border-collapse: separate;
                        border-spacing: 0 8px;
                        width: 100%;
                    }

                    .table thead th {
                        border-bottom: 3px solid #667eea;
                        border-top: 1px solid #dee2e6;
                    }

                    .table tbody tr {
                        border-bottom: 1px solid #dee2e6;
                    }

                    .table tbody tr:hover {
                        background-color: rgba(102, 126, 234, 0.05);
                        transition: background-color 0.3s ease;
                    }

                    .product-name {
                        font-size: 0.75rem;
                        max-width: 150px;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        display: inline-block;
                    }

                    .table th[title] {
                        cursor: help;
                        position: relative;
                    }

                    .table th[title]:hover {
                        background-color: rgba(255, 255, 255, 0.2) !important;
                    }

                    .day-column {
                        border-left: 3px solid #dee2e6;
                        border-right: 3px solid #dee2e6;
                    }

                    .day-column.sunday {
                        border-left-color: #f44336;
                        border-right-color: #f44336;
                    }

                    .day-column.monday {
                        border-left-color: #ffc107;
                        border-right-color: #ffc107;
                    }

                    .day-column.tuesday {
                        border-left-color: #e91e63;
                        border-right-color: #e91e63;
                    }

                    .day-column.wednesday {
                        border-left-color: #4caf50;
                        border-right-color: #4caf50;
                    }

                    .day-column.thursday {
                        border-left-color: #ff9800;
                        border-right-color: #ff9800;
                    }

                    .day-column.friday {
                        border-left-color: #2196f3;
                        border-right-color: #2196f3;
                    }

                    .day-column.saturday {
                        border-left-color: #9c27b0;
                        border-right-color: #9c27b0;
                    }

                    .sticky-col {
                        position: sticky;
                        left: 0;
                        z-index: 10;
                        min-width: 400px;
                        max-width: 400px;
                        box-shadow: 3px 0 8px rgba(0, 0, 0, 0.15);
                        border-right: 3px solid #667eea !important;
                    }

                    .table tbody tr:nth-child(even) {
                        background-color: rgba(248, 249, 250, 0.3);
                    }

                    .row-header {
                        background-color: #343a40;
                        color: white;
                        font-weight: bold;
                    }

                    .row-total {
                        background-color: #667eea;
                        color: white;
                        font-weight: bold;
                    }

                    .row-ready-stock {
                        background-color: #3498db;
                        color: white;
                        font-weight: bold;
                    }

                    .row-customer {
                        background-color: #00b894;
                        color: white;
                        font-weight: bold;
                    }

                    .row-showcase {
                        background-color: #9c27b0;
                        color: white;
                        font-weight: bold;
                    }

                    .row-damaged {
                        background-color: #e74c3c;
                        color: white;
                        font-weight: bold;
                    }

                    .row-shipment {
                        background-color: #2c3e50;
                        color: white;
                        font-weight: bold;
                    }

                    .row-white {
                        background-color: white;
                        color: #333;
                    }

                    .positive-value {
                        color: #27ae60;
                        font-weight: bold;
                    }

                    .negative-value {
                        color: #e74c3c;
                        font-weight: bold;
                    }

                    .zero-value {
                        color: #95a5a6;
                    }

                    .today {
                        border: 3px solid #007bff !important;
                        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
                    }

                    .green-bg {
                        background-color: #d4edda;
                    }

                    .red-bg {
                        background-color: #f8d7da;
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

                    .main-container {
                        animation: fadeIn 0.5s ease-out;
                    }

                    @media (max-width: 768px) {
                        .table {
                            font-size: 0.75rem;
                        }

                        .table td,
                        .table th {
                            padding: 6px 4px;
                        }

                        .sticky-col {
                            min-width: 250px;
                            max-width: 250px;
                        }

                        .controls-section h2 {
                            font-size: 1.3rem;
                        }
                    }
                </style>
            </head>

            <body>
                <div class="container-fluid">
                    <div class="main-container">
                        <div class="controls-section">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h2 class="text-dark">
                                    Stock Bowins
                                </h2>
                                <div class="date-info">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    ช่วงวันที่: <?php echo date("d/m/Y", $reportData['dates'][0]) . " - " . date("d/m/Y", end($reportData['dates'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="table-wrapper">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <th class="sticky-col row-header">
                                                    <i class="fas fa-calendar me-2"></i>วันที่
                                                </th>
                                                <?php
                                                foreach ($reportData['dates'] as $date) {
                                                    $isToday = date('Y-m-d', $date) == date('Y-m-d');
                                                    $bgColor = $this->getDayColorStyle($date);
                                                    $extraClass = $isToday ? ' today' : '';
                                                    $dayNumber = date('w', $date);
                                                    $thaiDayNames = [
                                                        '0' => 'วันอาทิตย์',
                                                        '1' => 'วันจันทร์',
                                                        '2' => 'วันอังคาร',
                                                        '3' => 'วันพุธ',
                                                        '4' => 'วันพฤหัสบดี',
                                                        '5' => 'วันศุกร์',
                                                        '6' => 'วันเสาร์'
                                                    ];
                                                    $dayName = $thaiDayNames[$dayNumber];

                                                    $dayClasses = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                                    $dayClass = $dayClasses[$dayNumber];

                                                    echo '<th colspan="' . count($this->productTypeIds) . '" class="day-column ' . $dayClass . $extraClass . '" style="background-color: ' . $bgColor . '; font-weight: bold; padding: 15px 8px;">';
                                                    echo '<i class="fas fa-calendar-day me-1"></i>';
                                                    echo $dayName . '<br><span style="font-size: 0.9rem;">' . date("d/m/Y", $date) . '</span>';
                                                    echo '</th>';
                                                }
                                                ?>
                                            </tr>

                                            <tr>
                                                <th class="sticky-col row-header">
                                                    <i class="fas fa-tag me-2"></i>สินค้า
                                                </th>
                                                <?php
                                                foreach ($reportData['dates'] as $adate) {
                                                    $bgColor = $this->getDayColorStyle($adate);
                                                    $dayNumber = date('w', $adate);
                                                    $dayClasses = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                                    $dayClass = $dayClasses[$dayNumber];

                                                    foreach ($reportData['products'] as $product) {
                                                        $fullName = $product['name'];
                                                        $nameParts = explode(' - ', $fullName);

                                                        echo '<th class="day-column ' . $dayClass . '" style="background-color: ' . $bgColor . '; color: #2d3436; font-weight: bold; padding: 12px 8px; white-space: normal;">';

                                                        foreach ($nameParts as $index => $part) {
                                                            $marginBottom = ($index < count($nameParts) - 1) ? 'margin-bottom: 6px;' : '';
                                                            echo '<div style="font-size: 0.85rem; line-height: 1.5; ' . $marginBottom . '">' . htmlspecialchars(trim($part)) . '</div>';
                                                        }

                                                        echo '</th>';
                                                    }
                                                }
                                                ?>
                                            </tr>

                                            <?php
                                            $allDates = array();
                                            foreach ($reportData['dates'] as $date) {
                                                $allDates[] = date("Y-m-d", $date);
                                            }

                                            $dailyReadyStock = array();
                                            $dailyCustomerDeposit = array();
                                            $dailyShowcase = array();
                                            $dailyDamaged = array();
                                            $dailyTotalStock = array();

                                            $readyStock = array();
                                            $customerDeposit = array();
                                            $showcase = array();
                                            $damaged = array();

                                            foreach ($this->productTypeIds as $productTypeId) {
                                                $readyStock[$productTypeId] = isset($this->initialStockBalance[$productTypeId]) ? $this->initialStockBalance[$productTypeId] : 0;
                                                $customerDeposit[$productTypeId] = isset($reportData['initial_balance']['customer_deposit'][$productTypeId]) ? $reportData['initial_balance']['customer_deposit'][$productTypeId] : 0;
                                                $showcase[$productTypeId] = isset($reportData['initial_balance']['showcase'][$productTypeId]) ? $reportData['initial_balance']['showcase'][$productTypeId] : 0;
                                                $damaged[$productTypeId] = isset($reportData['initial_balance']['damaged'][$productTypeId]) ? $reportData['initial_balance']['damaged'][$productTypeId] : 0;
                                            }

                                            foreach ($allDates as $currentDate) {
                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $dailyReadyStock[$currentDate][$productTypeId] = $readyStock[$productTypeId];
                                                    $dailyCustomerDeposit[$currentDate][$productTypeId] = $customerDeposit[$productTypeId];
                                                    $dailyShowcase[$currentDate][$productTypeId] = $showcase[$productTypeId];
                                                    $dailyDamaged[$currentDate][$productTypeId] = $damaged[$productTypeId];
                                                    $dailyTotalStock[$currentDate][$productTypeId] =
                                                        $readyStock[$productTypeId] +
                                                        $customerDeposit[$productTypeId] +
                                                        $showcase[$productTypeId] +
                                                        $damaged[$productTypeId];

                                                    $stockIn = isset($reportData['all_data']['stock_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['stock_in'][$currentDate][$productTypeId] : 0;
                                                    $newShipment = isset($reportData['all_data']['new_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['new_shipment'][$currentDate][$productTypeId] : 0;
                                                    $luckgemsShipment = isset($reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId] : 0;
                                                    $safeWithdrawal = isset($reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId]) ? $reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId] : 0;
                                                    $depositIn = isset($reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId] : 0;
                                                    // หักเบิกของออกจากเซฟออกจาก Ready Stock ด้วย
                                                    $readyStock[$productTypeId] += $stockIn - $newShipment - $luckgemsShipment - $safeWithdrawal - $depositIn;

                                                    $depositOut = isset($reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId] : 0;
                                                    $customerDeposit[$productTypeId] += $depositIn - $depositOut;
                                                    $dailyShowcaseMovement = isset($reportData['all_data']['showcase'][$currentDate][$productTypeId]) ? $reportData['all_data']['showcase'][$currentDate][$productTypeId] : 0;
                                                    $showcase[$productTypeId] += $dailyShowcaseMovement;

                                                    $dailyDamagedMovement = isset($reportData['all_data']['damaged'][$currentDate][$productTypeId]) ? $reportData['all_data']['damaged'][$currentDate][$productTypeId] : 0;
                                                    $damaged[$productTypeId] += $dailyDamagedMovement;
                                                }
                                            }
                                            ?>

                                            <tr class="major-section">
                                                <td class="sticky-col row-total text-left">
                                                    รวมของในคลังยกมา
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = $dailyTotalStock[$currentDate][$productTypeId];
                                                        $colorClass = $value > 0 ? 'positive-value' : ($value < 0 ? 'negative-value' : 'zero-value');
                                                        echo '<td class="' . $colorClass . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>

                                            <tr>
                                                <td class="sticky-col row-white text-left">แท่งพร้อมส่ง ยกมา</td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = $dailyReadyStock[$currentDate][$productTypeId];
                                                        $class = $value > 0 ? 'positive-value' : ($value < 0 ? 'negative-value' : 'zero-value');
                                                        echo '<td class="' . $class . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #9c27b0; font-weight: bold;">
                                                    แท่งในตู้โชว์ ยกมา
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = $dailyShowcase[$currentDate][$productTypeId];
                                                        $color = $value > 0 ? 'color: #9c27b0; font-weight: bold;' : 'color: #95a5a6;';
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left">ลูกค้าฝากแท่ง ยกมา</td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = $dailyCustomerDeposit[$currentDate][$productTypeId];
                                                        $class = $value > 0 ? 'positive-value' : ($value < 0 ? 'negative-value' : 'zero-value');
                                                        echo '<td class="' . $class . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #e74c3c; font-weight: bold;">
                                                    แท่งชำรุด ยกมา
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = $dailyDamaged[$currentDate][$productTypeId];
                                                        $color = $value > 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;';
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #28a745; font-weight: bold;">
                                                    รับเข้าแท่งเงิน
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['stock_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['stock_in'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #28a745; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' green-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>

                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #17a2b8; font-weight: bold;">
                                                    ลูกค้าฝากแท่งเพิ่ม
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #17a2b8; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' green-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #e74c3c; font-weight: bold;">
                                                    ส่งแท่งลูกค้าฝาก
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' red-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr class="major-section">
                                                <td class="sticky-col row-customer text-left">
                                                    รวมฝากแท่ง
                                                </td>
                                                <?php
                                                $runningCustomerDeposit = array();
                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $runningCustomerDeposit[$productTypeId] = isset($reportData['initial_balance']['customer_deposit'][$productTypeId]) ? $reportData['initial_balance']['customer_deposit'][$productTypeId] : 0;
                                                }

                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $depositIn = isset($reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId] : 0;
                                                        $depositOut = isset($reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId] : 0;
                                                        $runningCustomerDeposit[$productTypeId] += $depositIn - $depositOut;
                                                        $value = $runningCustomerDeposit[$productTypeId];

                                                        $color = $value > 0 ? 'color: #00b894; font-weight: bold;' : ($value < 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;');
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>

                                            <!-- เพิ่มบรรทัดนี้: เบิกของออกจากเซฟ -->
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #6c757d; font-weight: bold;">
                                                    เบิกของออกจากเซฟ
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId]) ? $reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #6c757d; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' red-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>

                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #2c3e50; font-weight: bold;">
                                                    ส่งแท่ง: เบิกใหม่
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['new_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['new_shipment'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #2c3e50; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' red-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #e74c3c; font-weight: bold;">
                                                    ส่งแท่งฝาก ไป Luck Gems
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' red-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-white text-left" style="color: #fd7e14; font-weight: bold;">
                                                    ส่งแท่งขายหน้าร้าน ไป Luck Gems
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $value = isset($reportData['all_data']['luckgems_retail_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_retail_shipment'][$currentDate][$productTypeId] : 0;
                                                        $color = $value > 0 ? 'color: #fd7e14; font-weight: bold;' : 'color: #95a5a6;';
                                                        $bgClass = $value > 0 ? ' red-bg' : '';
                                                        echo '<td class="' . $bgClass . '" style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr class="major-section">
                                                <td class="sticky-col row-shipment text-left">
                                                    ส่งแท่งทั้งหมด
                                                </td>
                                                <?php
                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $customerDepositOut = isset($reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId] : 0;
                                                        $newShipment = isset($reportData['all_data']['new_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['new_shipment'][$currentDate][$productTypeId] : 0;
                                                        $luckgemsShipment = isset($reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId] : 0;
                                                        $luckgemsRetail = isset($reportData['all_data']['luckgems_retail_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_retail_shipment'][$currentDate][$productTypeId] : 0;
                                                        $safeWithdrawal = isset($reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId]) ? $reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId] : 0;

                                                        $totalShipment = $customerDepositOut + $newShipment + $luckgemsShipment + $luckgemsRetail + $safeWithdrawal;

                                                        $color = $totalShipment > 0 ? 'color: #2c3e50; font-weight: bold;' : 'color: #95a5a6;';
                                                        echo '<td style="' . $color . '">' . number_format($totalShipment, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr class="major-section">
                                                <td class="sticky-col row-ready-stock text-left">
                                                    แท่งพร้อมส่ง
                                                </td>
                                                <?php
                                                $runningReadyStock = array();
                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $runningReadyStock[$productTypeId] = isset($this->initialStockBalance[$productTypeId]) ? $this->initialStockBalance[$productTypeId] : 0;
                                                }

                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $stockIn = isset($reportData['all_data']['stock_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['stock_in'][$currentDate][$productTypeId] : 0;
                                                        $newShipment = isset($reportData['all_data']['new_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['new_shipment'][$currentDate][$productTypeId] : 0;
                                                        $luckgemsShipment = isset($reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId] : 0;
                                                        $safeWithdrawal = isset($reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId]) ? $reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId] : 0;
                                                        $depositIn = isset($reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId] : 0;
                                                        $runningReadyStock[$productTypeId] += $stockIn - $newShipment - $luckgemsShipment - $safeWithdrawal - $depositIn;
                                                        $value = $runningReadyStock[$productTypeId];

                                                        $color = $value > 0 ? 'color: #3498db; font-weight: bold;' : ($value < 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;');
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-customer text-left">
                                                    ลูกค้าฝากแท่ง
                                                </td>
                                                <?php
                                                $runningCustomerDeposit2 = array();
                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $runningCustomerDeposit2[$productTypeId] = isset($reportData['initial_balance']['customer_deposit'][$productTypeId]) ? $reportData['initial_balance']['customer_deposit'][$productTypeId] : 0;
                                                }

                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $depositIn = isset($reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId] : 0;
                                                        $depositOut = isset($reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId] : 0;
                                                        $runningCustomerDeposit2[$productTypeId] += $depositIn - $depositOut;
                                                        $value = $runningCustomerDeposit2[$productTypeId];

                                                        $color = $value > 0 ? 'color: #00b894; font-weight: bold;' : ($value < 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;');
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-showcase text-left">
                                                    แท่งในตู้โชว์
                                                </td>
                                                <?php
                                                $runningShowcase = array();
                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $runningShowcase[$productTypeId] = isset($reportData['initial_balance']['showcase'][$productTypeId]) ? $reportData['initial_balance']['showcase'][$productTypeId] : 0;
                                                }

                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $dailyShowcaseMovement = isset($reportData['all_data']['showcase'][$currentDate][$productTypeId]) ? $reportData['all_data']['showcase'][$currentDate][$productTypeId] : 0;
                                                        $runningShowcase[$productTypeId] += $dailyShowcaseMovement;
                                                        $value = $runningShowcase[$productTypeId];

                                                        $color = $value > 0 ? 'color: #9c27b0; font-weight: bold;' : ($value < 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;');
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <td class="sticky-col row-damaged text-left">
                                                    แท่งชำรุด
                                                </td>
                                                <?php
                                                $runningDamaged = array();
                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $runningDamaged[$productTypeId] = isset($reportData['initial_balance']['damaged'][$productTypeId]) ? $reportData['initial_balance']['damaged'][$productTypeId] : 0;
                                                }

                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $dailyDamagedMovement = isset($reportData['all_data']['damaged'][$currentDate][$productTypeId]) ? $reportData['all_data']['damaged'][$currentDate][$productTypeId] : 0;
                                                        $runningDamaged[$productTypeId] += $dailyDamagedMovement;
                                                        $value = $runningDamaged[$productTypeId];

                                                        $color = $value > 0 ? 'color: #e74c3c; font-weight: bold;' : ($value < 0 ? 'color: #c0392b; font-weight: bold;' : 'color: #95a5a6;');
                                                        echo '<td style="' . $color . '">' . number_format($value, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <tr class="major-section">
                                                <td class="sticky-col row-total text-left">
                                                    <i class="fas fa-warehouse me-2"></i>รวมของในคลังทั้งหมด
                                                </td>
                                                <?php
                                                $runningReadyStockCalc = array();
                                                $runningCustomerDepositCalc = array();
                                                $runningShowcaseCalc = array();
                                                $runningDamagedCalc = array();

                                                foreach ($this->productTypeIds as $productTypeId) {
                                                    $runningReadyStockCalc[$productTypeId] = isset($this->initialStockBalance[$productTypeId]) ? $this->initialStockBalance[$productTypeId] : 0;
                                                    $runningCustomerDepositCalc[$productTypeId] = isset($reportData['initial_balance']['customer_deposit'][$productTypeId]) ? $reportData['initial_balance']['customer_deposit'][$productTypeId] : 0;
                                                    $runningShowcaseCalc[$productTypeId] = isset($reportData['initial_balance']['showcase'][$productTypeId]) ? $reportData['initial_balance']['showcase'][$productTypeId] : 0;
                                                    $runningDamagedCalc[$productTypeId] = isset($reportData['initial_balance']['damaged'][$productTypeId]) ? $reportData['initial_balance']['damaged'][$productTypeId] : 0;
                                                }

                                                foreach ($allDates as $currentDate) {
                                                    foreach ($this->productTypeIds as $productTypeId) {
                                                        $stockIn = isset($reportData['all_data']['stock_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['stock_in'][$currentDate][$productTypeId] : 0;
                                                        $newShipment = isset($reportData['all_data']['new_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['new_shipment'][$currentDate][$productTypeId] : 0;
                                                        $luckgemsShipment = isset($reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId]) ? $reportData['all_data']['luckgems_shipment'][$currentDate][$productTypeId] : 0;
                                                        $safeWithdrawal = isset($reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId]) ? $reportData['all_data']['safe_withdrawal'][$currentDate][$productTypeId] : 0;
                                                        $depositIn = isset($reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_in'][$currentDate][$productTypeId] : 0;

                                                        // หัก depositIn ออกจาก Ready Stock ด้วย
                                                        $runningReadyStockCalc[$productTypeId] += $stockIn - $newShipment - $luckgemsShipment - $safeWithdrawal - $depositIn;

                                                        $depositOut = isset($reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId]) ? $reportData['all_data']['customer_deposit_out'][$currentDate][$productTypeId] : 0;
                                                        $runningCustomerDepositCalc[$productTypeId] += $depositIn - $depositOut;

                                                        $dailyShowcaseMovement = isset($reportData['all_data']['showcase'][$currentDate][$productTypeId]) ? $reportData['all_data']['showcase'][$currentDate][$productTypeId] : 0;
                                                        $runningShowcaseCalc[$productTypeId] += $dailyShowcaseMovement;

                                                        $dailyDamagedMovement = isset($reportData['all_data']['damaged'][$currentDate][$productTypeId]) ? $reportData['all_data']['damaged'][$currentDate][$productTypeId] : 0;
                                                        $runningDamagedCalc[$productTypeId] += $dailyDamagedMovement;

                                                        $totalStockValue = $runningReadyStockCalc[$productTypeId] + $runningCustomerDepositCalc[$productTypeId] + $runningShowcaseCalc[$productTypeId] + $runningDamagedCalc[$productTypeId];

                                                        $color = $totalStockValue > 0 ? 'color: #667eea; font-weight: bold;' : ($totalStockValue < 0 ? 'color: #e74c3c; font-weight: bold;' : 'color: #95a5a6;');
                                                        echo '<td style="' . $color . '">' . number_format($totalStockValue, 2) . '</td>';
                                                    }
                                                }
                                                ?>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                <script>
                    (function() {
                        let scrolled = false;
                        let attempts = 0;

                        function findScrollableParent(element) {
                            let parent = element.parentElement;
                            while (parent) {
                                const style = window.getComputedStyle(parent);
                                const overflowX = style.overflowX;

                                if (overflowX === 'auto' || overflowX === 'scroll') {
                                    console.log('Found scrollable parent:', parent.className);
                                    return parent;
                                }
                                parent = parent.parentElement;
                            }
                            return null;
                        }

                        const interval = setInterval(() => {
                            attempts++;

                            if (scrolled || attempts > 30) {
                                clearInterval(interval);
                                if (!scrolled) console.log('❌ Timeout');
                                return;
                            }

                            const todayColumn = document.querySelector('.today');
                            const stickyCol = document.querySelector('.sticky-col');

                            if (todayColumn && stickyCol && todayColumn.offsetWidth > 0) {

                                const scrollContainer = findScrollableParent(todayColumn);

                                if (scrollContainer) {
                                    const stickyColWidth = stickyCol.offsetWidth;
                                    const todayOffset = todayColumn.offsetLeft;
                                    const scrollPosition = todayOffset - stickyColWidth - 100;


                                    if (typeof jQuery !== 'undefined') {
                                        $(scrollContainer).animate({
                                            scrollLeft: Math.max(0, scrollPosition)
                                        }, 800, function() {
                                            console.log('✓ Scroll complete! scrollLeft =', scrollContainer.scrollLeft);
                                        });
                                    } else {
                                        scrollContainer.scrollLeft = Math.max(0, scrollPosition);
                                        console.log('✓ Scrolled! scrollLeft =', scrollContainer.scrollLeft);
                                    }

                                    setTimeout(() => {
                                        todayColumn.style.transition = 'all 0.5s ease';
                                        todayColumn.style.transform = 'scale(1.05)';
                                        todayColumn.style.boxShadow = '0 0 25px rgba(255,193,7,0.8)';

                                        setTimeout(() => {
                                            todayColumn.style.transform = 'scale(1)';
                                            todayColumn.style.boxShadow = '';
                                        }, 800);
                                    }, 500);

                                    scrolled = true;
                                    clearInterval(interval);
                                } else {
                                    console.log('No scrollable parent found');
                                }
                            }
                        }, 1000);
                    })();
                </script>

            </body>

            </html>
<?php
            return ob_get_clean();
        } catch (Exception $e) {
            return "<div class='alert alert-danger'>Error rendering General Stock report: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function testConnection()
    {
        try {
            $result = $this->dbc->Query("SELECT COUNT(*) as count FROM bs_products_type");
            if ($result === false) {
                throw new Exception("Test query failed. Check SQL syntax or table existence.");
            }
            $data = $this->dbc->Fetch($result);
            $this->debugLog("Database connection OK. Found " . ($data['count'] ? $data['count'] : 'N/A') . " product types during test.");
            return true;
        } catch (Exception $e) {
            $this->debugLog("Database connection ERROR during test: " . $e->getMessage());
            return false;
        }
    }
}

try {
    if (!isset($dbc) || !is_object($dbc) || !method_exists($dbc, 'Query') || !method_exists($dbc, 'Fetch')) {
        throw new Exception("Database connection variable \$dbc not properly initialized.");
    }

    $generalStockReport = new GeneralStockReportManager($dbc);

    if ($generalStockReport->testConnection()) {
        echo $generalStockReport->renderReport();
    } else {
        echo "<div class='alert alert-danger'>Database connection failed. General Stock report cannot be generated.</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>FATAL ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

?>