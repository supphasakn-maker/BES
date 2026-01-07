<?php
$today = time();
$order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_GET['order_id']);
$delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $_GET['order_id']);

$product = $dbc->GetRecord("bs_products_bwd", "*", "id=" . $order['product_id']);
$product_name = $product['name'];

$shipping = $dbc->GetRecord("bs_shipping_bwd", "*", "id=" . $order['shipping']);
$shipping_name = $shipping['name'];

$product_type = $dbc->GetRecord("bs_products_type", "*", "id=" . $order['product_type']);
$product_type_name = $product_type['name'];


if ($delivery['payment_note'] == null) {
    $payment_note = array(
        "bank" => $delivery['default_bank'],
        "payment" => $delivery['default_payment'],
        "remark" => ""
    );
} else {
    $payment_note = json_decode($delivery['payment_note'], true);
}

$signature = "";
if ($dbc->HasRecord("os_users", "id=" . $order['sales'])) {
    $employee = $dbc->GetRecord("os_users", "*", "id=" . $order['sales']);
    $sales = $employee['display'];
    $signature = $employee['name'];
} else {
    $sales = "-";
}

if ($order['product_id'] == 1) {

    $img = ' <img class="pull-right flower" src="img/design-15.png" width="220px" height="220px">';
} else if ($order['product_id'] == 2) {

    $img = ' <img class="pull-right flower" src="img/design-50.png" width="220px" height="220px">';
} else if ($order['product_id'] == 3) {
    $img = ' <img class="pull-right flower" src="img/design-150.png" width="220px" height="220px">';
} else {
    $img = "";
}
?>
<div class="btn-area btn-group mb-2">
    <button type="button" class="btn btn-dark" onclick='window.history.back()'>Back</button>
    <button class="btn btn-light has-icon mt-1 mt-sm-0" type="button" onclick="window.print()">
        <i class="mr-2" data-feather="printer"></i>Print
    </button>
</div>

