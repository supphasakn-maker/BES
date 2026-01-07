<?php
	$today = time();
	$order = $dbc->GetRecord("bs_orders","*","id=".$_GET['order_id']);
	$customer = $dbc->GetRecord("bs_customers","*","id=".$order['customer_id']);
	

	
	if(is_null($order['product_id'])){
		$product_name = "เม็ดเงิน";
	}else{
		$product = $dbc->GetRecord("bs_products","*","id=".$order['product_id']);
		$product_name = $product['name'];
		
	}
	
	$signature = "";
	if($dbc->HasRecord("bs_employees","id=".$order['sales'])){
		$employee = $dbc->GetRecord("bs_employees","*","id=".$order['sales']);
		$sales = $employee['fullname'];
		$signature = $employee['nickname'];
	}else{
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
	<div class="card-body mr-4 ml-4">
		<div class="d-flex align-items-center container">
			<div>
				<img class="pull-right" src="img/bowins.jpg">
			</div>
			<div class="m-4">
				<h2>บริษัท โบวินส์ ซิลเวอร์ จำกัด</h2>
				<span >
					
					เลขที่ 39 ซอยรามคำแหง 24/5 ถนนรามคำแหง แขวงหัวหมาก เขตบางกะปิ กรุงเทพมหานคร 10240 <br>
					โทรศัพท์ : 02-7203000 FAX : 02-7203041 E-mail : info@bowinsgroup.com				
				</span>
			</div>
		</div>
		<hr style="border:1px solid #000">
		<div class="container docu-print mt-5">
			<h2 class="text-center">ใบยืนยันการสั่งซื้อ / CONFIRM ORDER</h2>
			<br><br>
			<div class="big-text mt-4">
				<dl class="row col-5 offset-8">
					<dt class="col-3">วันที่ :</dt>
					<dd class="col-5 under-line "><?php echo date("d/m/Y",strtotime($order['date']));?></dd>
					<div class="col-2 "></div>
					<dt class="col-3">No :</dt>
					<dd class="col-5 under-line"><?php echo $order['code'];?></dd>
					<div class="col-2 "></div>
				</dl>
			</div>
			<br><br>
			<div class="big-text">
				<dl class="row col-8">
					<dt class="col-3">บริษัท / ชื่อ :</dt>
					<dd class="col-8 under-line"><?php echo is_null($customer['org_name'])?$order['customer_name']:$customer['org_name'];?></dd>
					<dt class="col-3">เรียน : </dt>
					<dd class="col-8 under-line"><?php echo $order['info_contact'];?></dd>
					<dt class="col-3"><?php echo $product_name;?> : </dt>
					<dd class="col-8 under-line"><?php echo number_format($order['amount'], 4, ".", ",");?> <span class="ml-2">กิโล</span></dd>
				</dl>
			</div>
			<br><br>
			<div class="big-text">
				<dl class="row col-7 offset-5">
					<dt class="col-5">ราคาต่อกิโล :</dt>
					<dd class="col-6 text-right under-line"><?php echo number_format($order['price'], 2, ".", ",");?> บาท</dd>
					<dt class="col-5">ราคารวม :</dt>
					<dd class="col-6 text-right under-line"><?php echo number_format($order['total'], 2, ".", ",");?> บาท</dd>
					<dt class="col-5">ภาษีมูลค่าเพิ่ม 7% :</dt>
					<dd class="col-6 text-right under-line"><?php echo number_format($order['vat'], 2, ".", ",");?> บาท</dd>
					<dt class="col-5">ราคารวมทั้งหมด :</dt>
					<dd class="col-6 text-right under-line"><?php echo number_format($order['net'], 2, ".", ",");?> บาท</dd>
				</dl>
			</div>
			<br><br>
			<div class="big-text">
				<dl class="row col-7">
					<dt class="col-4">วันที่ส่งสินค้า :</dt>
					<dd class="col-8 ">
						<span class="under-line pl-4 pr-4"><?php
						
						if(is_null($order['delivery_date'])){
							echo "";
						}else{
							echo date("d/m/Y",strtotime($order['delivery_date']));
						}
						?>
						
						</span> 
						<span class="under-line pl-4 pr-4"><?php echo $order['delivery_time']=="none"?"-":$order['delivery_time'];?></span>
					</dd>
				</dl>
			</div>
			<div class="mt-5 mb-5 mr-5 ml-4 ">
				<p class="big-text">
					ราคาที่ตกลงตามใบยืนยันการสั่งซื้อนี้ ผู้ขายและผู้ซื้อไม่สามารถเปลี่ยนแปลงได้ 
					ไม่ว่าราคาจะขึ้นหรือลง ไม่ว่ากรณีใดใดทั้งสิ้น รายละเอียดดังกล่าวถูกต้องทุกประการ 
				</p>
				<p class="big-text">
					เพื่อเป็นการยืนยันคำสั่งซื้อตามรายละเอียดดังกล่าว กรุณาเซ็นต์ชื่อยืนยันการสั่งซื้อ 
					แล้วแฟกซ์กลับมาที่ 0-2720-3041
				</p>
			</div>
			<div class="mb-3"><br><br><br></div>
			<table class="p-5 mt-5 big-text" width="100%">
				<tbody>
					<tr>
						<td class="text-center">
							<div>__________________________</div>
							<div>ผู้มีอำนาจการสั่งซื้อ</div>
							<div><?php echo $order['info_contact'];?></div>
							<div><?php echo is_null($customer['org_name'])?$order['customer_name']:$customer['org_name'];?></div>
						</td>
						<td class="text-center">
							<h3 style="font-family: cursive;"><?php echo $signature;?></h3>
							<div style="margin-top: -25px;">________________________</div>
							<div>พนักงานขาย</div>
							<div><?php echo $sales;?></div>
							<div> บริษัท โบวินส์ ซิลเวอร์ จำกัด</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>

.big-text{
	font-size: 16.5pt;
}

.under-line{
	border-bottom: 1px solid #000;
}



@media print {

  .main-header,.sidebar,.breadcrumb,.btn-area {
    display:none;
  }
  
}



</style>