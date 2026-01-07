<?php
$today = time();
$order = $dbc->GetRecord("bs_orders_back_bwd", "*", "id=" . $_GET['order_id']);

$product = $dbc->GetRecord("bs_products_bwd", "*", "id=" . $order['product_id']);
$product_name = $product['name'];

$product_type = $dbc->GetRecord("bs_products_type", "*", "id=" . $order['product_type']);
$product_type_name = $product_type['name'];


$signature = "";
if ($dbc->HasRecord("os_users", "id=" . $order['sales'])) {
    $employee = $dbc->GetRecord("os_users", "*", "id=" . $order['sales']);
    $sales = $employee['display'];
    $signature = $employee['name'];
} else {
    $sales = "-";
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
            <img class="pull-right" src="img/bowins_rjc_head.png" width="1005px" height="120px">
        </div>
    </div>

    <!-- <hr style="border:1px solid #000"> -->
    <div class="container docu-print mt-1">
        <h2 class="text-center">ใบยืนยันการสั่งขายแท่งเงินดีไซน์ / Sell Confirmation</h2>
        <br>
        <div class="big-text mt-3">
            <dl class="row col-5 offset-8">
                <dt class="col-3">วันที่ :</dt>
                <dd class="col-8 under-line "><?php echo date("d/m/Y", strtotime($order['date'])); ?></dd>
                <dt class="col-3">No :</dt>
                <dd class="col-8 under-line"><?php echo $order['code']; ?></dd>
            </dl>
        </div>
        <div class="big-text">
            <dl class="row col-8">
                <dt class="col-5">ชื่อลูกค้า :</dt>
                <dd class="col-5 under-line"><?php echo $order['customer_name']; ?></dd>

                <dt class="col-5"><?php echo $product_name; ?> : </dt>
                <dd class="col-5 under-line"><?php echo number_format($order['amount'], 0); ?> <span class="ml-2">แท่ง</span></dd>

                <dt class="col-5">ประเภทสินค้า : </dt>
                <dd class="col-5 under-line"><?php echo $product_type_name; ?></span></dd>
                <dt class="col-5">เลเซอร์ : </dt>
                <dd class="col-5"><input class="form-check-input" <?php if ($order['engrave'] == 'สลักข้อความบนแท่งเงิน') echo "checked"; ?> type="checkbox"></dd>
                <dt class="col-5">ไม่เลเซอร์ : </dt>
                <dd class="col-5"><input class="form-check-input" <?php if ($order['engrave'] == 'ไม่สลักข้อความบนแท่งเงิน') echo "checked"; ?> type="checkbox"></dd>

            </dl>
        </div>
        <div class="big-text">
            <dl class="row col-7 offset-5">
                <dt class="col-5">ราคาต่อแท่ง :</dt>
                <dd class="col-6 text-right under-line"><?php echo number_format($order['price'], 2, ".", ","); ?> บาท</dd>
                <dt class="col-5">ราคารวม :</dt>
                <dd class="col-6 text-right under-line"><?php echo number_format($order['total'], 2, ".", ","); ?> บาท</dd>
                <dt class="col-5">ภาษีมูลค่าเพิ่ม 7% :</dt>
                <dd class="col-6 text-right under-line"><?php echo number_format($order['vat'], 2, ".", ","); ?> บาท</dd>
                <dt class="col-5">ราคาทั้งหมด :</dt>
                <dd class="col-6 text-right under-line"><?php echo number_format($order['net'], 2, ".", ","); ?> บาท</dd>
            </dl>
        </div>
        <div>
            <div class="big-text">
                <dl class="row col-7">
                    <dt class="col-4">วันที่ขาย :</dt>
                    <dd class="col-8 ">
                        <span class="under-line pl-4 pr-4"><?php

                                                            if (is_null($order['date'])) {
                                                                echo "";
                                                            } else {
                                                                echo date("d/m/Y", strtotime($order['date']));
                                                            }
                                                            ?>

                        </span>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="mt-3 mb-5 mr-5 ml-4 ">
            <p class="big-text">
                ราคาที่ตกลงตามใบยืนยันการสั่งขายแท่งดีไซน์นี้ ผู้ขายและผู้ซื้อไม่สามารถเปลี่ยนแปลงได้ ไม่ว่าราคาจะขึ้น หรือ ลง ไม่ว่ากรณีใดใดทั้งสิ้น รายละเอียดดังกล่าวถูกต้องทุกประการ
            </p>
            <p class="big-text">
                Buyer and Seller both agreed on price indicated in Order Confirmation. Agreed price shall be fixed and cannot be changed under any circumstance, disregarding any change in market price.
            </p>
            <p class="big-text">
                ผู้ขายจะต้องลงนาม หรือแจ้งยืนยันคำสั่งซื้อส่งกลับมา ผ่านทาง E-mail : sale@bowinsgroup.com หรือช่องทาง Line ภายในวันที่ทำการสั่งขาย ก่อน 17.30 น. มิฉะนั้นจะถือว่าคำสั่งซื้อดังกล่าวไม่สมบูรณ์ ผู้ขายมีสิทธิ์ยกเลิกคำสั่งซื้อดังกล่าวได้โดยชอบธรรม
            </p>
            <p class="big-text">
                The seller must sign or notify confirmation of the order sent back via E-mail: sale@bowinsgroup.com or Line channel within the day of the sale order before 5:30 p.m. Otherwise, the order will be considered incomplete. The seller has the right to cancel such orders at their right.
            </p>
            <p class="big-text">
                - ผู้ขายยืนยันว่ารายละเอียดดังกล่าวถูกต้องทุกประการ (The seller confirms that all the information is accurate.)
            </p>
        </div>
        <table class="p-5 mt-5 big-text" width="100%">
            <tbody>
                <tr>
                    <td class="text-center">
                        <div>__________________________</div>
                        <div>ผู้มีอำนาจการสั่งซื้อ</div>
                        <div><?php echo $order['customer_name']; ?></div>
                    </td>
                    <td class="text-center">
                        <h3 style="font-family: cursive;"><?php echo $signature; ?></h3>
                        <div style="margin-top: -25px;">________________________</div>
                        <div>พนักงานขาย</div>
                        <div><?php echo $sales; ?></div>
                        <div> บริษัท โบวินส์ ซิวเวอร์ จำกัด</div>
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
    .big-text {
        font-size: 14pt;
    }

    .under-line {
        border-bottom: 1px solid #000;
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