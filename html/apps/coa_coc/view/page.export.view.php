<?php
$today = time();

$delivery_id = $dbc->GetRecord("bs_orders", "*", "delivery_id=" . $_GET['order_id']);
$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $delivery_id['customer_id']);
$coa = $dbc->GetRecord("bs_coa_run", "*", "order_id= '" . $delivery_id['code'] . "' AND created = '" . $delivery_id['delivery_date'] . "' AND customer_id=" . $customer['id']);

$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $_GET['order_id']);

if ($customer['po'] == '-') {
    $aa = "-";
} else {
    $aa = DateThaiFullNotime($customer['date_po']);
}

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
}

if ($customer['default_sales'] == 13) {

    $sale = 'Miss. Kanya Naiyanate';
} else if ($customer['default_sales'] == 38) {

    $sale = 'Miss. Nittiya Srikong';
} else if ($customer['default_sales'] == 75) {

    $sale = 'Miss. Phakhawn Tongkaourma';
}

if ($customer['export'] == 'อัญธานี') {
    $title = 'เรียน เจ้าหน้าที่ศุลกากรกำกับเขตประกอบการเสรี (นิคมอุตสาหกรรมอัญธานี)';
    $title2 = '5 05 01 11';
} else {
    $title = 'เรียน หัวหน้าฝ่ายบริการเขตประกอบการเสรี (นิคมอุตสาหกรรมลาดกระบัง)';
    $title2 = '6 03 01 03';
}

function DateThaiFullNotime($strDate)
{
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}

function Convert($amount_number)
{
    $amount_number = number_format($amount_number, 2, ".", "");
    $pt = strpos($amount_number, ".");
    $number = $fraction = "";
    if ($pt === false)
        $number = $amount_number;
    else {
        $number = substr($amount_number, 0, $pt);
        $fraction = substr($amount_number, $pt + 1);
    }

    $ret = "";
    $baht = ReadNumber($number);
    if ($baht != "")
        $ret .= $baht . "บาท";

    $satang = ReadNumber($fraction);
    if ($satang != "")
        $ret .=  $satang . "สตางค์";
    else
        $ret .= "ถ้วน";
    return $ret;
}

