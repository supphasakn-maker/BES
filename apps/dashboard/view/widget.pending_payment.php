<?php
$today_date = date('Y-m-d', $today);

// นับจำนวนรวมและยอดเงิน
$sql = "SELECT COUNT(*) as total_count, SUM(amount) as total_amount FROM bs_payments WHERE date_active > DATE(datetime) AND date_active > '$today_date'";
$result = $dbc->Query($sql);
$row = $dbc->Fetch($result);
$total_count = $row['total_count'];
$total_amount = $row['total_amount'] ? $row['total_amount'] : 0;

// นับสร้างวันนี้และยอดเงิน
$created_today_sql = "SELECT COUNT(*) as count, SUM(amount) as amount FROM bs_payments WHERE date_active > DATE(datetime) AND DATE(datetime) = '$today_date' AND date_active > '$today_date'";
$created_today_result = $dbc->Query($created_today_sql);
$created_today_row = $dbc->Fetch($created_today_result);
$created_today_count = $created_today_row['count'];
$created_today_amount = $created_today_row['amount'] ? $created_today_row['amount'] : 0;

// นับรายการที่ครบกำหนดพรุ่งนี้
$tomorrow_date = date('Y-m-d', strtotime('+1 day', $today));
$tomorrow_sql = "SELECT COUNT(*) as count, SUM(amount) as amount FROM bs_payments WHERE date_active > DATE(datetime) AND date_active = '$tomorrow_date'";
$tomorrow_result = $dbc->Query($tomorrow_sql);
$tomorrow_row = $dbc->Fetch($tomorrow_result);
$tomorrow_count = $tomorrow_row['count'];
$tomorrow_amount = $tomorrow_row['amount'] ? $tomorrow_row['amount'] : 0;

?>
<div class="card h-100" style="padding: 1rem;">
    <div class="card-body" style="padding: 2rem;">
        <div class="flex-center justify-content-start mb-3">
            <i data-feather="calendar" class="mr-3" style="font-size: 3rem;"></i>
            <h1 class="card-title mb-0 mr-auto" style="font-size: 4rem; font-weight: bold;"><?php echo number_format($total_count); ?></h1>
        </div>
        <h4 class="text-info" style="font-size: 1.5rem;">รายการที่ยังไม่ครบกำหนดชำระ</h4>
        <p class="text-success mb-0" style="font-size: 1.3rem; font-weight: bold;">
            ยอดรวม: ฿<?php echo number_format($total_amount, 2); ?>
        </p>
        <p class="text-secondary mb-0" style="font-size: 1.1rem;">รายการที่ยังไม่ครบกำหนดชำระ | วันที่ <?php echo date('d/m/Y', $today); ?></p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card h-100" style="padding: 1rem;">
            <div class="card-body" style="padding: 2rem;">
                <div class="flex-center justify-content-start mb-3">
                    <i data-feather="plus-circle" class="mr-3 text-success" style="font-size: 2.5rem;"></i>
                    <h1 class="card-title mb-0 mr-auto" style="font-size: 3rem; font-weight: bold;"><?php echo number_format($created_today_count); ?></h1>
                </div>
                <h5 class="text-success" style="font-size: 1.3rem;">รายการที่สร้างวันนี้</h5>
                <p class="text-success mb-1" style="font-size: 1.1rem; font-weight: bold;">
                    ฿<?php echo number_format($created_today_amount, 2); ?>
                </p>
                <p class="text-secondary mb-0" style="font-size: 1rem;">ยังไม่ครบกำหนดชำระ</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" style="padding: 1rem;">
            <div class="card-body" style="padding: 2rem;">
                <div class="flex-center justify-content-start mb-3">
                    <i data-feather="clock" class="mr-3 text-warning" style="font-size: 2.5rem;"></i>
                    <h1 class="card-title mb-0 mr-auto" style="font-size: 3rem; font-weight: bold;"><?php echo number_format($tomorrow_count); ?></h1>
                </div>
                <h5 class="text-warning" style="font-size: 1.3rem;">รายการที่ครบกำหนดพรุ่งนี้</h5>
                <p class="text-warning mb-1" style="font-size: 1.1rem; font-weight: bold;">
                    ฿<?php echo number_format($tomorrow_amount, 2); ?>
                </p>
                <p class="text-secondary mb-0" style="font-size: 1rem;">ต้องเตรียมชำระ</p>
            </div>
        </div>
    </div>
</div>

