<?php
function isValidRecord($record)
{
    return $record && is_array($record) && !empty($record);
}

$today = time();
$production_id = intval($_GET['production_id']); 
$production = $dbc->GetRecord("bs_productions", "*", "id=" . $production_id);

$furnace = $dbc->GetRecord("bs_productions_furnace", "*", "round=" . $production_id);

$get_product  = $dbc->GetRecord("bs_products", "*", "id=" . $production['product_id']);

$get_product_out  = $dbc->GetRecord("bs_products", "*", "id=" . $production['product_id_out']);

$silver_save = $dbc->GetRecord("bs_productions_silver_save", "*", "round =" . $production_id);



$sql = "SELECT bs_packing_items.code AS min
FROM bs_packing_items 
LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
WHERE bs_packing_items.production_id  = '" . $production_id . "'
ORDER BY bs_packing_items.id ASC LIMIT 1";
$rst = $dbc->Query($sql);
$getmin = $dbc->Fetch($rst);

$sql = "SELECT bs_packing_items.code AS max
FROM bs_packing_items 
LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
WHERE bs_packing_items.production_id  = '" . $production_id . "'
ORDER BY bs_packing_items.id DESC LIMIT 1";
$rst = $dbc->Query($sql);
$getmax = $dbc->Fetch($rst);

$sql = "SELECT bs_employees.fullname FROM bs_employees
LEFT OUTER JOIN bs_productions ON bs_productions.approver_weight = bs_employees.id
WHERE bs_productions.approver_weight = '" . $production['approver_weight'] . "' AND bs_productions.id ='" . $production_id . "'";
$rst = $dbc->Query($sql);
$approver_weight = $dbc->Fetch($rst);

$sql = "SELECT bs_employees.fullname FROM bs_employees
LEFT OUTER JOIN bs_productions ON bs_productions.approver_general = bs_employees.id
WHERE bs_productions.approver_general = '" . $production['approver_general'] . "' AND bs_productions.id ='" . $production_id . "'";
$rst = $dbc->Query($sql);
$approver_general = $dbc->Fetch($rst);


?>
<div class="btn-area btn-group mb-2">
    <button type="button" onclick="window.history.back();" class="btn btn-dark">Back</button>
    <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
        <i class="mr-2" data-feather="printer"></i>Print
    </button>
