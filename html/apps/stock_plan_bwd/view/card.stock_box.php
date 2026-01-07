<?php

global $dbc;

$today = strtotime("now");
$currentDateStr = date("Y-m-d", $today);

// Product ID สำหรับกล่อง 13-21
$PID = array(13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,25);
$aProduct = array();
$boxStockData = array();

for ($i = 0; $i < count($PID); $i++) {
    $product = $dbc->GetRecord("bs_products_type", "*", "id=" . intval($PID[$i]));
    if ($product) {
        array_push($aProduct, $product);
    } else {
        // กรณีไม่พบข้อมูลสินค้า ให้ใช้ชื่อและ ID ชั่วคราว
        array_push($aProduct, ['id' => $PID[$i], 'name' => 'Box Type ' . $PID[$i]]);
    }
}

for ($i = 0; !($i >= count($PID)); $i++) {
    $productId = intval($PID[$i]);

    $yesterdayStr = date("Y-m-d", strtotime("-1 day"));
    $line_balance = $dbc->GetRecord(
        "bs_stock_bwd",
        "SUM(amount)",
        "submited <= '{$yesterdayStr}' AND product_type = {$productId}"
    );
    $balance_forward = isset($line_balance[0]) ? floatval($line_balance[0]) : 0;

    $line_orders_yesterday = $dbc->GetRecord(
        "bs_orders_bwd",
        "SUM(amount)",
        "delivery_date <= '{$yesterdayStr}' AND status > 0 AND product_type = {$productId} AND platform != 'LuckGems'"
    );
    $orders_yesterday = isset($line_orders_yesterday[0]) ? floatval($line_orders_yesterday[0]) : 0;
    $balance_forward -= $orders_yesterday; // ลบยอดออกจากการยกมา

    $line_in_today = $dbc->GetRecord(
        "bs_stock_bwd",
        "SUM(amount)",
        "submited = '{$currentDateStr}' AND product_type = {$productId}"
    );
    $stock_in_today = isset($line_in_today[0]) ? floatval($line_in_today[0]) : 0;

    $line_out_today = $dbc->GetRecord(
        "bs_orders_bwd",
        "SUM(amount)",
        "delivery_date = '{$currentDateStr}' AND status > 0 AND product_type = {$productId} AND platform != 'LuckGems'"
    );
    $stock_out_today = isset($line_out_today[0]) ? floatval($line_out_today[0]) : 0;

    $line_adjust_today = $dbc->GetRecord(
        "bs_stock_adjusted_bwd",
        "SUM(amount)",
        "date = '{$currentDateStr}' AND product_type = {$productId}"
    );
    $adjust_today = isset($line_adjust_today[0]) ? floatval($line_adjust_today[0]) : 0;

    $current_stock = $balance_forward + $stock_in_today - $stock_out_today + $adjust_today;

    $boxStockData[$i] = array(
        'product' => $aProduct[$i],
        'balance_forward' => $balance_forward,
        'stock_in' => $stock_in_today,
        'stock_out' => $stock_out_today,
        'adjust' => $adjust_today,
        'current_stock' => $current_stock
    );
}

$total_balance_forward = array_sum(array_column($boxStockData, 'balance_forward'));
$total_stock_in = array_sum(array_column($boxStockData, 'stock_in'));
$total_stock_out = array_sum(array_column($boxStockData, 'stock_out'));
$total_adjust = array_sum(array_column($boxStockData, 'adjust'));
$total_current_stock = array_sum(array_column($boxStockData, 'current_stock'));

?>