<?php if ($total_count > 0): ?>
    <div class="card mt-4" style="padding: 1rem;">
        <div class="card-header" style="padding: 1.5rem 2rem;">
            <h5 class="card-title mb-0" style="font-size: 1.4rem; font-weight: bold;">
                <i data-feather="list" class="mr-2"></i>
                รายละเอียดข้อมูล
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 1.1rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">รหัส</th>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">ชื่อลูกค้า</th>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">ประเภทการชำระ</th>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">จำนวนเงิน</th>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">วันที่สร้าง</th>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">วันที่ชำระ</th>
                            <th style="padding: 1.2rem; font-size: 1.1rem; font-weight: bold;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                        p.code,
                        p.datetime,
                        p.date_active,
                        p.payment,
                        p.amount,
                        c.name as customer_name
                    FROM bs_payments p
                    INNER JOIN bs_customers c ON p.customer_id = c.id
                    WHERE p.date_active > DATE(p.datetime)
                      AND p.date_active > '$today_date'
                    ORDER BY p.date_active ASC, p.datetime DESC
                    LIMIT 50";
                        $result = $dbc->Query($sql);
                        $tomorrow_date = date('Y-m-d', strtotime('+1 day', $today));

                        while ($row = $dbc->Fetch($result)):
                            $is_created_today = date('Y-m-d', strtotime($row['datetime'])) == $today_date;
                            $is_tomorrow = $row['date_active'] == $tomorrow_date;
                        ?>
                            <tr>
                                <td style="padding: 1.2rem;">
                                    <code style="font-size: 1rem;"><?php echo htmlspecialchars($row['code']); ?></code>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <strong style="font-size: 1.1rem;"><?php echo htmlspecialchars($row['customer_name']); ?></strong>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <span style="font-size: 1rem; font-weight: 500;">
                                        <?php echo htmlspecialchars($row['payment']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <strong style="font-size: 1.1rem; color: #28a745;">
                                        ฿<?php echo number_format($row['amount'], 2); ?>
                                    </strong>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <div class="<?php echo $is_created_today ? 'text-success fw-bold' : ''; ?>" style="font-size: 1rem;">
                                        <?php echo date('d/m/Y H:i', strtotime($row['datetime'])); ?>
                                        <?php if ($is_created_today): ?>
                                            <i data-feather="star" class="text-warning ms-2" style="font-size: 1rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <div class="<?php echo $is_tomorrow ? 'text-warning fw-bold' : ''; ?>" style="font-size: 1rem;">
                                        <?php echo date('d/m/Y', strtotime($row['date_active'])); ?>
                                        <?php if ($is_tomorrow): ?>
                                            <i data-feather="star" class="text-warning ms-2" style="font-size: 1rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <?php if ($is_tomorrow): ?>
                                        <span style="color: #ffc107; font-weight: bold; font-size: 1rem;">ครบกำหนดพรุ่งนี้</span>
                                    <?php else: ?>
                                        <span style="color: #28a745; font-weight: bold; font-size: 1rem;">ยังไม่ครบกำหนด</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card" style="padding: 1rem;">
                <div class="card-header" style="padding: 1.5rem;">
                    <h5 class="mb-0" style="font-size: 1.3rem; font-weight: bold;">
                        <i data-feather="pie-chart" class="mr-2"></i>
                        ตามประเภทการชำระ
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <?php
                    $summary_sql = "SELECT 
                    payment,
                    COUNT(*) as count
                FROM bs_payments
                WHERE date_active > DATE(datetime)
                  AND date_active > '$today_date'
                GROUP BY payment
                ORDER BY count DESC";
                    $summary_result = $dbc->Query($summary_sql);
                    ?>
                    <?php while ($summary_row = $dbc->Fetch($summary_result)): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span style="font-size: 1.1rem; font-weight: 500;"><?php echo htmlspecialchars($summary_row['payment']); ?></span>
                            <span style="font-size: 1.1rem; font-weight: bold; color: #0d6efd;"><?php echo $summary_row['count']; ?> รายการ</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card" style="padding: 1rem;">
                <div class="card-header" style="padding: 1.5rem;">
                    <h5 class="mb-0" style="font-size: 1.3rem; font-weight: bold;">
                        <i data-feather="users" class="mr-2"></i>
                        ลูกค้า Top 10
                    </h5>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <?php
                    $customer_sql = "SELECT 
                    c.name as customer_name,
                    COUNT(*) as count
                FROM bs_payments p
                INNER JOIN bs_customers c ON p.customer_id = c.id
                WHERE p.date_active > DATE(p.datetime)
                  AND p.date_active > '$today_date'
                GROUP BY c.id, c.name
                ORDER BY count DESC
                LIMIT 10";
                    $customer_result = $dbc->Query($customer_sql);
                    ?>
                    <?php while ($customer_row = $dbc->Fetch($customer_result)): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span style="font-size: 1.1rem; font-weight: 500;"><?php echo htmlspecialchars($customer_row['customer_name']); ?></span>
                            <span style="font-size: 1.1rem; font-weight: bold; color: #198754;"><?php echo $customer_row['count']; ?> รายการ</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>