<?php
$today = time();

$delivery_id = $dbc->GetRecord("bs_orders", "*", "delivery_id=" . $_GET['order_id']);
$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $delivery_id['customer_id']);

$coa = $dbc->GetRecord("bs_coa_run", "*", "order_id= '" . $delivery_id['code'] . "' AND created = '" . $delivery_id['delivery_date'] . "' AND customer_id=" . $customer['id']);

$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $_GET['order_id']);

if ($delivery_id['product_id'] == 1) {
    $product_name = "Silver Grains 99.99%";
    $type = "";
} else if ($delivery_id['product_id'] == 2) {
    $product_name = "Silver Bar 99.99%";
    $type = "LBMA";
} else if ($delivery_id['product_id'] == 3) {
    $product_name = "Silver Grains 99.99%";
    $type = "LBMA";
} else if ($delivery_id['product_id'] == 4) {
    $product_name = "Recycled Silver Grains 99.99%";
    $type = "RECYCLE";
} else if ($delivery_id['product_id'] == 5) {
    $product_name = "Silver Grains 99.99%";
    $type = "Mined";
} else if ($delivery_id['product_id'] == 7) {
    $product_name = "Recycled Silver Grains 99.99%";
    $type = "RECYCLE";
}

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
            <img class="pull-right" src="img/bowins_rjc_head.png" width="1005px" height="120px">
        </div>
    </div>
    <!-- <hr style="border:1px solid #000"> -->
    <div class="container docu-print mt-5">
        <h4 class="text-center">CERTIFICATE OF ANALYSIS / ORIGIN </h4>
        <table class="p-5 mt-5 flower" width="100%">
            <thead>
                <tr>
                    <th class="text-left">Date of Certification</th>
                    <th class="text-left">: <?php echo $delivery_id['delivery_date']; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Certificate No.</th>
                    <th class="text-left">: <?php echo isset($coa['number']) ? $coa['number'] : ""; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Customer</th>
                    <th class="text-left">: <?php echo $customer['org_name_coc']; ?></th>
                </tr>
                <tr <?php if (($customer['po'] == '0') || ($customer === NULL)) : ?> style="display: none;" <?php endif; ?>>
                    <th class="text-left">PO</th>
                    <th class="text-left">: <?php echo $customer['po']; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Product</th>
                    <th class="text-left">: <?php echo $product_name; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Total Weight</th>
                    <th class="text-left">: <?php echo number_format($delivery['amount'], 4); ?> Kg</th>
                </tr>
                <tr>
                    <th class="text-left">Lot No.</th>
                    <?php
                    echo '<th class="text-left">';
                    $sql = "SELECT DISTINCT (bs_productions.round) AS round , bs_productions.data, bs_productions.id,bs_packing_items.pack_type
                    FROM bs_delivery_pack_items 
                    LEFT OUTER JOIN bs_packing_items ON bs_delivery_pack_items.item_id = bs_packing_items.id
                    LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                    WHERE bs_delivery_pack_items.delivery_id  = '" . $_GET['order_id'] . "' ";
                    $rst = $dbc->Query($sql);
                    while ($production = $dbc->Fetch($rst)) {
                        if (!empty($production['weight_in_safe']) || ($production['pack_type'] == 'ถุงรวมเศษ')) {
                            $scrap = $dbc->GetRecord("bs_scrap_items", "*", "production_id=" . $production['id'] . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า'");
                            $scrap_parent = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap['id']);
                            $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . $scrap_parent['production_id']);
                            echo ': ' . $get_product['round'] . '-' . $production['round'] . '<br />';
                        } else {
                            echo ': ' . $production['round'] . '<br />';
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <th class="text-left">Reference No.</th>
                    <?php
                    echo '<th class="text-left">';
                    $sql = "SELECT DISTINCT (bs_productions.round) AS round , bs_productions.data , bs_productions.id,bs_packing_items.pack_type
                    FROM bs_delivery_pack_items 
                    LEFT OUTER JOIN bs_packing_items ON bs_delivery_pack_items.item_id = bs_packing_items.id
                    LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                    WHERE bs_delivery_pack_items.delivery_id  = '" . $_GET['order_id'] . "' ";
                    $rst = $dbc->Query($sql);
                    while ($production = $dbc->Fetch($rst)) {
                        if (!empty($production['weight_in_safe']) || ($production['pack_type'] == 'ถุงรวมเศษ')) {
                            $scrap = $dbc->GetRecord("bs_scrap_items", "*", "production_id=" . $production['id'] . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า'");
                            $scrap_parent = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap['id']);
                            $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . $scrap_parent['production_id']);
                            if (isset($production['data'])  || isset($get_product['data'])) {
                                if ($production['data'] != null) {
                                    $data = json_decode($production['data'], true);
                                    $dataim = json_decode($get_product['data'], true);
                                    foreach ($data['import'] as $import) {
                                        foreach ($dataim['import'] as $import2) {
                                            $sql = "SELECT  DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' OR brand LIKE '%" . $import2['import_number'] . "%' ";
                                            $rst_in = $dbc->Query($sql);

                                            while ($income = $dbc->Fetch($rst_in)) {
                                                foreach ($dataim['import'] as $import) {
                                                }

                                                echo ': ' . $income['coa'] . '<br />';
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if (isset($production['data'])) {
                                if ($production['data'] != null) {
                                    $data = json_decode($production['data'], true);
                                    foreach ($data['import'] as $import) {
                                        $sql = "SELECT  DISTINCT(coa) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%'";
                                        $rst_in = $dbc->Query($sql);

                                        while ($income = $dbc->Fetch($rst_in)) {
                                            echo ': ' . $income['coa'] . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <th class="text-left">COC Transfer Document Number(s)</th>
                    <th class="text-left">: <?php echo isset($coa['number_coc']) ? $coa['number_coc'] : ""; ?></th>
                </tr>
                <tr>
                    <th class="text-left">TYPE</th>
                    <th class="text-left">: <?php echo $type; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Country of Origin</th>
                    <?php
                    echo '<th class="text-left">';
                    $sql = "SELECT DISTINCT (bs_productions.round) AS round , bs_productions.data
                    FROM bs_delivery_pack_items 
                    LEFT OUTER JOIN bs_packing_items ON bs_delivery_pack_items.item_id = bs_packing_items.id
                    LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                    WHERE bs_delivery_pack_items.delivery_id  = '" . $_GET['order_id'] . "' ";
                    $rst = $dbc->Query($sql);
                    while ($production = $dbc->Fetch($rst)) {
                        if (isset($production['data']))
                            if ($production['data'] != null) {
                                $data = json_decode($production['data'], true);
                                foreach ($data['import'] as $import) {
                                    $sql = "SELECT DISTINCT(country) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                    $rst_in = $dbc->Query($sql);
                                    while ($income = $dbc->Fetch($rst_in)) {
                                        echo ': ' . $income['country'] . '<br />';
                                    }
                                }
                            }
                    }
                    ?>
                </tr>
                <tr <?php if ($customer['coa'] == '0') : ?> style="display: none;" <?php endif; ?>>
                    <th class="text-left">Brand</th>
                    <?php
                    $sql = "SELECT DISTINCT (bs_productions.round) AS round , bs_productions.data
                    FROM bs_delivery_pack_items 
                    LEFT OUTER JOIN bs_packing_items ON bs_delivery_pack_items.item_id = bs_packing_items.id
                    LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                    WHERE bs_delivery_pack_items.delivery_id  = '" . $_GET['order_id'] . "' ";
                    $rst = $dbc->Query($sql);
                    while ($production = $dbc->Fetch($rst)) {
                        if (isset($production['data']))
                            if ($production['data'] != null) {
                                $data = json_decode($production['data'], true);
                                foreach ($data['import'] as $import) {
                                    $sql = "SELECT DISTINCT(import_brand) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                    $rst_in = $dbc->Query($sql);
                                    while ($income = $dbc->Fetch($rst_in)) {
                                        echo '<th class="text-left">: ' . $income['import_brand'] . '</th>';
                                    }
                                }
                            }
                    }
                    ?>
                </tr>
            </thead>
        </table>

        <table class="p-5 mt-5 smaill-text" width="100%">
            <tbody>
                <tr>
                    <td class="text-center">
                    </td>
                    <td class="text-center font-weight-bold">
                        <div style="padding-bottom: 50px;">Authorized Signature</div>
                        <div style="padding-bottom: 40px;">________________________</div>
                        <div>( Miss Palita Lunsa )</div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <div class="d-flex align-items-center container">
            <div>
                <img class="pull-right" src="img/bowins-rjc-footer.png" width="1005px" height="115px">
            </div>
        </div>
    </div>
</div>
</div>


<style>
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
        border: 2px dashed black;
    }

    th,
    td {
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 10px;
        padding-right: 40px;
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