<div class="card shadow-sm border-0 custom-stock-card">
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <span class="stock-header-text"><i class="fas fa-boxes me-2"></i>Box</span>
                <small class="ms-2 text-muted-light"><?php echo date("d/m/Y"); ?> </small>
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-light-custom ms-3" onclick="exportBoxStockCSV()">
                    <i class="fas fa-download me-1"></i> ส่งออก CSV
                </button>
                <button class="btn btn-sm btn-outline-light-custom ms-2" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i> รีเฟรช
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="row g-0 border-bottom">
            <div class="col-md-4 p-3 border-end">
                <div class="text-center">
                    <div class="h4 mb-1 text-success-soft">
                        <?php echo number_format($total_current_stock, 0); ?>
                    </div>
                    <small class="text-muted">สต็อกปัจจุบัน</small>
                </div>
            </div>
            <div class="col-md-4 p-3 border-end">
                <div class="text-center">
                    <div class="h4 mb-1 text-info-soft">
                        <?php echo number_format($total_stock_in, 0); ?>
                    </div>
                    <small class="text-muted">รับเข้าวันนี้</small>
                </div>
            </div>
            <div class="col-md-4 p-3">
                <div class="text-center">
                    <div class="h4 mb-1 text-danger-soft">
                        <?php echo number_format($total_stock_out, 0); ?>
                    </div>
                    <small class="text-muted">ส่งออกวันนี้</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 box-stock-table" id="boxStockTable">
                <thead class="bg-light">
                    <tr>
                        <th>
                            ชื่อสินค้า
                        </th>
                        <th class="text-center">
                            คงเหลือ
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($boxStockData as $data): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($data['product']['name']); ?></strong>
                                <small class="text-muted d-block mt-1 box-id-text">ID: <?php echo $data['product']['id']; ?></small>
                            </td>
                            <td class="text-center">
                                <?php
                                $currentStockTextColorClass = '';
                                if ($data['current_stock'] < 20) {
                                    $currentStockTextColorClass = 'text-danger fw-bold';
                                } else {
                                    $currentStockTextColorClass = 'text-dark';
                                }
                                ?>
                                <span class="<?php echo $currentStockTextColorClass; ?> fs-6 fw-bold">
                                    <?php echo number_format($data['current_stock'], 0); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td class="text-end py-3">
                            <i class="fas fa-calculator me-2"></i>รวมทั้งหมด:
                        </td>
                        <td class="text-center py-3">
                            <span class="badge bg-primary-soft fs-6">
                                <?php echo number_format($total_current_stock, 0); ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

