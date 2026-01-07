<?php
$today = time();
$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_GET['order_id']);
$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $order['customer_id']);



if ($order['product_id'] == 2) {
	$product_name = "SILVER BAR 1 KG";
	$pro2 = "แท่งเงิน";
	$pro = "BAR";
} else {
	$product = $dbc->GetRecord("bs_products", "*", "id=" . $order['product_id']);
	$product_name = $product['name'];
	$pro2 = "เม็ดเงิน";
	$pro = "GRAINS";
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
			<img class="pull-right" src="img/bowins_rjc_head.png" width="1005px" height="120px">
		</div>
	</div>
	<div style="display: flex; justify-content: flex-end" class="container">
		<div>FM-SM-004</div>
	</div>

	<!-- <hr style="border:1px solid #000"> -->
	<div class="container docu-print mt-1">
		<h2 class="text-center">ใบยืนยันการสั่งซื้อ<?php echo $pro2; ?> / SILVER <?php echo $pro; ?> Purchase Confirmation</h2>
		<br>
		<div class="big-text mt-2">
			<dl class="row col-5 offset-8">
				<dt class="col-3">Date :</dt>
				<dd class="col-8 under-line "><?php echo date("d/m/Y", strtotime($order['date'])); ?></dd>
				<dt class="col-3">No :</dt>
				<dd class="col-8 under-line"><?php echo $order['code']; ?></dd>
			</dl>
		</div>
		<div class="big-text">
			<dl class="row col-8">
				<dt class="col-4 mt-2">บริษัท / ชื่อ :</dt>
				<dd class="col-8 under-line"><?php echo is_null($customer['org_name']) ? $order['customer_name'] : $customer['org_name']; ?></dd>
				<dt class="col-6 small-text">Company / Name </dt>
				<dd class="col-5"></dd>
				<dt class="col-4 mt-2">เรียน : </dt>
				<dd class="col-5 under-line"><?php echo $order['info_contact']; ?></dd>
				<dt class="col-4 small-text">Dear</dt>
				<dd class="col-5"></dd>
				<dt class="col-4"><?php echo $product_name; ?> : </dt>
				<dd class="col-5 under-line"><?php echo number_format($order['amount'], 4, ".", ","); ?> <span class="ml-2">Kgs.</span></dd>
			</dl>
		</div>
		<div class="big-text">
			<dl class="row col-7 offset-5">
				<dt class="col-5 mt-2">ราคาต่อกิโล :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['price'], 2, ".", ","); ?> Baht</dd>
				<dt class="col-5 small-text">Price Per Kgs.</dt>
				<dd class="col-6 text-right "></dd>
				<dt class="col-5 mt-2">ราคารวม :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['total'], 2, ".", ","); ?> Baht</dd>
				<dt class="col-5 small-text">Total Price</dt>
				<dd class="col-6 text-right "></dd>
				<dt class="col-5 mt-2">ภาษีมูลค่าเพิ่ม 7% :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['vat'], 2, ".", ","); ?> Baht</dd>
				<dt class="col-5 small-text">Vat 7%</dt>
				<dd class="col-6 text-right "></dd>
				<dt class="col-5 mt-2">ราคารวมทั้งหมด :</dt>
				<dd class="col-6 text-right under-line"><?php echo number_format($order['net'], 2, ".", ","); ?> Baht</dd>
				<dt class="col-5 small-text">Total Amount</dt>
				<dd class="col-6 text-right "></dd>
			</dl>
		</div>
		<div <?php if ($order['keep_silver'] == '1') : ?> style="display: none;" <?php endif; ?>>
			<div class="big-text">
				<dl class="row col-7">
					<dt class="col-4 mt-2">วันที่ส่งสินค้า :</dt>
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
					<dt class="col-4 small-text">Delivery Date.</dt>
				</dl>
			</div>
		</div>
		<div class="mt-1 mb-5 mr-5 ml-4 small-text font-weight-bold">
			<p>
				1. ผู้ซื้อและผู้ขาย ตกลงราคาตามเอกสารใบยืนยันคำสั่งซื้อ และไม่สามารถเปลี่ยนแปลงราคาได้ ไม่ว่าราคาตลาดจะมีการเปลี่ยนแปลงหรือไม่ก็ตาม
			</p>
			<p>
				Buyer and Seller both agreed on price indicated in Order Confirmation. Agreed price shall be fixed and cannot be changed under any circumstance, disregarding any change in market price.
			</p>
			<p>
				2. ผู้ซื้อจะต้องลงนาม หรือแจ้งยืนยันคำสั่งซื้อส่งกลับมาให้ผู้ขาย ผ่านทาง E-Mail : sale@bowinsgroup.com หรือช่องทาง Line ภายในวันที่ทำการสั่งซื้อ มิฉะนั้นจะถือว่าคำสั่งซื้อดังกล่าวไม่สมบูรณ์ ผู้ขายมีสิทธิ์ยกเลิกคำสั่งซื้อดังกล่าวได้โดยชอบธรรม
			</p>
			<p>
				Buyer shall sign or confirm Order confirmation and send back to Seller via Seller's email : sale@bowinsgroup.com or via Seller's Line account within the same day of the agreed order date. Failing to do so, Seller will treat the order as incomplete and reserve every right to cancel this transaction.
			</p>
			<p>
				3. ถ้าผู้ซื้อไม่ปฏิบัติตามใบยืนยันตามที่ระบุข้างต้น ผู้ขายขอสงวนสิทธิดำเนินการเรียกร้องค่าเสียหายตามกฏหมาย
			</p>
			<p>
				In case Buyer does not comply with 1) and/or 2), Seller reserves every right to pursue any damage via every applicable Law.
			</p>
		</div>
		<div <?php if ($order['keep_silver'] == '0') : ?> style="display: none;" <?php endif; ?>>
			<div class="small-text font-weight-bold">
				<dl class="row col-8">
					<dt class="col-3">ฝากสินค้า : </dt>
					<dd class="col-8 under-line"><?php echo number_format($order['amount'], 4, ".", ","); ?> <span class="ml-2">Kgs.</span></dd>
				</dl>
			</div>
			<div class="mt-2 mb-5 mr-5 ml-4 ">
				<p class="small-text font-weight-bold">
					กรณีผู้ซื้อฝากสินค้าไว้กับทางบริษัทตามจำนวนยอดในใบยืนยันการสั่งซื้อ หากผู้ซื้อประสงค์จะรับสินค้าดังกล่าวผู้ซื้อจะต้องแจ้งกับทางบริษัทล่วงหน้าอย่างน้อย 7 วันทำการ
				</p>
			</div>
		</div>
		<table class="p-5 mt-2 big-text" width="100%">
			<tbody>
				<tr>
					<td class="text-center">
						<div>__________________________</div>
						<div>ผู้มีอำนาจการสั่งซื้อ (Ordering authority)</div>
						<div><?php echo $order['info_contact']; ?></div>
						<div><?php echo is_null($customer['org_name']) ? $order['customer_name'] : $customer['org_name']; ?></div>
					</td>
					<td class="text-center">
						<h3 style="font-family: cursive;"><?php echo $signature; ?></h3>
						<div style="margin-top: -25px;">________________________</div>
						<div>พนักงานขาย (Salesperson)</div>
						<div><?php echo $sales; ?></div>
						<div> บริษัท โบวินส์ ซิลเวอร์ จำกัด</div>
						<div class="small-text font-weight-bold">Bowins Silver Co., Ltd.</div>
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
		font-size: 16pt;
	}

	.under-line {
		border-bottom: 1px solid #000;
	}

	.small-text {
		font-size: 12pt;
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