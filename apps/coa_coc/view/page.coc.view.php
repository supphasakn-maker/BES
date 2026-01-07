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


if ($customer['default_sales'] == 13) {

    $sale = 'Miss. Kanya Naiyanate';
} else if ($customer['default_sales'] == 38) {

    $sale = 'Miss. Nittiya Srikong';
} else if ($customer['default_sales'] == 75) {

    $sale = 'Miss. Phakhawn Tongkaourma';
}
?>
<div class="btn-area btn-group mb-2">
    <button type="button" onclick="window.history.back();" class="btn btn-dark">Back</button>
    <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
        <i class="mr-2" data-feather="printer"></i>Print
    </button>
</div>

<div class="card">
    <div class="d-flex align-items-center container">
        <div>
            <img class="pull-right" src="img/bowins_rjc_head.png" width="1010px" height="110px">
        </div>
    </div>
    <!-- <hr style="border:1px solid #000"> -->
    <div class="container docu-print">
        <h5 class="text-center">RJC Chain-of-Custody (CoC) Material Transfer Document</h5>
        <div class="row">
            <div class="col-2 small-text">
            </div>
            <div class="col-10 text-coc-small mt-1">
                <dl class="row col-10 offset-5">
                    <dt class="col-6 mt-1">Date :</dt>
                    <dd class="col-5 mt-1"><?php echo $delivery_id['delivery_date']; ?></dd>
                    <dt class="col-6 mt-1">Chain of Custody Document Number:</dt>
                    <dd class="col-5 mt-1"><?php echo isset($coa['number_coc']) ? $coa['number_coc'] : ""; ?></dd>
                </dl>
            </div>
        </div>
        <table class="p-5 flower text-coc-small" width="100%">
            <thead>
                <tr>
                    <th class="text-left">Issue : <?php echo $sale; ?></th>
                    <th class="text-left" colspan="3">Receiver : <?php echo $customer['contact_coc']; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Name of company : Bowins Silver Co.,Ltd.</th>
                    <th class="text-left" colspan="3">Name of company : <?php echo $customer['org_name_coc']; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Address : 74/1 Sukphapiban 2 Road,Soi 31 Dokmai Praves, BKK Thailand</th>
                    <th class="text-left" colspan="3">Address : <?php echo $customer['address_coc']; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Chain of Custody Certification number : C0000 5039</th>
                    <th class="text-left" colspan="3">Chain of Custody Certification number : <?php echo $customer['certificate_number']; ?></th>
                </tr>
                <tr>
                    <th class="text-left">Certification start and end dates : December6, 2023 - December6, 2026</th>
                    <th class="text-left" colspan="3">Certification start and end dates : <?php echo $customer['certificate_coc']; ?></th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">Responsible person : <?php echo $sale; ?></th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">The information provided i this CoC transfer document is in conformance with the RJC CoC Standard.</th>
                </tr>
                <tr>
                    <th class="text-center font-weight-bold bb" bgcolor="#AAB7B8" colspan="4">
                        <h5 class="small-text">CoC material</h5>
                    </th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">Total weight : <?php echo number_format($delivery['amount'], 4); ?> Kg</th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">Sales Invoice Number : <?php echo $delivery['billing_id']; ?> </th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">Description : <?php echo $product_name; ?> </th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">Number of items (if applicable)
                        <?php
                        $sql = "SELECT bs_delivery_pack_items.delivery_id, bs_delivery_pack_items.item_id ,bs_packing_items.pack_name,bs_packing_items.weight_expected , COUNT(bs_delivery_pack_items.item_id) AS aa FROM bs_delivery_pack_items 
                        LEFT OUTER JOIN bs_packing_items ON bs_delivery_pack_items.item_id = bs_packing_items.id
                        WHERE bs_delivery_pack_items.delivery_id = '" . $_GET['order_id'] . "'
                        GROUP BY bs_packing_items.weight_expected  ";

                        $rst = $dbc->Query($sql);
                        while ($delivery_item = $dbc->Fetch($rst)) {
                        ?>
                            :<?php echo number_format($delivery_item['weight_expected'], 4); ?> KG * <?php echo number_format($delivery_item['aa'], 0); ?> bags <br />
                        <?php
                        }
                        ?>
                    </th>
                </tr>
                <tr>
                    <th class="text-left" colspan="4">Lot No. / Serial Number (if applicable)</th>
                </tr>
            </thead>
            <thead>
                <th class="text-center" style="width:35%">No.</th>
                <th class="text-center">Lot No.</th>
                <th class="text-center">Serial Number / Bags</th>
                <th class="text-center">Weight Expected</th>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT bs_packing_items.code , bs_productions.id , bs_productions.round ,bs_packing_items.weight_expected ,bs_packing_items.pack_type
                FROM bs_delivery_pack_items 
                LEFT OUTER JOIN bs_packing_items ON bs_delivery_pack_items.item_id = bs_packing_items.id
                LEFT OUTER JOIN bs_productions ON bs_packing_items.production_id = bs_productions.id
                WHERE bs_delivery_pack_items.delivery_id  = '" . $_GET['order_id'] . "' ORDER BY bs_packing_items.code ASC";
                $rst = $dbc->Query($sql);
                $number = 1;
                while ($production = $dbc->Fetch($rst)) {
                    if (!empty($production['weight_in_safe']) || ($production['pack_type'] == 'ถุงรวมเศษ')) {
                        $scrap = $dbc->GetRecord("bs_scrap_items", "*", "production_id=" . $production['id'] . " AND pack_name = 'เศษเสียรอการผลิตนำเข้า'");
                        $scrap_parent = $dbc->GetRecord("bs_scrap_items", "*", "parent=" . $scrap['id']);
                        $get_product = $dbc->GetRecord("bs_productions", "*", "id=" . $scrap_parent['production_id']);
                        echo '</tr>';
                        echo '<td class="text-center">' . $number . '</td>';
                        echo '<td class="text-center">' . $get_product['round'] . '-' . $production['round'] . '</td>';
                        echo '<td class="text-center">' . $production['code'] . '</td>';
                        echo '<td class="text-center">' . $production['weight_expected'] . '</td>';
                    } else {
                        echo '<tr>';
                        echo '<td class="text-center">' . $number . '</td>';
                        echo '<td class="text-center">' . $production['round'] . '</td>';
                        echo '<td class="text-center">' . $production['code'] . '</td>';
                        echo '<td class="text-center">' . $production['weight_expected'] . '</td>';
                    }
                    $number++;
                    echo '</tr>';
                }
                ?>
            </tbody>
            <thead>
                <tr>
                    <th class="text-left font-weight-bold bb" bgcolor="#AAB7B8" colspan="4">
                        <h5 class="small-text">Type of transfer (check one)</h5>
                    </th>
                </tr>
                <tr>
                    <th class="text-center"> <input <?php if ($delivery_id['product_id'] == '1') echo "checked"; ?> type="checkbox" readonly="readonly"></th>
                    <th class="text-left" colspan="3">Eligible material declaration intiating the CoC</th>
                </tr>
                <tr>
                    <th class="text-center"><input <?php if ($delivery_id['product_id'] == '3') echo "checked"; ?> type="checkbox" readonly="readonly"></th>
                    <th class="text-left" colspan="3">Eligible material declaration intiating the CoC for mined material</th>
                </tr>
                <tr>
                    <th class="text-center"><input <?php if ($delivery_id['product_id'] == '4' || $delivery_id['product_id'] == '5') echo "checked"; ?> type="checkbox" readonly="readonly"></th>
                    <th class="text-left" colspan="3">Subsequent CoC transfer, single type CoC material</th>
                </tr>
                <tr>
                    <th class="text-center"><input type="checkbox" readonly="readonly"></th>
                    <th class="text-left" colspan="3">Subsequent CoC transfer, Jewellery products containing more than one type of Coc mater</th>
                </tr>
            </thead>
        </table>
        <table class="p-5 flower text-coc-small" width="100%">
            <thead>
                <tr>
                    <th class="text-left font-weight-bold bb" bgcolor="#AAB7B8" colspan="6">
                        <h5 class="small-text">Type of material contained in transfer (check all that apply)</h5>
                    </th>
                </tr>
                <tr>
                    <th class="text-left">Silver</th>
                    <th class="text-left">Gold</th>
                    <th class="text-left">Platinum</th>
                    <th class="text-left">Palladium</th>
                    <th class="text-left">Rhodium</th>
                    <th class="text-left">Type of Material</th>
                </tr>
                <tr>
                    <th class="text-center"><input <?php if ($delivery_id['product_id'] == '5') echo "checked"; ?> type="checkbox" readonly="readonly"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left">Mined</th>
                </tr>
                <tr>
                    <th class="text-center"><input <?php if ($delivery_id['product_id'] == '3') echo "checked"; ?> type="checkbox" readonly="readonly"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left">Mined by-product</th>
                </tr>
                <tr>
                    <th class="text-center"><input <?php if ($delivery_id['product_id'] == '4') echo "checked"; ?> type="checkbox" readonly="readonly"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left">Recycled</th>
                </tr>
                <tr>
                    <th class="text-center"><input type="checkbox" readonly="readonly"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left">Grandfathered</th>
                </tr>
                <tr>
                    <th class="text-center"><input type="checkbox" readonly="readonly"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left"></th>
                    <th class="text-left">Mix of Mined,Recylces and/or Grandfather</th>
                </tr>
            </thead>
            <thead>

                <?php
                echo '<th class="text-left font-weight-bold" colspan="5">';
                echo 'Supplementaty information (include at issues discretion)<br>';
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
                </th>
                <?php
                echo '<th class="text-left font-weight-bold" colspan="5">';
                ?>
                Material's previous CoC transfer document number(s)(optional)<br>
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
                                $sql = "SELECT DISTINCT(coc) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                                $rst_in = $dbc->Query($sql);
                                while ($income = $dbc->Fetch($rst_in)) {
                                    echo '' . $income['coc'] . '<br />';
                                }
                            }
                        }
                }

                ?>
                </th>
            </thead>
            <thead>
                <th class="text-left font-weight-bold" colspan="6">Description of any non-CoC material that is part of jewellery products containing CoC material (if applicable)</th>
            </thead>
        </table>
        <div class="mt-2 mb-5 mr-5 ml-4 ">
            <p class="small-text font-weight-bold">
                A RJC Chain-of-Custody Material Transfer Document need to be attached to each shipment of CoC Material
            </p>
        </div>
        <div class="d-flex align-items-center container">
            <div>
                <img class="pull-right" src="img/bowins-rjc-footer.png" width="1005px" height="105px">
            </div>
        </div>
    </div>
</div>
</div>


<style>
    .small-text {
        font-size: 11.5pt;
    }

    .text-coc-small {
        font-size: 9.5pt;
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

    th,
    td {
        padding-top: 3px;
        padding-bottom: 3px;
        padding-left: 10px;
        padding-right: 30px;
        border: 1px solid black;
        border-collapse: collapse;
    }

    @media print {

        .main-header,
        .sidebar,
        .breadcrumb,
        .btn-area {
            display: none;
        }

        .bb {
            background-color: #AAB7B8;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
<script>

</script>