</div>
<div class="card">
    <div class="card-body mr-4 ml-4"></div>
    <div class="d-flex align-items-center container">
        <div>
            <img class="pull-right" src="img/bowins.png" width="100px" height="100px">
        </div>
    </div>
    <div class="container-fluid docu-print mt-0">
        <h4 class="text-center">ใบบันทึกยอดการผลิตเม็ดเงิน</h4>
        <div class="row">
            <div class="col-7 small-text mt-3">
                <dl class="row col-8 offset-0">
                    <dt class="col-6">Invoice No. :</dt>
                    <dd class="col-5 under-line ">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    if (isset($import['import_number']) && !empty($import['import_number'])) {
                                        $sql = "SELECT DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                        $rst_in = $dbc->Query($sql);
                                        while ($income = $dbc->Fetch($rst_in)) {
                                            echo $income['coa'] . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </dd>
                    <dt class="col-6 mt-2">Type :</dt>
                    <dd class="col-6 mt-2 under-line"> <?php echo isset($get_product['name']) ? $get_product['name'] : ''; ?></dd>
                    <dt class="col-6 mt-2">แท่งเงินใช้หลอม :</dt>
                    <dd class="col-6 mt-2 under-line"> <?php echo isset($get_product_out['name']) ? $get_product_out['name'] : ''; ?></dd>
                    <dt class="col-6 mt-2">ประเภทงาน :</dt>
                    <dd class="col-6 mt-2 under-line"> <?php echo $production['type_material']; ?></dd>
                </dl>
                <div class="row mt-2">
                    <div class="col">Supplier :</div>
                    <div class="col">
                        <div class="under-line"><?php echo $production['supplier']; ?></div>
                    </div>
                    <div class="col">
                        <div class="under-line"><?php
                                                if (isset($production['data']) && $production['data'] != null) {
                                                    $data = json_decode($production['data'], true);
                                                    if (isset($data['import']) && is_array($data['import'])) {
                                                        foreach ($data['import'] as $import) {
                                                            if (isset($import['import_number']) && !empty($import['import_number'])) {
                                                                $sql = "SELECT bs_incoming_plans.remark , bs_products_import.name FROM bs_incoming_plans 
                                    LEFT OUTER JOIN bs_products_import ON bs_incoming_plans.remark = bs_products_import.code
                                    WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                                                $rst_in = $dbc->Query($sql);
                                                                while ($income = $dbc->Fetch($rst_in)) {
                                                                    echo $income['name'] . '<br />';
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?> </div>
                    </div>
                    <div class="col">
                        <div>กิโล / แท่ง</div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">เวลาเปิดเตา :</div>
                    <div class="col">
                        <div class="under-line"><?php echo (isset($furnace['time_start'])) ? $furnace['time_start'] : ''; ?></div>
                    </div>
                    <div class="col">เวลาปิดเตา :</div>
                    <div class="col">
                        <div class="under-line"><?php echo (isset($furnace['time_end'])) ? $furnace['time_end'] : ''; ?></div>
                    </div>
                    <div class="col">เบ้าหลอม No.:</div>
                    <div class="col">
                        <div class="under-line"><?php echo (isset($furnace['crucible'])) ? $furnace['crucible'] : ''; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-5 small-text mt-5">
                <dl class="row col-10 offset-8">
                    <dd class="col-6 mt-0">
                        <table border="1" style="width:70%">
                            <tr style="height:40px">
                                <th class="text-center">LOT No.</th>
                            </tr>
                            <tr style="height:60px">
                                <td class="text-center"><?php echo $production['round']; ?></td>
                            </tr>
                        </table>
                    </dd>
                </dl>
            </div>
        </div>
        <br>
        <span class="page-number">
            <table border="1" style="width:100%">
                <tr>
                    <th class="text-center">วันที่</th>
                    <th class="text-center">เวลาเริ่มงาน</th>
                    <th class="text-center">No.-Shipping</th>
                    <th class="text-center">จำนวนแท่ง</th>
                    <th class="text-center">น้ำหนัก-Brinks</th>
                    <th class="text-center">น้ำหนัก-ชั่งจริง</th>
                    <th class="text-center">นน.<br>ขาด/เกิน</th>
                    <th class="text-center">นน.<br>เฉลี่ย</th>
                </tr>
                <tr style="height:80px">
                    <td class="text-center"><?php echo $production['submited']; ?></td>
                    <td class="text-center"><?php echo $production['approver_appointment']; ?></td>
                    <td class="text-center">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    if (isset($import['import_number']) && !empty($import['import_number'])) {
                                        $sql = "SELECT DISTINCT(brand) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                        $rst_in = $dbc->Query($sql);
                                        while ($income = $dbc->Fetch($rst_in)) {
                                            echo $income['brand'] . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    echo (isset($import['import_bar']) ? $import['import_bar'] : '') . '<br />';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    echo (isset($import['import_weight_in']) ? $import['import_weight_in'] : '') . '<br />';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    echo (isset($import['import_weight_actual']) ? $import['import_weight_actual'] : '') . '<br />';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    echo (isset($import['import_weight_margin']) ? $import['import_weight_margin'] : '') . '<br />';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if (isset($production['data']) && $production['data'] != null) {
                            $data = json_decode($production['data'], true);
                            if (isset($data['import']) && is_array($data['import'])) {
                                foreach ($data['import'] as $import) {
                                    echo (isset($import['import_weight_bar']) ? $import['import_weight_bar'] : '') . '<br />';
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <h5 class="text-left mt-2">2. SILVER ก่อนเริ่มการผลิตและเข้าคลัง (BEGINNING ANDENDIND GOODS TRANSFER FORM)</h5>
            <table border="1" style="width:100%">
                <tr>
                    <th class="text-center">สินค้าเข้า - ก่อนเริ่มการผลิต</th>
                    <th class="text-center">สินค้าออก - เข้าคลัง</th>
                </tr>
                <tr>
                    <td>
                        <p>หลอมรอบแรกรวมเศษ</p>
                        <p>เม็ดเสียนำเข้าผลิต</p>
                        <div class="row">
                            <div class="col">
                                LOT No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    if (!empty($production['weight_in_safe']) || ($production['pack_type'] == 'ถุงรวมเศษ')) {
                                        $sqlscrap = "SELECT * FROM bs_scrap_items WHERE production_id= " . $production_id . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า' ";
                                        $query = $dbc->Query($sqlscrap);
                                        while ($scrap = $dbc->Fetch($query)) {
                                            if (isValidRecord($scrap)) {
                                                $sql1 = "SELECT DISTINCT bs_scrap_items.production_id,bs_scrap_items.parent
                                        FROM bs_scrap_items
                                        WHERE parent = '" . $scrap['id'] . "' ";
                                                $rst1 = $dbc->Query($sql1);
                                                while ($productions = $dbc->Fetch($rst1)) {
                                                    if (isset($productions['production_id']) && !empty($productions['production_id'])) {
                                                        $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . intval($productions['production_id']));
                                                        if (isValidRecord($get_product)) {
                                                            echo $get_product['round'] . '<br />';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else if (($production['product_id'] == '4') || ($production['product_id'] == '3') || ($production['product_id'] == '1') || ($production['product_id'] == '9') || ($production['product_id'] == '8') || ($production['product_id'] == '6') || ($production['product_id'] == '2')) {
                                        echo $production['round'];
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col">
                                Invoice No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    if ($production['PMR'] == 'PMR' || ($production['product_id'] == '4') || ($production['product_id'] == '3') || ($production['product_id'] == '1') || ($production['product_id'] == '9') || ($production['product_id'] == '8') || ($production['product_id'] == '6') || ($production['product_id'] == '2')) {
                                        if (isset($production['data']) && $production['data'] != null) {
                                            $data = json_decode($production['data'], true);
                                            if (isset($data['import']) && is_array($data['import'])) {
                                                foreach ($data['import'] as $import) {
                                                    if (isset($import['import_number']) && !empty($import['import_number'])) {
                                                        $sql = "SELECT  DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%'";
                                                        $rst_in = $dbc->Query($sql);

                                                        while ($income = $dbc->Fetch($rst_in)) {
                                                            echo $income['coa'] . '<br />';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else if (!empty($production['weight_in_safe']) || ($production['pack_type'] == 'ถุงรวมเศษ')) {
                                        $scrap = $dbc->GetRecord("bs_scrap_items", "*", "production_id=" . $production['id'] . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า'");
                                        if (isValidRecord($scrap)) {
                                            $scrap_parent = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap['id']);
                                            if (isValidRecord($scrap_parent)) {
                                                $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . $scrap_parent['production_id']);
                                                if (isset($production['data']) && $production['data'] != null && isValidRecord($get_product) && isset($get_product['data'])) {
                                                    $data = json_decode($production['data'], true);
                                                    $dataim = json_decode($get_product['data'], true);
                                                    if (isset($data['import']) && is_array($data['import']) && isset($dataim['import']) && is_array($dataim['import'])) {
                                                        foreach ($data['import'] as $import) {
                                                            foreach ($dataim['import'] as $import2) {
                                                                if (isset($import['import_number']) && !empty($import['import_number']) && isset($import2['import_number']) && !empty($import2['import_number'])) {
                                                                    $sql = "SELECT  DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' OR brand LIKE '%" . $import2['import_number'] . "%' ";
                                                                    $rst_in = $dbc->Query($sql);

                                                                    while ($income = $dbc->Fetch($rst_in)) {
                                                                        echo $income['coa'] . '<br />';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (isset($production['data']) && $production['data'] != null) {
                                            $data = json_decode($production['data'], true);
                                            if (isset($data['import']) && is_array($data['import'])) {
                                                foreach ($data['import'] as $import) {
                                                    if (isset($import['import_number']) && !empty($import['import_number'])) {
                                                        $sql = "SELECT  DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%'";
                                                        $rst_in = $dbc->Query($sql);

                                                        while ($income = $dbc->Fetch($rst_in)) {
                                                            echo $income['coa'] . '<br />';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                เม็ดเสียรอบก่อนหน้า :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    if ($production['weight_in_safe'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_in_safe'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                LOT No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    echo $production['round'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Invoice No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    if (isset($production['data']) && $production['data'] != null) {
                                        $data = json_decode($production['data'], true);
                                        if (isset($data['import']) && is_array($data['import'])) {
                                            foreach ($data['import'] as $import) {
                                                if (isset($import['import_number']) && !empty($import['import_number'])) {
                                                    $sql = "SELECT DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                                    $rst_in = $dbc->Query($sql);
                                                    while ($income = $dbc->Fetch($rst_in)) {
                                                        echo $income['coa'] . '<br />';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                น้ำหนักหลอมแท่ง (เตาแรก) :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_in_1'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_in_1'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                รวม :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    if ($production['weight_in_safe'] + $production['weight_in_1'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_in_safe'] + $production['weight_in_1'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p>เม็ดเสียนำออก</p>
                        <div class="row">
                            <div class="col">
                                LOT No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    <?php
                                    echo $production['round'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                เม็ดรอการผลิต :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_out_safe'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_out_safe'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                เม็ดเสียรอ Refine :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_out_refine'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_out_refine'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                LOT No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                เศษเบ้า :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                LOT No. :
                            </div>
                            <div class="col">
                                <div class="col-6 under-line ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                ผง .........................
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                ผงเปียก
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    -
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                นน. Packing
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    echo $production['weight_out_packing'];
                                    ?> Kgs.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                น้ำหนักออกทั้งหมด
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    echo $production['weight_out_total'];
                                    ?> Kgs.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Processing Loss
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_margin'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_margin'] . ' Kgs./ton';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>นำเข้าหลอม</p>
                        <div class="row">
                            <div class="col">
                                LOT No. :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    echo $production['round'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Invoice No. :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if (isset($production['data']) && $production['data'] != null) {
                                        $data = json_decode($production['data'], true);
                                        if (isset($data['import']) && is_array($data['import'])) {
                                            foreach ($data['import'] as $import) {
                                                if (isset($import['import_number']) && !empty($import['import_number'])) {
                                                    $sql = "SELECT DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                                    $rst_in = $dbc->Query($sql);
                                                    while ($income = $dbc->Fetch($rst_in)) {
                                                        echo $income['coa'] . '<br />';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                น้ำหนักหลอมแท่ง :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_in_2'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_in_2'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                น้ำหนักหลอมเม็ด :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_in_3'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_in_3'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                แพ็คเม็ด / แท่งเงิน:
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    if ($production['weight_in_4'] == '0.0000') {
                                        echo "-";
                                    } else {
                                        echo $production['weight_in_4'] . 'Kgs.';
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                น้ำหนักเข้าทั้งหมด :
                            </div>
                            <div class="col">
                                <div class="col-8 under-line ">
                                    <?php
                                    echo $production['weight_in_total'];
                                    ?> Kgs.
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-center">แท่งเข้าเซฟ</p>
                        <div class="row">
                            <div class="col">จำนวน</div>
                            <div class="col">
                                <?php
                                if (isValidRecord($silver_save) && $silver_save['bar'] == '0.0000') {
                                    echo "-";
                                } else {
                                    echo (isValidRecord($silver_save) ? $silver_save['bar'] : '0') . 'Kgs.';
                                }

                                ?> แท่ง
                            </div>
                            <div class="col">
                                <?php
                                if (isValidRecord($silver_save) && $silver_save['amount'] == '0.0000') {
                                    echo "-";
                                } else {
                                    echo (isValidRecord($silver_save) ? $silver_save['amount'] : '0') . 'Kgs.';
                                }
                                ?>Kgs.
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="text-center">ผู้เปิดเซฟเก็บของเข้าคลัง</p>
                        <div class="row">
                            <div class="col">เวลา <?php echo isValidRecord($silver_save) && isset($silver_save['time']) ? $silver_save['time'] : ''; ?>น.</div>
                            <div class="col">ลงชื่อ <?php echo isValidRecord($silver_save) && isset($silver_save['user']) ? $silver_save['user'] : ''; ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">

                        <div class="row">
                            <div class="col">
                                <p class="text-left"><u>หมายเหตุ</u></p>
                            </div>
                            <div class="col">
                                <?php
                                echo $production['remark'];
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="text-left"><u>ผู้บันทึก</u></p>
                        <div class="row">
                            <div class="col">ผู้บันทึกเข้า ลงชื่อ <?php echo isValidRecord($approver_weight) && isset($approver_weight['fullname']) ? $approver_weight['fullname'] : ''; ?> (ตัวบรรจง)</div>
                            <div class="col">ผู้บันทึกออก ลงชื่อ <?php echo isValidRecord($approver_general) && isset($approver_general['fullname']) ? $approver_general['fullname'] : ''; ?> (ตัวบรรจง)</div>
                        </div>
                    </td>
                </tr>
            </table>
        </span>
    </div>
    <br>
    <h5>เศษเสียรอการผลิตนำออก</h5>
    <table border="1" style="width:100%">
        <thead">
            <tr>
                <th class="text-center">รอบการผลิต</th>
                <th class="text-center">หมายเลขเศษ</th>
                <th class="text-center">ประเภท</th>
                <th class="text-center">น้ำหนัก</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $aSum = array(0, 0);
                $sql = "SELECT bs_scrap_items.id, bs_scrap_items.production_id,bs_scrap_items.code,bs_scrap_items.pack_name,
                bs_scrap_items.weight_expected,bs_productions.round
                FROM bs_scrap_items
                LEFT OUTER JOIN bs_productions ON bs_scrap_items.production_id = bs_productions.id
                WHERE production_id = '" . $production_id . "' AND pack_name != 'เศษเสียรอการผลิตนำเข้า'";
                $rst = $dbc->Query($sql);
                $number = 1;
                while ($product = $dbc->Fetch($rst)) {
                    echo '<tr>';
                    echo '<td class="text-center">' . $product['round'] . '</td>';
                    echo '<td class="text-center">' . $product['code'] . '</td>';
                    echo '<td class="text-center">' . $product['pack_name'] . '</td>';
                    echo '<td class="text-center">' . $product['weight_expected'] . '</td>';
                    echo '</tr>';
                    $aSum[0] += 1;
                    $aSum[1] += $product['weight_expected'];
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center" colspan="1">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
                    <th class="text-center" colspan="2"></th>
                    <th class="text-center"><?php echo $aSum[1]; ?></th>
                </tr>

            </tfoot>
    </table>
    <br>
    <h5>เศษเสียรอการผลิตนำเข้า</h5>
    <table border="1" style="width:100%">
        <thead">
            <tr>
                <th class="text-center">รอบการผลิต</th>
                <th class="text-center">หมายเลขเศษ</th>
                <th class="text-center">ประเภท</th>
                <th class="text-center">น้ำหนัก</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $aSum = array(0, 0);
                $sqlscrap = "SELECT * FROM bs_scrap_items WHERE production_id= " . $production_id . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า' ";
                $query = $dbc->Query($sqlscrap);
                while ($scrap = $dbc->Fetch($query)) {
                    if (isValidRecord($scrap)) {
                        $sql1 = "SELECT bs_scrap_items.id, bs_scrap_items.production_id,bs_scrap_items.code,bs_scrap_items.pack_name,
                    bs_scrap_items.weight_expected,bs_scrap_items.parent
                    FROM bs_scrap_items
                    WHERE parent = '" . $scrap['id'] . "' ";
                        $rst1 = $dbc->Query($sql1);
                        while ($productions = $dbc->Fetch($rst1)) {
                            if (isset($productions['production_id']) && !empty($productions['production_id'])) {
                                $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . intval($productions['production_id']));
                                if (isValidRecord($get_product)) {
                                    echo '<tr>';
                                    echo '<td class="text-center">' . $get_product['round'] . '</td>';
                                    echo '<td class="text-center">' . $productions['code'] . '</td>';
                                    echo '<td class="text-center">' . $productions['pack_name'] . '</td>';
                                    echo '<td class="text-center">' . $productions['weight_expected'] . '</td>';
                                    echo '</tr>';
                                    $aSum[0] += 1;
                                    $aSum[1] += $productions['weight_expected'];
                                }
                            }
                        }
                    }
                }

                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center" colspan="1">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
                    <th class="text-center" colspan="2"></th>
                    <th class="text-center"><?php echo $aSum[1]; ?></th>
                </tr>

            </tfoot>
    </table>
    <br>
</div> <!--  close div card -->
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<div class="card">
    <div class="card-body mr-4 ml-4"></div>
    <div class="d-flex align-items-center container">
        <div>
            <img class="pull-right" src="img/bowins.png" width="130px" height="130px">
        </div>
    </div>

    <?php if ($production['product_id'] !== '6') {
    ?>
        <!-- <hr style="border:1px solid #000"> -->
        <div class="container docu-print mt-0">
            <h2 class="text-center">Packing List (OUT)</h2>
            <div class="row">
                <div class="col-6 small-text mt-3">
                    <dl class="row col-8 offset-0">
                        <dt class="col-6">Invoice No. :</dt>
                        <dd class="col-5 under-line ">
                            <?php
                            if (isset($production['data']) && $production['data'] != null) {
                                $data = json_decode($production['data'], true);
                                if (isset($data['import']) && is_array($data['import'])) {
                                    foreach ($data['import'] as $import) {
                                        if (isset($import['import_number']) && !empty($import['import_number'])) {
                                            $sql = "SELECT DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                            $rst_in = $dbc->Query($sql);
                                            while ($income = $dbc->Fetch($rst_in)) {
                                                echo $income['coa'] . '<br />';
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </dd>
                        <dt class="col-6 mt-2">Serial Number:</dt>
                        <dd class="col-6 mt-2 under-line"><?php echo isValidRecord($getmin) && isset($getmin['min']) ? $getmin['min'] : ''; ?> - <?php echo isValidRecord($getmax) && isset($getmax['max']) ? $getmax['max'] : ''; ?></dd>
                    </dl>
                </div>
                <div class="col-6 small-text mt-4">
                    <dl class="row col-10 offset-8">
                        <dt class="col-5">Production Lot. :</dt>
                        <dd class="col-4 under-line "><?php echo $production['round']; ?></dd>
                        <dt class="col-5 mt-2">Date :</dt>
                        <dd class="col-4 mt-2 under-line"><?php echo date("Y-m-d"); ?></dd>
                    </dl>
                </div>
            </div>
            <span class="page-number">
                <table class="p-5 mt-2" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Lot No.</th>
                            <th class="text-center">Serial Number</th>
                            <th class="text-center">Pack Type</th>
                            <th class="text-center">Net weight (kg)</th>
                            <th class="text-center">Weight include Packaging</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $aSum = array(0, 0, 0, 0);
                        $sql = "SELECT bs_packing_items.id,bs_packing_items.production_id,bs_packing_items.code,bs_packing_items.pack_name,
                        bs_packing_items.pack_type,bs_packing_items.weight_actual,bs_packing_items.weight_expected,bs_productions.round,bs_productions.id,bs_packing_items.status
                        FROM bs_packing_items 
                        LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                        WHERE bs_packing_items.status != '-1' AND bs_packing_items.production_id = '" . $production_id . "'
                        ORDER BY bs_packing_items.code ASC";
                        $rst = $dbc->Query($sql);
                        $number = 1;
                        while ($product = $dbc->Fetch($rst)) {
                            if (!empty($product['weight_in_safe']) || ($product['pack_type'] == 'ถุงรวมเศษ')) {
                                $scrap = $dbc->GetRecord("bs_scrap_items", "*", "production_id=" . $production_id . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า'");
                                if (isValidRecord($scrap)) {
                                    $scrap_parent = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap['id']);
                                    if (isValidRecord($scrap_parent)) {
                                        $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . $scrap_parent['production_id']);
                                        if (isValidRecord($get_product)) {
                                            echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $number . '</td>';
                                            echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $get_product['round'] . '-' . $product['round'] . '</td>';
                                            echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $product['code'] . '</td>';
                                            echo '<td bgcolor="lightblue" class="text-center font-weight-bold">ถุงปกติ</td>';
                                            echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $product['weight_expected'] . '</td>';
                                            echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $product['weight_actual'] . '</td>';
                                        }
                                    }
                                }
                            } else {
                                echo '<tr>';
                                echo '<td class="text-center">' . $number . '</td>';
                                echo '<td class="text-center">' . $product['round'] . '</td>';
                                echo '<td class="text-center">' . $product['code'] . '</td>';
                                echo '<td class="text-center">' . $product['pack_type'] . '</td>';
                                echo '<td class="text-center">' . $product['weight_expected'] . '</td>';
                                echo '<td class="text-center">' . $product['weight_actual'] . '</td>';
                            }

                            $number++;
                            echo '</tr>';

                            $aSum[0] += 1;
                            $aSum[1] += $product['weight_actual'];
                            $aSum[2] += $product['weight_actual'] - $product['weight_expected'];
                            $aSum[3] += $product['weight_expected'];
                        }
                        ?>

                    </tbody>
                </table>
                <div class="container-fluid mt-4">
                    <div class="row">
                        <div class="col-sm-8">
                        </div>
                        <div class="col-sm-4 small-text">
                            <table class="p-5 mt-2" width="100%">
                                <tr>
                                    <th>น้ำหนักชั่งรวม Packkaging</th>
                                    <th class="text-right"><?php echo number_format($aSum[1], 4); ?> </th>
                                </tr>
                                <tr>
                                    <th>น้ำหนัก Packaging (รวม)</th>
                                    <th class="text-right"><?php echo number_format($aSum[2], 4); ?></th>
                                </tr>
                                <tr>
                                    <th>น้ำหนักสุทธิ</th>
                                    <th class="text-right"><?php echo number_format($aSum[3], 4); ?></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
        } else {
            ?>
                <!-- <hr style="border:1px solid #000"> -->
                <div class="container docu-print mt-0">
                    <h2 class="text-center">Packing List (OUT)</h2>
                    <div class="row">
                        <div class="col-6 small-text mt-3">
                            <dl class="row col-8 offset-0">
                                <dt class="col-6 mt-2">Serial Number:</dt>
                                <dd class="col-6 mt-2 under-line"><?php echo isValidRecord($getmin) && isset($getmin['min']) ? $getmin['min'] : ''; ?> - <?php echo isValidRecord($getmax) && isset($getmax['max']) ? $getmax['max'] : ''; ?></dd>
                                <dt class="col-6">Production Lot. :</dt>
                                <dd class="col-5 under-line "><?php echo $production['round']; ?></dd>
                            </dl>
                        </div>
                        <div class="col-6 small-text mt-4">
                            <dl class="row col-10 offset-8">

                                <dt class="col-5 mt-2">Date :</dt>
                                <dd class="col-5 mt-2 under-line"><?php echo date("Y-m-d"); ?></dd>
                            </dl>
                        </div>
                    </div>
                    <span class="page-number">
                        <table class="p-5 mt-2" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Lot No.</th>
                                    <th class="text-center">Serial Number</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Net weight (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $aSum = array(0, 0, 0, 0);
                                $sql = "SELECT bs_packing_items.id,bs_packing_items.production_id,bs_packing_items.code,bs_packing_items.pack_name,
                    bs_packing_items.pack_type,bs_packing_items.weight_actual,bs_packing_items.weight_expected,bs_productions.round,bs_productions.id,bs_packing_items.status
                    FROM bs_packing_items 
                    LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                    WHERE bs_packing_items.status != '-1' AND bs_packing_items.production_id = '" . $production_id . "'
                    ORDER BY LENGTH( bs_packing_items.code ),bs_packing_items.code ASC";
                                $rst = $dbc->Query($sql);
                                $number = 1;
                                while ($product = $dbc->Fetch($rst)) {
                                    if (!empty($product['weight_in_safe']) || ($product['pack_type'] == 'ถุงรวมเศษ')) {
                                        $scrap = $dbc->GetRecord("bs_scrap_items", "*", "production_id=" . $production_id . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า'");
                                        if (isValidRecord($scrap)) {
                                            $scrap_parent = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap['id']);
                                            if (isValidRecord($scrap_parent)) {
                                                $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . $scrap_parent['production_id']);
                                                if (isValidRecord($get_product)) {
                                                    echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $number . '</td>';
                                                    echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $get_product['round'] . '-' . $product['round'] . '</td>';
                                                    echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $product['code'] . '</td>';
                                                    echo '<td bgcolor="lightblue" class="text-center font-weight-bold">ถุงปกติ</td>';
                                                    echo '<td bgcolor="lightblue" class="text-center font-weight-bold">' . $product['weight_actual'] . '</td>';
                                                }
                                            }
                                        }
                                    } else {
                                        echo '<tr>';
                                        echo '<td class="text-center">' . $number . '</td>';
                                        echo '<td class="text-center">' . $product['round'] . '</td>';
                                        echo '<td class="text-center">' . $product['code'] . '</td>';
                                        echo '<td class="text-center">' . $product['pack_type'] . '</td>';
                                        echo '<td class="text-center">' . $product['weight_actual'] . '</td>';
                                    }

                                    $number++;
                                    echo '</tr>';

                                    $aSum[0] += 1;
                                    $aSum[1] += $product['weight_actual'];
                                }
                                ?>

                            </tbody>
                        </table>
                        <div class="container-fluid mt-4">
                            <div class="row">
                                <div class="col-sm-8">
                                </div>
                                <div class="col-sm-4 small-text">
                                    <table class="p-5 mt-2" width="100%">
                                        <tr>
                                            <th>Total Weight</th>
                                            <th class="text-right"><?php echo number_format($aSum[1], 4); ?> </th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php
                }
                    ?>

                    <table class="p-5 mt-5 smaill-text" width="100%">
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <div>________________________</div>
                                    <div>Inspector</div>
                                </td>
                                <td class="text-center">
                                    <div>__________________________</div>
                                    <div>Finance</div>
                                </td>
                                <td class="text-center">
                                    <div>__________________________</div>
                                    <div> Approver</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
</div>

<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 5px;
    }

    .small-text {
        font-size: 11.5pt;
    }

    .big-text {
        font-size: 16pt;
    }

    .under-line {
        border-bottom: 1px solid #000;
    }

    .flower {
        border: 2px solid black;
    }


    @media print {

        .main-header,
        .sidebar,
        .breadcrumb,
        .btn-area {
            display: none;
        }

    }
</style>
<script>

</script>