<style>
    .custom-stock-card {
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        overflow: hidden;
    }

    .custom-stock-card .card-header {
        background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%) !important;
        color: #333 !important;
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .custom-stock-card .card-title {
        font-weight: 600;
        color: #333;
    }

    .custom-stock-card .stock-header-text {
        font-weight: 700;
        font-size: 1.15rem;
        color: #2c3e50;
        margin-right: 0.5rem;
        vertical-align: middle;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .custom-stock-card .text-muted-light {
        color: rgba(0, 0, 0, 0.65) !important;
        font-size: 0.85rem;
    }

    .custom-stock-card .btn-outline-light-custom {
        border-color: rgba(0, 0, 0, 0.15);
        color: #555;
        background-color: rgba(255, 255, 255, 0.8);
        transition: all 0.2s ease-in-out;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.85rem;
    }

    .custom-stock-card .btn-outline-light-custom:hover {
        background-color: rgba(0, 0, 0, 0.1);
        color: #333;
        border-color: rgba(0, 0, 0, 0.25);
    }

    .custom-stock-card .row.g-0>div {
        padding-top: 1.2rem !important;
        padding-bottom: 1.2rem !important;
        background-color: #f8fbfd;
        border-color: #e0e6ed !important;
    }

    .custom-stock-card .text-primary-soft {
        color: #5b84b1 !important;
    }

    .custom-stock-card .text-success-soft {
        color: #28a745 !important;
    }

    .custom-stock-card .text-info-soft {
        color: #17a2b8 !important;
    }

    .custom-stock-card .text-danger-soft {
        color: #dc3545 !important;
    }


    .custom-stock-card .box-stock-table {
        border-collapse: separate;
        border-spacing: 0 5px;
    }

    .custom-stock-card .box-stock-table thead th {
        background-color: #eef4f9;
        border-bottom: 2px solid #dae3ec;
        font-weight: 600;
        font-size: 0.9rem;
        color: #5d6d7e;
        padding: 0.8rem 1.2rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .custom-stock-card .box-stock-table tbody tr {
        transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
    }

    .custom-stock-card .box-stock-table tbody tr:nth-child(even) {
        background-color: #fcfdfe;
    }

    .custom-stock-card .box-stock-table tbody tr:hover {
        background-color: #e8f0f7;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .custom-stock-card .box-stock-table td {
        vertical-align: middle;
        font-size: 0.95rem;
        padding: 0.9rem 1.2rem;
        color: #333;
        border-top: 1px solid #f0f4f8;
    }

    .custom-stock-card .box-stock-table tbody tr:first-child td {
        border-top: none;
    }

    .custom-stock-card .box-stock-table td strong {
        font-size: 1rem;
    }

    .custom-stock-card .box-id-text {
        font-size: 0.75rem;
        color: #888;
    }

    /* Badge Styling (for the total summary at the bottom) */
    .custom-stock-card .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.9em;
        font-weight: 700;
        border-radius: 0.4rem;
        min-width: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .custom-stock-card .badge.bg-primary-soft {
        background-color: #6c757d !important;
        color: #fff !important;
    }

    .custom-stock-card .table-light {
        background-color: #eef2f7 !important;
        border-top: 2px solid #d9e2ec;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .custom-stock-card .table-light td {
        padding: 1rem 1.2rem;
    }


    .custom-stock-card .bg-light-subtle {
        background-color: #f9fbfd !important;
        border-top: 1px solid #e0e6ed;
        padding: 0.75rem 1.5rem;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .custom-stock-card .text-danger {
        color: #dc3545 !important;
    }

    .custom-stock-card .text-dark {
        color: #212529 !important;
    }

    @media (max-width: 768px) {
        .custom-stock-card .table-responsive {
            font-size: 0.8rem;
        }

        .custom-stock-card .badge {
            font-size: 0.7rem;
            padding: 0.3em 0.6em;
        }

        .custom-stock-card .h4 {
            font-size: 1.1rem;
        }

        .custom-stock-card .card-header h5 {
            font-size: 1.2rem;
        }

        .custom-stock-card .box-stock-table thead th,
        .custom-stock-card .box-stock-table td {
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
        }

        .custom-stock-card .box-id-text {
            display: inline-block;
            margin-top: 0 !important;
            margin-left: 0.5rem;
        }
    }
</style>

<script>
    const exportBoxStockCSV = () => {
        const table = document.getElementById('boxStockTable');
        const rows = table.querySelectorAll('tbody tr');
        let csv = 'รหัสกล่อง,ชื่อสินค้า,คงเหลือ\n';

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                const productNameAndId = cells[0].textContent.trim();
                const productNameMatch = productNameAndId.match(/(.*?)\s*ID:\s*(\d+)/);
                let productName = productNameAndId;
                let productId = '';
                if (productNameMatch) {
                    productName = productNameMatch[1].trim();
                    productId = productNameMatch[2];
                }

                const currentStockElement = cells[1].querySelector('span.fs-6');
                const currentStock = currentStockElement ? currentStockElement.textContent.trim() : '';

                csv += `"${productId}","${productName}","${currentStock}"\n`;
            }
        });

        const blob = new Blob(['\ufeff' + csv], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'box_stock_today_' + new Date().toISOString().slice(0, 10) + '.csv';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('#boxStockTable tbody tr');
        rows.forEach(row => {
            const productNameCell = row.cells[0].textContent.trim();
            const currentStockElement = row.cells[1].querySelector('span.fs-6');
            const currentStock = currentStockElement ? currentStockElement.textContent.trim() : '';

            const productNameMatch = productNameCell.match(/(.*?)\s*ID:\s*(\d+)/);
            let productNameForTooltip = productNameCell;
            if (productNameMatch) {
                productNameForTooltip = productNameMatch[1].trim();
            }

            row.setAttribute('title', `${productNameForTooltip}: สต็อกคงเหลือ ${currentStock} ใบ`);
        });
    });
</script>