function ReadNumber($number)
{
    $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
    $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
    $number = $number + 0;
    $ret = "";
    if ($number == 0) return $ret;
    if ($number > 1000000) {
        $ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
        $number = intval(fmod($number, 1000000));
    }

    $divider = 100000;
    $pos = 0;
    while ($number > 0) {
        $d = intval($number / $divider);
        $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : ((($divider == 10) && ($d == 1)) ? "" : ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
        $ret .= ($d ? $position_call[$pos] : "");
        $number = $number % $divider;
        $divider = $divider / 10;
        $pos++;
    }
    return $ret;
}


?>
<div class="btn-area btn-group mb-2">
    <button type="button" onclick="window.history.back();" class="btn btn-dark">Back</button>
    <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
        <i class="mr-2" data-feather="printer"></i>Print
    </button>
</div>

<div class="card">
    <div class="container docu-print">
        <p class="text-right mt-3">กศก.122</p>
        <h5 class="text-center">คำร้องขอนำสินค้าในราชอาณาจักรเข้าไปในเขตประกอบการเสรี</h5>
        <p class="text-center">(แบบแนบประมวลข้อ <?php echo $title2; ?> )</p>
        <div class="row">
            <div class="col-6 small-text">
            </div>
            <div class="col-6 text-coc-small mt-1">
                <dl class="row col-8 offset-5">
                    <dt class="col-6 mt-1">เลขที่</dt>
                    <dd class="col-5 mt-1"></dd>
                    <dt class="col-5 mt-1">วันที่ :</dt>
                    <dd class="col-6 mt-1"><?php echo DateThaiFullNotime($delivery_id['delivery_date']); ?></dd>

                </dl>
            </div>

        </div>
        <p class="text-left">เรื่อง ขอนำสินค้าในราชอาณาจักรเข้าเขตประกอบการเสรี (นิคมอุตสาหกรรม<?php echo $customer['export']; ?>)</p>
        <p class="text-left"><?php echo $title; ?></p>
        <p class="text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เนื่องด้วยข้าพเจ้า <u>บริษัท โบวินส์ ซิลเวอร์ จำกัด </u></p>
        <p class="text-left">เลขประจำตัวผู้เสียภาษีอากร <u>0105555082745</u>.ที่ตั้งบริษัท เลขที่<u> 74/1 ซอยสุขาภิบาล 2 ซอย 31</u></p>
        <p class="text-left">แขวงดอกไม้ เขต เขตประเวศ จังหวัด กรุงเทพฯ รหัสไปรษณีย์ 10250 โทร.02-7203000 </p>
        <p class="text-left mt-3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            มีความประสงค์จะนำผลิตภัณฑ์ภายในประเทศ เข้าเขตประกอบการเสรี เพื่อใช้ในการผลิต ผสม ประกอบ
        </p>
        <p class="text-left">เป็นสินค้าเพื่อส่งออกหรือใช้ในการประกอบติดตั้งในสำนักงานหรือโรงงานของ <u><?php echo $customer['org_name']; ?></u></p>
        <p class="text-left">ซึ่งตั้งอยู่ในเขตประกอบการเสรี นิคมอุตสาหกรรม <u><?php echo $customer['export']; ?></u>.ตามใบสั่งซื้อเลขที่ <u><?php echo $customer['po']; ?></u></p>
        <p class="text-left">ลงวันที่ <u><?php echo $aa; ?></u> ดังรายละเอียดดังนี้</p>
        <table class="table table-sm table-bordered table-striped" id="tblDeposit">
            <thead>
                <tr>
                    <th class="text-center font-weight-bold"></th>
                    <th class="text-center font-weight-bold">จำนวนหีบห่อ (ถุง)</th>
                    <th class="text-center font-weight-bold">น้ำหนักสุทธิ (กิโลกรัม)</th>
                    <th class="text-center font-weight-bold">ปริมาณ (กิโลกรัม)</th>
                    <th class="text-center font-weight-bold">ราคาของ (บาท)</th>
                    <th class="text-center font-weight-bold">ชนิดของ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $aSum = array(0, 0, 0);
                $sql = "SELECT * FROM bs_delivery_items WHERE delivery_id=" . $delivery['id'];
                $rst = $dbc->Query($sql);
                $number = 1;
                while ($production = $dbc->Fetch($rst)) {

                    echo '</tr>';
                    // echo '<td class="text-center">' . $number . '</td>';
                    // echo '<td class="text-center">' . $production['amount'] . '</td>';
                    // echo '<td class="text-center">' . $production['amount'] * $production['size'] . '</td>';
                    // echo '<td class="text-center">' . $production['amount'] * $production['size'] . '</td>';
                    // echo '<td class="text-center">' . number_format($delivery_id['net'], 2) . '</td>';
                    // echo '<td class="text-center">' . $product_name . '</td>';
                    $number++;
                    echo '</tr>';
                    $aSum[0] += 1;
                    $aSum[1] += $production['amount'];
                    $aSum[2] += $production['amount'] * $production['size'];
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center">รวมทั้งหมด </th>
                    <th class="text-center"> <?php echo  $aSum[1]; ?> </th>
                    <th class="text-center"><?php echo $aSum[2]; ?> กิโลกรัม</th>
                    <th class="text-center"> <?php echo $aSum[2]; ?> กิโลกรัม</th>
                    <th class="text-center"> <?php echo number_format($delivery_id['net'], 2); ?> </th>
                    <th class="text-left"><?php echo $product_name; ?><br>
                </tr>
                <tr>
                    <th class="text-center" colspan="2"> จำนวนเงินเป็นตัวอักษร: </th>
                    <th class="text-center" colspan="3"><?php echo Convert($delivery_id['net']); ?></th>
                    <th class="text-left">รายละเอียดตามบัญชีราคาสินค้า/ ใบกำกับภาษี <?php echo $delivery['billing_id']; ?></th>
                    </th>
                </tr>
            </tfoot>
        </table>
        <div class="mt-2 mb-5 mr-5 ml-4 ">
            <p class="small-text font-weight-bold text-center">
                จึงเรียนมาเพื่อโปรดพิจารณา
            </p>
            <br>
            <p class="text-center">(ลงชื่อ)............................................</p>
            <p class="text-center">( <?php echo $customer['signature']; ?> )</p>
            <p class="text-center">ผู้จัดการแผนก / ตัวแทนบริษัท</p>
            <br>
            <p class="text-left">หมายเลขทะเบียนรถ .................................</p>
        </div>
        <table style="width:100%">
            <tr>
                <th class="text-center">บันทึกการอนุญาตของเจ้าหน้าที่</th>
                <td class="text-center">บันทึกการตรวจของเจ้าหน้าที่</td>
            </tr>
            <tr>
                <th rowspan="6">ส่งงานตรวจสอบและควบคุมสินค้าตรวจสอบ ฯ<br><br><br><br></th>
                <th rowspan="6">ตรวจสอบจำนวนพบถูกต้องพอใจ<br><br><br><br></th>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
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