<?php
$today = time();
$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_GET['order_id']);
$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $order['customer_id']);



if (is_null($order['product_id'])) {
	$product_name = "เม็ดเงิน";
} else {
	$product = $dbc->GetRecord("bs_products", "*", "id=" . $order['product_id']);
	$product_name = $product['name'];
}

$signature = "";
if ($dbc->HasRecord("bs_employees", "id=" . $order['sales'])) {
	$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
	$sales = $employee['fullname'];
	$signature = $employee['nickname'];
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
			<img class="pull-right" src="img/AW_Bowins Future_ หัวใบเสร็จ.png" width="1005px" height="120px">
		</div>
	</div>

	<!-- <hr style="border:1px solid #000"> -->
	<div class="container docu-print mt-1">
		<h2 class="text-center">ใบยืนยันการสั่งซื้อแท่งเงิน / Purchase Confirmation</h2>
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
				<dt class="col-3">บริษัท / ชื่อ :</dt>
				<dd class="col-8 under-line"><?php echo is_null($customer['org_name']) ? $order['customer_name'] : $customer['org_name']; ?></dd>
				<dt class="col-3">เรียน : </dt>
				<dd class="col-8 under-line"><?php echo $order['info_contact']; ?></dd>
				<dt class="col-3"><?php echo $product_name; ?> : </dt>
				<dd class="col-8 under-line"><?php echo number_format($order['amount'], 4, ".", ","); ?> <span class="ml-2">กิโล</span></dd>
			</dl>
		</div>
		<div class="big-text">
			<dl class="row col-7 offset-5">
				<dt class="col-5">ราคาต่อกิโล :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['price'], 2, ".", ","); ?> บาท</dd>
				<dt class="col-5">ราคารวม :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['total'], 2, ".", ","); ?> บาท</dd>
				<dt class="col-5">ภาษีมูลค่าเพิ่ม 7% :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['vat'], 2, ".", ","); ?> บาท</dd>
				<dt class="col-5">ราคารวมทั้งหมด :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['net'], 2, ".", ","); ?> บาท</dd>
			</dl>
		</div>
		<div <?php if ($order['keep_silver'] == '1') : ?> style="display: none;" <?php endif; ?>>
			<div class="big-text">
				<dl class="row col-7">
					<dt class="col-4">วันที่ส่งสินค้า :</dt>
					<dd class="col-8 ">
						<span class="under-line pl-4 pr-4"><?php

															if (is_null($order['delivery_date'])) {
																echo "";
															} else {
																echo date("d/m/Y", strtotime($order['delivery_date']));
															}
															?>

						</span>
						<span class="under-line pl-4 pr-4"><?php echo $order['delivery_time'] == "none" ? "-" : $order['delivery_time']; ?></span>
					</dd>
				</dl>
			</div>
		</div>
		<div class="mt-3 mb-5 mr-5 ml-4 ">
			<p class="big-text">
				ราคาที่ตกลงตามใบยืนยันราคารับซื้อแท่งเงินนี้ ผู้ขายและผู้ซื้อไม่สามารถเปลี่ยนแปลงได้
				ไม่ว่าราคาจะขึ้นหรือลง ไม่ว่ากรณีใดใดทั้งสิ้น รายละเอียดดังกล่าวถูกต้องทุกประการ
			</p>
			<p class="big-text">
				Buyer and Seller both agreed on price indicated in Order Confirmation. Agreed price shall be fixed and cannot be changed under any circumstance, disregarding any change in market price.
			</p>
			<p class="big-text">
				<b>หมายเหตุ :</b> สินค้าซื้อแล้ว ไม่รับเปลี่ยนทุกกรณี
				<br><b>วิธีดูแลรักษาแท่งเงิน</b><br>
				ไม่แกะพลาสติกที่ซีลสุญญากาศไว้กับแท่งเงินเพื่อป้องกันไม่ให้อากาศเข้า ซึ่งเป็นสาเหตุที่ทำให้แท่งเงินดำ และเพื่อทำให้แท่งเงินคงความเงาไว้ตลอดเวลา

			</p>
			<p class="big-text">
				Notice : Delivered goods cannot be returned, refunded or changed under any circumstance
				To store your silver bar, please do not remove plastic seal. Plastic seal protects your silver bar from oxidizing which turns silver blackish. Plastic seal also keeps silver bar shiny.
			</p>
			<p class="big-text">
				ผู้ซื้อจะต้องลงนาม หรือแจ้งยืนยันคำสั่งซื้อส่งกลับมาให้ผู้ขาย ผ่านทาง E-Mail : sale@bowinsgroup.com หรือช่องทาง Line ภายในวันที่ทำการสั่งซื้อ มิฉะนั้นจะถือว่าคำสั่งซื้อดังกล่าวไม่สมบูรณ์ ผู้ขายมีสิทธิ์ยกเลิกคำสั่งซื้อดังกล่าวได้โดยชอบธรรม
			</p>
			<p class="big-text">
				Buyer shall sign or confirm Order confirmation and send back to Seller via Seller's email : sale@bowinsgroup.com or via Seller's Line account within the same day of the agreed order date. Failing to do so, Seller will treat the order as incomplete and reserve every right to cancel this transaction.
			</p>
		</div>
		<div <?php if ($order['keep_silver'] == '0') : ?> style="display: none;" <?php endif; ?>>
			<div class="big-text">
				<dl class="row col-8">
					<dt class="col-3">ฝากสินค้า : </dt>
					<dd class="col-8 under-line"><?php echo number_format($order['amount'], 4, ".", ","); ?> <span class="ml-2">กิโล</span></dd>
				</dl>
			</div>
			<div class="mt-5 mb-5 mr-5 ml-4 ">
				<p class="big-text">
					กรณีผู้ซื้อฝากสินค้าไว้กับทางบริษัทตามจำนวนยอดในใบยืนยันการสั่งซื้อ หากผู้ซื้อประสงค์จะรับสินค้าดังกล่าวผู้ซื้อจะต้องแจ้งกับทางบริษัทล่วงหน้าอย่างน้อย 7 วันทำการ
				</p>
			</div>
		</div>
		<table class="p-5 mt-5 big-text" width="100%">
			<tbody>
				<tr>
					<td class="text-center">
						<div>__________________________</div>
						<div>ผู้มีอำนาจการสั่งซื้อ</div>
						<div><?php echo $order['info_contact']; ?></div>
						<div><?php echo is_null($customer['org_name']) ? $order['customer_name'] : $customer['org_name']; ?></div>
					</td>
					<td class="text-center">
						<h3 style="font-family: cursive;"><?php echo $signature; ?></h3>
						<div style="margin-top: -25px;">________________________</div>
						<div>พนักงานขาย</div>
						<div><?php echo $sales; ?></div>
						<div> บริษัท โบวินส์ ฟิวเจอร์ส จำกัด</div>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div class="d-flex align-items-center container">
			<div>
				<img class="pull-right" src="img/BW_Future_ที่อยู่ท้ายจดหมาย.png" width="675px" height="115px">
			</div>
		</div>
	</div>
</div>
</div>
<style>
	.big-text {
		font-size: 16pt;
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