<div class="card">
    <div class="card-body mr-4 ml-4"></div>
    <div class="d-flex align-items-center container">
        <div>
            <img class="pull-right" src="img/logo-Bowins-design.png" width="120px" height="120px">
        </div>
    </div>
    <div style="display: flex; justify-content: flex-end" class="container">
        <div>FM-SM-006</div>
    </div>

    <div class="container docu-print mt-2">
        <h4 class="text-center">ใบยืนยันการสั่งซื้อ / CONFIRM ORDER</h4>
        <br>
        <div class="row small-text">
            <div class="col-sm-7">
                <dl class="row col-8">
                    <dt class="col-5">Platform :</dt>
                    <dd class="col-6 under-line"><?php echo $order['platform']; ?></dd>
                    <dt class="col-4">ชื่อลูกค้า : </dt>
                    <dd class="col-7 under-line"><?php echo $order['customer_name']; ?></dd>
                    <dt class="col-5">โทร : </dt>
                    <dd class="col-6 under-line"><?php echo $order['phone']; ?></dd>
                </dl>
            </div>
            <div class="col-sm-5">
                <dl class="row col-8 offset-8">
                    <dt class="col-4">วันที่ :</dt>
                    <dd class="col-7 under-line "><?php echo date("d/m/Y", strtotime($order['date'])); ?></dd>
                    <dt class="col-4">No :</dt>
                    <dd class="col-8 under-line"><?php echo $order['code']; ?></dd>
                </dl>
            </div>
        </div>
        <div class="row small-text">
            <div class="col-sm-8">
                <dl class="row col-8 ">
                    <dt class="col-5">ผู้ขาย :</dt>
                    <dd class="col-6 under-line" style="font-family: cursive;"><?php echo $signature; ?></dd>
                    <dt class="col-5">การชำระเงิน :</dt>
                    <dd class="col-6 under-line"><?php echo $payment_note['bank']; ?></dd>
                    <dt class="col-6">เงื่อนไขการชำระเงิน : </dt>
                    <dd class="col-5 under-line"><?php echo $payment_note['payment']; ?></dd>
                    <dt class="col-4">หมายเหตุ : </dt>
                    <dd class="col-7 under-line"><?php echo $payment_note['remark']; ?></dd>
                    <dt class="col-6">ตรวจสอบเงินเข้าบัญชี </dt>
                    <dd class="col-5 ">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ส่งสินค้าได้
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            รอเงินเข้าร้านค้า E-Commerce
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            เงินไม่เข้า (ไม่ส่งสินค้า)
                        </label>
                    </dd>
                    <dt class="col-6">สินค้าเบิกการตลาด </dt>
                    <dd class="col-5 ">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ยืมสินค้า
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            คืนสินค้า
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ของขวัญเพื่อใช้ในการโฆษณา
                        </label>
                    </dd>
                </dl>
            </div>
            <div class="col-sm-4">
                <dl class="row col-8 offset-6 mt-5">
                    <?php echo $img; ?>
                </dl>
            </div>
        </div>
        <div class="row small-text flower">
            <div class="col-12 col-sm-12 col-md-12">
                <dl class="row col-10  mt-2">
                    <dt class="col-3">ที่อยู่ออกอินวอยซ์ : </dt>
                    <dd class="col-8 under-line"><?php echo $order['billing_address']; ?></dd>
                    <dt class="col-3">ที่อยู่จัดส่ง : </dt>
                    <dd class="col-8 under-line"><?php echo $order['shipping_address']; ?></dd>
                    <dt class="col-3">ขนาดแท่ง : </dt>
                    <dd class="col-8 under-line"><?php echo $product_name; ?></dd>
                    <dt class="col-3">จำนวนแท่ง : </dt>
                    <dd class="col-8 under-line"><?php echo $order['amount']; ?></dd>
                    <dt class="col-3">ลาย : </dt>
                    <dd class="col-8 under-line mt-1"><?php echo $product_type_name; ?></dd>
                    <dt class="col-3 mt-2">Collection : </dt>
                    <dd class="col-8 under-line mt-2"></dd>
                    <dt class="col-3 mt-2">การสลักข้อความ : </dt>
                    <dd class="col-8 under-line mt-2"><?php echo $order['engrave']; ?></dd>
                    <dt class="col-3 mt-2">Font : </dt>
                    <dd class="col-8 under-line mt-2"><?php echo $order['font']; ?></dd>
                    <dt class="col-3 mt-2">Text : </dt>
                    <dd class="col-8 under-line mt-2"><?php echo $order['carving']; ?></dd>
                </dl>
            </div>
        </div>
        <div class="row small-text flower">
            <div class="col-6 col-sm-6 col-md-6">
                <dl class="row col-10  mt-2">
                    <dt class="col-12">อื่นๆภายในบรรจุภัณฑ์/พัสดุ</dt>
                    <dd class="col-5 ">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            แท่งเปล่า
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ซองกันกระแทก
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ซองพลาสติก
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            Certificate Card
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            Care Card
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            About Artist Card
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ผ้าเช็ดแท่งเงิน
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ถุงใหญ่
                        </label>
                        <br>
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label" for="defaultCheck1">
                            ถุงเล็ก
                        </label>
                    </dd>
                </dl>
            </div>
            <div class="col-6 col-sm-6 col-md-6 ">
                <dl class="row col-10  mt-2">
                    <dt class="col-12">รายละเอียดในการขาย</dt>
                    <dd class="col-10 ">
                        <input class="form-check-input" <?php if ($order['ai'] == '1') echo "checked"; ?> type="checkbox">
                        <label class="form-check-label font-weight-bold" for="defaultCheck1">
                            ภาษาอื่นๆ , ภาพมีรายละเอียดสูง , มีไฟล์ AI คิดเพิ่ม 200.-
                        </label>
                    <dt class="col-3">การจัดส่ง : </dt>
                    <dd class="col-8 text-right under-line"><?php echo $shipping_name; ?></dd>
                    <dt class="col-5">ราคาต่อแท่ง :</dt>
                    <dd class="col-6 text-right under-line"><?php echo number_format($order['price'], 2, ".", ","); ?> บาท</dd>
                    <dt class="col-5">ส่วนลด :</dt>
                    <dd class="col-6 text-right under-line"><?php echo number_format($order['discount'], 2, ".", ","); ?> บาท</dd>
                    <dt class="col-5">ค่าจัดส่ง :</dt>
                    <dd class="col-6 text-right under-line"><?php echo number_format($shipping['price'], 2, ".", ","); ?> บาท</dd>
                    <dt class="col-5">ราคารวม :</dt>
                    <dd class="col-6 text-right under-line"><?php echo number_format($order['total'], 2, ".", ","); ?> บาท</dd>
                    <dt class="col-5">รวมเงิน :</dt>
                    <dd class="col-6 text-right under-line"><?php echo number_format($order['net'], 2, ".", ","); ?> บาท</dd>
                    <br>

                    </dd>
                </dl>
            </div>
        </div>

        <table class="p-5 mt-5 smaill-text" width="100%">
            <tbody>
                <tr>
                    <td class="text-center">
                        <div>________________________</div>
                        <div>พนักงานขาย</div>
                        <div>วันที่____________</div>
                    </td>
                    <td class="text-center">
                        <div>__________________________</div>
                        <div>พนักงานการเงิน</div>
                        <div>วันที่____________</div>
                    </td>
                    <td class="text-center">
                        <div>__________________________</div>
                        <div>พนักงานปล่อยสินค้า</div>
                        <div>วันที่</div>
                    </td>
                    <td class="text-center">
                        <div>__________________________</div>
                        <div>พนักงานเลเซอร์</div>
                        <div>วันที่ ____________</div>
                    </td>
                    <td class="text-center">
                        <div>__________________________</div>
                        <div>พนักงานแพ็คของ</div>
                        <div>วันที่____________</div>
                    </td>
                    <td class="text-center">
                        <div>__________________________</div>
                        <div>พนักงานส่ง</div>
                        <div>วันที่____________</div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <div class="d-flex align-items-center container">
            <div>
                <img class="pull-right" src="img/bowins-footer.jpg" width="800px" height="98px">
            </div>
        </div>
    </div>
</div>
</div>
<style>
    .small-text {
        font-size: 11.2pt;
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