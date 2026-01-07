<div class="btn-area btn-group mb-2">
    <button type="button" class="btn btn-dark" onclick='window.history.back()'>Back</button>
    <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
        <i class="mr-2" data-feather="printer"></i>Print
    </button>
</div>
<h5 class="text-center">รายงาน เม็ดเกิน</h5>
<br>
<table class="table table-sm table-bordered table-striped" id="tblDeposit">
    <thead>

        <tr>
            <th class="text-center font-weight-bold">วันที่</th>
            <th class="text-center font-weight-bold">เลขที่เอกสาร</th>
            <th class="text-center font-weight-bold">ประเภท</th>
            <th class="text-center font-weight-bold">รับเข้า</th>
            <th class="text-center font-weight-bold">เบิก</th>
            <th class="text-center font-weight-bold">คงเหลือ</th>
            <th class="text-center font-weight-bold">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0, 0, 0, 0);
        $aSums = array(0, 0, 0, 0);
        $purchase = array(0, 0, 0);
        $margin_amount = 0;
        $margin_amount1 = 0;
        $sql = "SELECT bs_stock_adjusted_over.id,bs_stock_adjusted_over.product_id ,bs_products.name,bs_stock_adjusted_over.remark,
        bs_stock_adjusted_over.code_no,bs_stock_adjusted_over.amount,bs_stock_adjusted_over.date,bs_stock_adjusted_over.type_id FROM bs_stock_adjusted_over 
        LEFT OUTER JOIN bs_products ON bs_stock_adjusted_over.product_id = bs_products.id";
        $rst = $dbc->Query($sql);
        while ($production = $dbc->Fetch($rst)) {
            $sum1 = $dbc->GetRecord("bs_stock_adjusted_over", "SUM(amount) AS amount", "type_id = 1 AND id = '" . $production['id'] . "'");
            $sum2 = $dbc->GetRecord("bs_stock_adjusted_over", "SUM(amount) AS amount", "type_id = 2 AND id = '" . $production['id'] . "'");
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 1 ";
            $rst_in = $dbc->Query($sql);
            while ($product = $dbc->Fetch($rst_in)) {
                $aSum[0] += 1;
                $aSum[1] += $product['amount'];
                $margin_amount += $product['amount'];
            }
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 2 ";
            $rst_in = $dbc->Query($sql);
            while ($products = $dbc->Fetch($rst_in)) {
                $purchase[0] += 1;
                $purchase[1] += $products['amount'];
                $margin_amount1 += $products['amount'];
            }
            echo '<tr>';
            echo '<td class="text-center">' . $production['date'] . '</td>';
            echo '<td class="text-center">' . $production['code_no'] . '</td>';
            echo '<td class="text-center">' . $production['name'] . '</td>';
            echo '<td class="text-center">';
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 1 ";
            $rst_in = $dbc->Query($sql);
            while ($import = $dbc->Fetch($rst_in)) {
                echo $import['amount'] . '<br />';
            }
            echo '<td class="text-center">';
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 2 ";
            $rst_in = $dbc->Query($sql);
            while ($import2 = $dbc->Fetch($rst_in)) {
                echo $import2['amount'] . '<br />';
            }
            echo '<td class="text-center">' .  number_format($margin_amount - $margin_amount1, 4) . '</td>';
            echo '<td class="text-left">' . $production['remark'] . '</td>';
            echo '<td>';
            echo '</tr>';
            $aSums[0] += 1;
            $aSums[1] += $sum1["amount"];
            $aSums[2] += $sum2["amount"];
            $aSums[3] += $margin_amount - $margin_amount1;
        }

        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="3">รวมทั้งหมด <?php echo $aSums[0]; ?> รายการ</th>
            <th class="text-center"><?php echo number_format($aSums[1], 4); ?></th>
            <th class="text-center"><?php echo number_format($aSums[2], 4); ?> </th>
            <th class="text-center"><?php echo number_format($aSums[3], 4); ?></th>
            <th class="text-center"></th>
        </tr>
    </tfoot>
</table>
<style>
    .big-text {
        font-size: 16pt;
    }

    .small-text {
        font-size: 8pt;
    }

    .under-line {
        border-bottom: 1px solid #000;
    }

    @media print {

        .main-header,
        .left-sidebar,
        .sidebar,
        .breadcrumb,
        .btn-area {
            display: none !important;
        }

        body {
            font-family: sans-serif;
        }

        html,
        body,
        div,
        span,
        applet,
        object,
        iframe,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        a,
        abbr,
        acronym,
        address,
        big,
        cite,
        code,
        del,
        dfn,
        em,
        font,
        ins,
        kbd,
        q,
        s,
        samp,
        small,
        strike,
        strong,
        sub,
        sup,
        tt,
        var,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td {
            font-size: 6.35pt !important;
        }

        h1 {
            font-size: 10pt !important;
        }

        h3 {
            font-size: 10pt !important;
        }


        @page {
            size: A4 vertical;
            margin-top: 0.5cm;
            margin-bottom: 0.5cm;
            margin-left: 0.3cm;
            margin-right: 0.3cm
        }

        header,
        footer,
        aside,
        nav,
        form,
        iframe,
        .ad {
            display: none !important;
        }
    }
</style>