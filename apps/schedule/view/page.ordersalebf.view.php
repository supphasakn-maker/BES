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

$type_th = '-';
switch ($order['orderable_type'] ?? '') {
	case 'delivered_by_company':
		$type_th = 'จัดส่งโดยรถบริษัท';
		break;
	case 'post_office':
		$type_th = 'จัดส่งโดยไปรษณีย์ไทย';
		break;
	case 'receive_at_company':
		$type_th = 'รับสินค้าที่บริษัท';
		break;
	case 'receive_at_luckgems':
		$type_th = 'รับสินค้าที่ Luck Gems';
		break;
	case 'delivered_by_transport':
		$type_th = 'จัดส่งโดยขนส่ง';
		break;
	default:
		$type_th = '-';
		break;
}

$delivery_date_str = '';
if (!empty($order['delivery_date'])) {
	$ts = strtotime($order['delivery_date']);
	$delivery_date_str = $ts ? date("d/m/Y", $ts) : '';
}
$delivery_time_str = (isset($order['delivery_time']) && $order['delivery_time'] !== 'none' && $order['delivery_time'] !== '')
	? $order['delivery_time']
	: '-';
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

	<div class="container docu-print mt-1">
		<h2 class="text-center">ใบยืนยันการสั่งซื้อแท่งเงิน / Purchase Confirmation</h2>
		<br>
		<div class="big-text mt-3">
			<dl class="row col-5 offset-8">
				<dt class="col-3">วันที่ :</dt>
				<dd class="col-8 under-line "><?php echo date("d/m/Y", strtotime($order['date'])); ?></dd>
				<dt class="col-3">No :</dt>
				<dd class="col-8 under-line"><?php echo htmlspecialchars($order['code']); ?></dd>
			</dl>
		</div>
		<div class="big-text">
			<dl class="row col-8">
				<dt class="col-3">บริษัท / ชื่อ :</dt>
				<dd class="col-8 under-line">
					<?php echo is_null($customer['org_name']) ? htmlspecialchars($order['customer_name']) : htmlspecialchars($customer['org_name']); ?>
				</dd>
				<dt class="col-3">เรียน : </dt>
				<dd class="col-8 under-line"><?php echo htmlspecialchars($order['info_contact']); ?></dd>
				<dt class="col-3"><?php echo htmlspecialchars($product_name); ?> : </dt>
				<dd class="col-8 under-line">
					<?php echo number_format($order['amount'], 4, ".", ","); ?> <span class="ml-2">กิโล</span>
				</dd>
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

		<div <?php if ($order['keep_silver'] == '1' && is_null($order['delivery_date']) && $order['delivery_date'] === '') : ?> style="display: none;" <?php endif; ?>>
			<div class="big-text">
				<dl class="row col-7">
					<dt class="col-4">รูปแบบการจัดส่ง :</dt>
					<dd class="col-8">
						<span class="under-line pl-4 pr-4"><?php echo htmlspecialchars($type_th); ?></span>
					</dd>
				</dl>
			</div>
			<div class="big-text">
				<dl class="row col-7">
					<dt class="col-4">วันที่ส่งสินค้า :</dt>
					<dd class="col-8">
						<span class="under-line pl-4 pr-4"><?php echo $delivery_date_str !== '' ? $delivery_date_str : '-'; ?></span>
						<span class="under-line pl-4 pr-4"><?php echo htmlspecialchars($delivery_time_str); ?></span>
					</dd>
				</dl>
			</div>
		</div>

		<div class="mt-3 mb-5 mr-5 ml-4 ">
			<p>
				ราคาที่ตกลงตามใบยืนยันการสั่งซื้อนี้ ผู้ขายและผู้ซื้อไม่สามารถเปลี่ยนแปลงได้ไม่ว่าราคาจะขึ้นหรือลงไม่ว่ากรณีใดทั้งสิ้น ผู้ซื้อจะต้องลงนาม หรือ แจ้งยืนยันคำสั่งซื้อส่งกลับมาให้ผู้ขายผ่านทาง Email: sale@bowinsgroup.com หรือ ช่องทางLine ภายในวันที่ทำการสั่งซื้อมิฉะนั้นจะถือว่าคำสั้งซื้อดังกล่าวไม่สมบูรณ์ ผู้ขายมีสิทธิ์ยกเลิกคำสั่งซื้อดังกล่าวได้โดยชอบธรรมและไม่ต้องบอกกล่าวล่วงหน้า</p>
			<p>
				The price agreed upon in this Purchase Confirmation shall be <b>final and binding,</b> and <b>neither the Seller nor the Buyer shall alter it </b>, regardless of any increase or decrease in price under any circumstances.
				<b>The Buyer is required to sign or provide written confirmation </b>of the purchase order and send it back to the Seller via <b> Email: sale@bowinsgroup.com </b>or through the Line application on the date of the order. Failure to do so shall render the purchase order incomplete and invalid.
				The Seller reserves the right to <b>cancel such purchase order at its sole discretion </b>without prior notice.</p>
			<p>

				กรณีสินค้าจัดส่งทางไปรษณีย์ : การจัดส่งสินค้าทางบริษัทขนส่งมาตรฐานของบริษัท ไปรษณีย์ไทย จำกัด มี <b>วงเงินรับประกันค่าสินค้าสูงสุดไม่เกิน 2,000 บาทต่อรายการจัดส่ง </b> บริษัทฯ <b>ขอสงวนสิทธิ์การรับผิดชอบความเสียหายหรือสูญหายเกินวงเงินดังกล่าวในทุกกรณี</b> หากลูกค้าต้องการให้บริษัทฯ รับประกันความเสียหายเต็มมูลค่าของสินค้า สามารถเลือก <b>ซื้อประกันค่าสินค้าเพิ่มเติม</b> ได้ โดยมีค่าใช้จ่ายตามจริงจากบริษัทขนส่ง ในกรณีที่ลูกค้า <b>ไม่เลือกซื้อประกันเพิ่ม </b>จะถือว่าลูกค้าได้ <b>ยอมรับเงื่อนไขวงเงินรับประกันสูงสุด 2,000 บาท </b> และบริษัทฯ จะไม่รับผิดชอบต่อความเสียหายเกินวงเงินดังกล่าว</p>
			<p>

				In the case of shipment via postal service: Products delivered through the standard delivery service of <b>Thailand Post Co., Ltd.</b> are covered by an insurance limit of <b>up to 2,000 Baht per shipment.</b> The Company reserves the right <b>not to be held liable for any loss or damage exceeding this coverage limit under any circumstances.</b> If the customer wishes the Company to provide <b>full-value insurance coverage </b> for the goods, the customer may choose to purchase <b>additional shipping insurance</b>, subject to the actual rate charged by the carrier. In the event that the customer <b>does not opt for additional insurance</b>, it shall be deemed that the customer has <b>accepted the maximum insurance coverage limit of 2,000 Baht</b>, and the Company <b>shall not be responsible for any loss or damage exceeding such limit.</b></p>

			</p>
		</div>

		<div <?php if (intval($order['keep_silver']) === 0 && !empty($order['delivery_date'])): ?>style="display: none;" <?php endif; ?>>
			<div class="big-text">
				<dl class="row col-8">
					<dt class="col-3">ฝากสินค้า : </dt>
					<dd class="col-8 under-line">
						<?php echo number_format($order['amount'], 4, ".", ","); ?> <span class="ml-2">กิโล</span>
					</dd>
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
						<div><?php echo htmlspecialchars($order['info_contact']); ?></div>
						<div>
							<?php echo is_null($customer['org_name']) ? htmlspecialchars($order['customer_name']) : htmlspecialchars($customer['org_name']); ?>
						</div>
					</td>
					<td class="text-center">
						<h3 style="font-family: cursive;"><?php echo htmlspecialchars($signature); ?></h3>
						<div style="margin-top: -25px;">________________________</div>
						<div>พนักงานขาย</div>
						<div><?php echo htmlspecialchars($sales); ?></div>
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