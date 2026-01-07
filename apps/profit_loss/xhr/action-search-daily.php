<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

if (isset($_POST['date']) && $_POST['date'] != "") {
    $sale_order = $dbc->GetRecord("bs_orders_profit", "SUM(amount) AS amount", "bs_orders_profit.status > 0 AND YEAR(bs_orders_profit.date) > 2024 AND bs_orders_profit.flag_hide = 0 AND DATE(bs_orders_profit.date)='" . $_POST['date'] . "'");
    $sale_noted = $dbc->GetRecord("bs_profit_daily", "comment", "bs_profit_daily.date ='" . $_POST['date'] . "'");

    $sql = "SELECT SUM(bs_purchase_spot_profit.amount*32.1507*(bs_purchase_spot_profit.rate_spot+bs_purchase_spot_profit.rate_pmdc)) AS amount FROM bs_purchase_spot_profit WHERE (bs_purchase_spot_profit.value_date = '" .  $_POST['date'] . "') AND bs_purchase_spot_profit.parent IS NULL 
                    AND flag_hide = 0
                    AND bs_purchase_spot_profit.rate_spot > 0
                    AND (bs_purchase_spot_profit.type LIKE 'physical'
    OR bs_purchase_spot_profit.type LIKE 'MTM') 
                    AND (bs_purchase_spot_profit.status > 0 OR bs_purchase_spot_profit.status = -1)
                    AND YEAR(date) > 2024 AND currency = 'USD'";
    $rst_silver = $dbc->Query($sql);
    $row_silver = $dbc->Fetch($rst_silver);

    $sql_usd = "SELECT SUM(bs_mapping_profit_orders_usd.total) AS totalordersusd FROM `bs_mapping_profit_sumusd` 
    LEFT OUTER JOIN  bs_mapping_profit_orders_usd ON bs_mapping_profit_orders_usd.mapping_id=bs_mapping_profit_sumusd.id 
    WHERE DATE(bs_mapping_profit_sumusd.mapped) = '" . $_POST['date'] . "'";
    $rst_usd = $dbc->Query($sql_usd);
    $row_usd = $dbc->Fetch($rst_usd);
    $totalordersusd = (isset($row_usd['totalordersusd']) && $row_usd['totalordersusd'] != null  && $row_usd['totalordersusd'] != 0 && $row_usd['totalordersusd'] != '') ? (float)$row_usd['totalordersusd'] : 0; // Ensure it's a float
    $totalordersusd_formatted = number_format($totalordersusd, 4);

    //Query 2: ผลรวม total จาก bs_mapping_profit thaibath
    $sql_thb = "SELECT SUM(bs_mapping_profit_orders.total) AS totalordersthb FROM `bs_mapping_profit` 
    LEFT OUTER JOIN  bs_mapping_profit_orders ON bs_mapping_profit_orders.mapping_id=bs_mapping_profit.id 
    WHERE DATE(bs_mapping_profit.mapped) = '" . $_POST['date'] . "'";
    $rst_thb = $dbc->Query($sql_thb);
    $row_thb = $dbc->Fetch($rst_thb);
    $totalordersthb = (isset($row_thb['totalordersthb']) && $row_thb['totalordersthb'] != null  && $row_thb['totalordersthb'] != 0 && $row_thb['totalordersthb'] != '') ? (float)$row_thb['totalordersthb'] : 0;  // Ensure it's a float
    $totalordersthb_formatted = number_format($totalordersthb, 4);

    //คำนวณผลรวม totalorders
    $totalorders = $totalordersusd; // Changed as per previous clarification
    $totalorders_formatted = number_format($totalorders, 4); // Format after addition


    // Query 3: ผลรวม usd true จาก bs_purchase_usd_profit
    $sql_usd_true = "SELECT SUM(amount * rate_finance) AS usd_true FROM bs_purchase_usd_profit WHERE (bs_purchase_usd_profit.value_date = '" .  $_POST['date'] . "') AND ( bs_purchase_usd_profit.status <> -1) AND (bs_purchase_usd_profit.type LIKE 'physical' OR bs_purchase_usd_profit.type LIKE 'MTM')
                   AND YEAR(date) > 2024";
    $rst_usd_true = $dbc->Query($sql_usd_true);
    $row_usd_true = $dbc->Fetch($rst_usd_true);
    $usd_true = isset($row_usd_true['usd_true']) ? (float)$row_usd_true['usd_true'] : 0;  // Ensure it's a float
    $usd_true_formatted = number_format($usd_true, 4);
    $profit = number_format($totalorders - $usd_true, 4);

    $sql_thb_true = "SELECT SUM(THBValue) AS thb_true FROM bs_purchase_spot_profit WHERE (bs_purchase_spot_profit.value_date = '" .  $_POST['date'] . "') AND bs_purchase_spot_profit.parent IS NULL 
                    AND flag_hide = 0
                    AND bs_purchase_spot_profit.rate_spot > 0
                    AND (bs_purchase_spot_profit.type LIKE 'physical'
    OR bs_purchase_spot_profit.type LIKE 'MTM') 
                    AND (bs_purchase_spot_profit.status > 0 OR bs_purchase_spot_profit.status = -1)
                    AND YEAR(date) > 2024 AND bs_purchase_spot_profit.currency = 'THB'";
    $rst_thb_true = $dbc->Query($sql_thb_true);
    $row_thb_true = $dbc->Fetch($rst_thb_true);
    $thb_true = isset($row_thb_true['thb_true']) ? (float)$row_thb_true['thb_true'] : 0;  // Ensure it's a float
    $thb_true_formatted = number_format($thb_true, 4);
    $profit_thb = number_format($totalordersthb - $thb_true, 4);
    $total_profit = number_format(($totalorders - $usd_true) + ($totalordersthb - $thb_true), 2); //Corrected Calculation
}
?>
<div id="printThis">
    <div class="container">
        <h4 class="text-center">Result of <?php echo date("d/M/y", strtotime($_POST['date'])); ?></h4>
        <div class="row">
            <div class="col">
                <?php
                if (isset($_POST['date']) && $_POST['date'] != "") {

                    if ($sale_order) {
                        echo "Total Order: " . $sale_order['amount'] . " kg  &nbsp; &nbsp;";

                        if (!empty($sale_noted['comment'])) {
                            echo "Noted: " . $sale_noted['comment'];
                        }
                    } else {
                        echo "ไม่พบยอดขายสำหรับวันที่ " . $_POST['date'];
                    }
                }

                ?>
            </div>
        </div>
        <div class="row py-4">
            <div class="col">
                <table class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="1">Net Buy Spot</th>
                            <th class="text-center" colspan="1">Net Buy FX</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_POST['date']) && $_POST['date'] != "") {
                            // --- ดึงข้อมูล Net Buy Spot ---
                            $sql_spot = "SELECT bs_purchase_spot.amount AS spot_amount, bs_purchase_spot.type AS spot_type, bs_purchase_spot.rate_spot AS spot_price, bs_purchase_spot.rate_pmdc AS spot_dc , bs_suppliers.name AS name FROM bs_purchase_spot 
                    LEFT OUTER JOIN bs_suppliers ON bs_purchase_spot.supplier_id = bs_suppliers.id
                    WHERE bs_purchase_spot.parent IS NULL
                    AND bs_purchase_spot.flag_hide = 0
                    AND bs_purchase_spot.rate_spot > 0
                    AND (bs_purchase_spot.type LIKE 'physical'
                    OR bs_purchase_spot.type LIKE 'stock') 
                    AND (bs_purchase_spot.status > 0 OR bs_purchase_spot.status = -1)
                    AND YEAR(bs_purchase_spot.date) > 2024 AND bs_purchase_spot.date ='" . $_POST['date'] . "'";
                            $result_spot = $dbc->Query($sql_spot);
                            $spot_data = [];
                            while ($row_spot = $dbc->Fetch($result_spot)) {
                                $spot_data[] = $row_spot;
                            }

                            // --- ดึงข้อมูล Net Buy FX ---
                            $sql_fx = "SELECT amount AS fx_amount, bank AS fx_bank, rate_exchange AS fx_price FROM bs_purchase_usd WHERE (bs_purchase_usd.status = 1 OR bs_purchase_usd.status = -1)AND (bs_purchase_usd.type LIKE 'physical')
                    AND bs_purchase_usd.parent IS NULL AND YEAR(date) > 2024 AND date ='" . $_POST['date'] . "'";
                            $result_fx = $dbc->Query($sql_fx);
                            $fx_data = [];
                            while ($row_fx = $dbc->Fetch($result_fx)) {
                                $fx_data[] = $row_fx;
                            }

                            // --- หาจำนวนแถวสูงสุด ---
                            $max_rows = max(count($spot_data), count($fx_data));

                            // --- แสดงผล ---
                            for ($i = 0; $i < $max_rows; $i++) {
                                echo '<tr>';
                                // --- คอลัมน์ Net Buy Spot ---
                                if (isset($spot_data[$i])) {
                                    echo '<td>' . $spot_data[$i]['spot_amount'] . ' kg (' . $spot_data[$i]['spot_type'] . '), Supplier = ' . $spot_data[$i]['name'] . ', Price = ' . $spot_data[$i]['spot_price'] . ', DC = ' . $spot_data[$i]['spot_dc'] . '</td>';
                                } else {
                                    echo '<td></td>';
                                }

                                // --- คอลัมน์ Net Buy FX ---
                                if (isset($fx_data[$i])) {
                                    echo '<td>' . number_format($fx_data[$i]['fx_amount'], 2) . '$ (' . $fx_data[$i]['fx_bank'] . '), Price = ' . $fx_data[$i]['fx_price'] . '</td>';
                                } else {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                            }

                            // --- กรณีไม่มีข้อมูลเลย ---
                            if ($max_rows === 0) {
                                echo '<tr><td colspan="2" class="text-center text-muted">ไม่พบข้อมูลสำหรับวันที่ ' . $_POST['date'] . '</td></tr>';
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php
                            if (isset($_POST['date']) && $_POST['date'] != "") {
                                // --- คำนวณผลรวม Amount ของ Net Buy Spot ---
                                $sql_sum_spot = "SELECT SUM(amount) AS total_spot_amount FROM bs_purchase_spot WHERE bs_purchase_spot.parent IS NULL
                        AND flag_hide = 0
                        AND bs_purchase_spot.rate_spot > 0
                        AND (bs_purchase_spot.type LIKE 'physical'
                        OR bs_purchase_spot.type LIKE 'stock') 
                        AND (bs_purchase_spot.status > 0 OR bs_purchase_spot.status = -1)
                        AND YEAR(date) > 2024 AND  date ='" . $_POST['date'] . "'";
                                $result_sum_spot = $dbc->Query($sql_sum_spot);
                                $row_sum_spot = $dbc->Fetch($result_sum_spot);
                                $total_spot_amount = $row_sum_spot['total_spot_amount'] ?? 0;

                                // --- คำนวณผลรวม Amount ของ Net Buy FX ---
                                $sql_sum_fx = "SELECT SUM(amount) AS total_fx_amount FROM bs_purchase_usd WHERE (bs_purchase_usd.status = 1 OR bs_purchase_usd.status = -1)AND (bs_purchase_usd.type LIKE 'physical')
                        AND bs_purchase_usd.parent IS NULL AND YEAR(date) > 2024 AND date ='" . $_POST['date'] . "'";
                                $result_sum_fx = $dbc->Query($sql_sum_fx);
                                $row_sum_fx = $dbc->Fetch($result_sum_fx);
                                $total_fx_amount = $row_sum_fx['total_fx_amount'] ?? 0;

                                echo '<th class="text-center">Total: ' . number_format($total_spot_amount, 4) . ' kg</th>';
                                echo '<th class="text-center">Total: ' . number_format($total_fx_amount, 2) . ' $</th>';
                            }
                            ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php
                if (isset($_POST['date']) && $_POST['date'] != "") {
                    $sql = "SELECT bs_spot_profit_daily.id,bs_spot_profit_daily.supplier_id,bs_spot_profit_daily.type,bs_spot_profit_daily.amount,
                            bs_spot_profit_daily.rate_spot,bs_spot_profit_daily.rate_pmdc,bs_spot_profit_daily.date,bs_suppliers.name
                            FROM bs_spot_profit_daily
                            LEFT OUTER JOIN bs_suppliers ON bs_spot_profit_daily.supplier_id = bs_suppliers.id
                            WHERE bs_spot_profit_daily.status = '1'
                            AND bs_spot_profit_daily.date ='" . $_POST['date'] . "' ";
                    $result = $dbc->Query($sql);
                    $long_amount_sum = 0;
                    $short_amount_sum = 0;
                    $long_details = array();
                    $short_details = array();

                    while ($row = $dbc->Fetch($result)) {
                        $type_display =  $row['type'];

                        if ($type_display == 'Long') {
                            $long_amount_sum += $row['amount'];
                            $long_details[] = array(
                                'amount' => $row['amount'],
                                'supplier_name' => $row['name'],
                                'rate_spot' => $row['rate_spot'],
                                'rate_pmdc' => $row['rate_pmdc'],
                            );
                        } else if ($type_display == 'Short') {
                            $short_amount_sum += $row['amount'];
                            $short_details[] = array(
                                'amount' => $row['amount'],
                                'supplier_name' => $row['name'],
                                'rate_spot' => $row['rate_spot'],
                                'rate_pmdc' => $row['rate_pmdc'],
                            );
                        }
                    }

                    if ($long_amount_sum > 0) {
                        echo '<p style="color: green;">Inventory Spot Long = ' . $long_amount_sum . ' kg</p>';
                        foreach ($long_details as $detail) {
                            echo '<p style="color: green;">= ' . $detail['amount'] . ' kg (' . $detail['supplier_name'] . '), Price = ' . $detail['rate_spot'] . ', DC = ' . $detail['rate_pmdc'] .  '</p>';
                        }
                    }
                    if ($short_amount_sum > 0) {
                        echo '<p style="color: red;">Inventory Spot Short = ' . $short_amount_sum . ' kg</p>';
                        foreach ($short_details as $detail) {
                            echo '<p style="color: red;">= ' . $detail['amount'] . ' kg (' . $detail['supplier_name'] . '), Price = ' . $detail['rate_spot'] . ', DC = ' . $detail['rate_pmdc'] .  '</p>';
                        }
                    }
                }

                ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?php
                if (isset($_POST['date']) && $_POST['date'] != "") {
                    $sql = "SELECT bs_usd_profit_daily.id,bs_usd_profit_daily.bank,bs_usd_profit_daily.type,bs_usd_profit_daily.amount,bs_usd_profit_daily.rate_exchange,
                            bs_usd_profit_daily.rate_finance,bs_usd_profit_daily.date,bs_usd_profit_daily.status
                            FROM bs_usd_profit_daily
                            WHERE bs_usd_profit_daily.status = '1'
                            AND bs_usd_profit_daily.date ='" . $_POST['date'] . "' ";
                    $result = $dbc->Query($sql);
                    $long_amount_sum = 0;
                    $short_amount_sum = 0;
                    $long_details = array();
                    $short_details = array();

                    while ($row = $dbc->Fetch($result)) {
                        $type_display =  $row['type'];

                        if ($type_display == 'Long') {
                            $long_amount_sum += $row['amount'];
                            $long_details[] = array(
                                'amount' => $row['amount'],
                                'bank' => $row['bank'],
                                'rate_exchange' => $row['rate_exchange'],
                            );
                        } else if ($type_display == 'Short') {
                            $short_amount_sum += $row['amount'];
                            $short_details[] = array(
                                'amount' => $row['amount'],
                                'bank' => $row['bank'],
                                'rate_exchange' => $row['rate_exchange'],
                            );
                        }
                    }

                    if ($long_amount_sum > 0) {
                        echo '<p style="color: green;">Inventory FX Long = ' . $long_amount_sum . ' $</p>';
                        foreach ($long_details as $detail) {
                            echo '<p style="color: green;">= ' . number_format($detail['amount'], 2) . ' $ (' . $detail['bank'] . '), Price = ' . number_format($detail['rate_exchange'], 4) . '</p>';
                        }
                    }
                    if ($short_amount_sum > 0) {
                        echo '<p style="color: red;">Inventory FX Short = ' . $short_amount_sum . ' $</p>';
                        foreach ($short_details as $detail) {
                            echo '<p style="color: red;">= ' . number_format($detail['amount'], 2) . ' $ (' . $detail['bank'] . '), Price = ' . number_format($detail['rate_exchange'], 4) . '</p>';
                        }
                    }
                }

                ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>USD Dollar Today = <?php echo number_format($row_silver['amount'], 2); ?> $</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <p>Total Profit = <?php echo $total_profit; ?> $</p>
            </div>
        </div>
    </div>
</div>

<div id="notPrint">
    <div class="container">
        <div class="mb-2">
            <script type="text/javascript">
                var selectedDate = "<?php if (isset($_POST['date'])) {
                                        echo trim($_POST['date']);
                                    } ?>";
            </script>
            <button class="btn btn-primary" onclick="fn.app.profit_loss.daily.dialog_add_spot('<?php if (isset($_POST['date'])) {echo trim($_POST['date']);} ?>')">ADD SPOT</button>
            <button class="btn btn-dark" onclick="fn.app.profit_loss.daily.dialog_add_fx('<?php if (isset($_POST['date'])) {echo trim($_POST['date']);} ?>')">ADD FX</button>
            <button class="btn btn-primary" onclick="fn.app.profit_loss.daily.dialog_add('<?php if (isset($_POST['date'])) {echo trim($_POST['date']); } ?>')">ADD NOTED</button>

        </div>
        <br>
        <div class="row">
            <div class="col-6">
                <table id="tblSPOT" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
                    <thead>
                        <tr>
                            <th class="text-white bg-dark text-center" colspan="8">SPOT</th>
                        </tr>
                        <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Rate Spot</th>
                            <th class="text-center">Rate Pmdc</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_POST['date']) && $_POST['date'] != "") {

                            $sql = "SELECT bs_spot_profit_daily.id,bs_spot_profit_daily.supplier_id,bs_spot_profit_daily.type,bs_spot_profit_daily.amount,
                        bs_spot_profit_daily.rate_spot,bs_spot_profit_daily.rate_pmdc,bs_spot_profit_daily.date,bs_suppliers.name 
                        FROM bs_spot_profit_daily 
                        LEFT OUTER JOIN bs_suppliers ON bs_spot_profit_daily.supplier_id = bs_suppliers.id
                        WHERE bs_spot_profit_daily.status = '1' 
                        AND bs_spot_profit_daily.date ='" . $_POST['date'] . "' ";
                            $result = $dbc->Query($sql);
                            while ($row = $dbc->Fetch($result)) {
                                echo '<tr>';
                                echo '<td class="text-center">' . $row['date'] . ' </td>';
                                echo '<td class="text-center">' . $row['name'] . ' </td>';
                                echo '<td class="text-right">' . $row['amount'] . ' </td>';
                                echo '<td class="text-center">' . $row['type'] . ' </td>';
                                echo '<td class="text-right">' . $row['rate_spot'] . ' </td>';
                                echo '<td class="text-right">' . $row['rate_pmdc'] . ' </td>';
                                echo '<td class="text-center">';
                                echo '<button onclick="fn.app.profit_loss.daily.dialog_edit_spot(' . $row['id'] . ')" class="btn btn-xs btn-outline-warning mr-1 btn-icon"><i class="far fa-pen"></i></button>';
                                echo '<button onclick="fn.app.profit_loss.daily.remove_spot(' . $row['id'] . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }

                        ?>
                    </tbody>
                </table>

            </div>
            <div class="col-6">
                <table id="tblFX" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
                    <thead>
                        <tr>
                            <th class="text-white bg-dark text-center" colspan="6">FX</th>
                        </tr>
                        <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Bank</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Exchange Rate</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_POST['date']) && $_POST['date'] != "") {

                            $sql = "SELECT bs_usd_profit_daily.id,bs_usd_profit_daily.bank,bs_usd_profit_daily.type,bs_usd_profit_daily.amount,bs_usd_profit_daily.rate_exchange,
                        bs_usd_profit_daily.rate_finance,bs_usd_profit_daily.date,bs_usd_profit_daily.status
                        FROM bs_usd_profit_daily 
                        WHERE bs_usd_profit_daily.status = '1' 
                        AND bs_usd_profit_daily.date ='" . $_POST['date'] . "' ";
                            $result = $dbc->Query($sql);
                            while ($row = $dbc->Fetch($result)) {
                                echo '<tr>';
                                echo '<td class="text-center">' . $row['date'] . ' </td>';
                                echo '<td class="text-center">' . $row['bank'] . ' </td>';
                                echo '<td class="text-right">' . $row['amount'] . ' </td>';
                                echo '<td class="text-right">' . $row['rate_exchange'] . ' </td>';
                                echo '<td class="text-center">' . $row['type'] . ' </td>';
                                echo '<td class="text-center">';
                                echo '<button onclick="fn.app.profit_loss.daily.dialog_edit_fx(' . $row['id'] . ')" class="btn btn-xs btn-outline-warning mr-1 btn-icon"><i class="far fa-pen"></i></button>';
                                echo '<button onclick="fn.app.profit_loss.daily.remove_fx(' . $row['id'] . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }

                        ?>
                    </tbody>
                </table>

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table id="tblNoted" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
                    <thead>
                        <tr>
                            <th class="text-white bg-dark text-center" colspan="3">NOTED</th>
                        </tr>
                        <tr>
                            <th class="text-center">Date</th>
                            <th class="text-center">Noted</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_POST['date']) && $_POST['date'] != "") {
                            $sql = "SELECT * FROM bs_profit_daily WHERE date ='" . $_POST['date'] . "' ";
                            $result = $dbc->Query($sql);
                            while ($row = $dbc->Fetch($result)) {
                                echo '<tr>';
                                echo '<td class="text-center">' . $row['date'] . ' </td>';
                                echo '<td class="text-center">' . $row['comment'] . ' </td>';
                                echo '<td class="text-center">';
                                echo '<button onclick="fn.app.profit_loss.daily.dialog_edit(' . $row['id'] . ')" class="btn btn-xs  btn-outline-warning mr-1 btn-icon"><i class="far fa-pen"></i></button>';
                                echo '<button onclick="fn.app.profit_loss.daily.remove(' . $row['id'] . ')" class="btn btn-xs btn-outline-danger mr-1 btn-icon"><i class="fa fa-trash"></i></button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
            </div>
        </div>
        <style>
            #printThis {
                /* Styles for the div ที่คุณต้องการพิมพ์ */
                padding: 20px;
                border: 1px solid #ccc;
                margin-bottom: 20px;
                /* Center the div */
                margin-left: auto;
                margin-right: auto;
                max-width: 800px;
                /* You can adjust this value */
            }

            @media print {
                body * {
                    visibility: hidden;
                }

                #printThis,
                #printThis * {
                    visibility: visible;
                }

                #printThis {
                    position: absolute;
                    left: 0;
                    top: 0;
                    /* Center the div for printing */
                    width: 100%;
                    text-align: center;
                    background-color: #FFFFFF;
                    /* เปลี่ยนสีพื้นหลังเป็นสีขาว */
                }

                #notPrint {
                    display: none;
                }

                .btn-area {
                    display: none;
                }

                /* Hide header and footer */
                @page {
                    size: auto;
                    /* auto is the initial value */
                    margin: 0;
                    /* this affects the margin in the printer settings */
                }
            }
        </style>
        <script type="text/javascript">
            function printDiv(divId) {
                var printContents = document.getElementById(divId).innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;

                window.print();

                document.body.innerHTML = originalContents;
            }
        </script>