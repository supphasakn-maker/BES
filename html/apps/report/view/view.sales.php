<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	$title = "รายงานการขาย";
	$period = $_POST['period'];
	switch($period){
		case "daily":
			$subtitle = "ประจำวันที่ ".date("d/m/Y",strtotime($_POST['date']));
			break;
		case "monthly":
			$subtitle = "ประดือน ".date("F Y",strtotime($_POST['month']));
			break;
		case "yearly":
			$subtitle = "ประจำปี ".$_POST['year'];
			break;
		case "custom":	
			$subtitle = "ตั้งแต่วันที่ ".date("d/m/Y",strtotime($_POST['month']))." ถึงวันที่ ".date("d/m/Y",strtotime($_POST['month']));
	}

?>

<section class="text-center">
	<h1><?php echo $title;?></h1>
	<p><?php echo $subtitle;?> </p>
</section>

</header>
<table class="table table-sm table-bordered">
	<thead>
		<tr>
			<th>วันสั่ง</th>
			<th>Delivery No.</th>
			<th>Order No.</th>
			<th>ลูกค้า</th>
			<th>kio</th>
			<th>บาท/กิโล</th>
			<th>vat</th>
			<th>ยอดรวม(ก่อน vat)</th>
			<th>ยอดรวม</th>
			<th>วันส่ง</th>
			<th>ผู้ขาย</th>
		</tr>
	</thead>
	<tbody>
	<?php
	switch($period){
		case "daily":
			$sql = "SELECT * FROM bs_orders WHERE DATE(date) = '".$_POST['date']."'";
			$rst = $dbc->Query($sql);
			while($order = $dbc->Fetch($rst)){
				
				$employee = $dbc->GetRecord("bs_employees","*","id=".$order['sales']);
				echo '<tr>';
					echo '<td class="text-center">'.$order['date'].'</td>';
					echo '<td class="text-center">'.$order['delivery_id'].'</td>';
					echo '<td class="text-center">'.$order['code'].'</td>';
					echo '<td class="text-center">'.$order['customer_name'].'</td>';
					echo '<td class="text-center">'.$order['amount'].'</td>';
					echo '<td class="text-center">'.$order['price'].'</td>';
					echo '<td class="text-center">'.$order['vat'].'</td>';
					echo '<td class="text-center">'.$order['total'].'</td>';
					echo '<td class="text-center">'.$order['net'].'</td>';
					echo '<td class="text-center">'.$order['delivery_date'].'</td>';
					echo '<td class="text-center">'.$employee['fullname'].'</td>';
				
				
				echo '</tr>';
				
				
			}
			/*
			$columns = array(
		"id" => "bs_orders.id",
		"code" => "bs_orders.code",
		"customer_id" => "bs_orders.customer_id",
		"customer_name" => "bs_orders.customer_name",
		"date" => "bs_orders.date",
		"sales" => "bs_employees.fullname",
		"user" => "bs_orders.user",
		"type" => "bs_orders.type",
		"parent" => "bs_orders.parent",
		"created" => "bs_orders.created",
		"updated" => "bs_orders.updated",
		"amount" => "FORMAT(bs_orders.amount,2)",
		"price" => "FORMAT(bs_orders.price,2)",
		"vat_type" => "bs_orders.vat_type",
		"vat" => "FORMAT(bs_orders.vat,2)",
		"total" => "FORMAT(bs_orders.total,2)",
		"net" => "FORMAT(bs_orders.net,2)",
		"delivery_date" => "bs_orders.delivery_date",
		"delivery_time" => "bs_orders.delivery_time",
		"lock_status" => "bs_orders.lock_status",
		"status" => "bs_orders.status",
		"comment" => "bs_orders.comment",
		"shipping_address" => "bs_orders.shipping_address",
		"billing_address" => "bs_orders.billing_address",
		"rate_spot" => "bs_orders.rate_spot",
		"rate_exchange" => "bs_orders.rate_exchange",
		"billing_id" => "bs_orders.billing_id",
		"currency" => "bs_orders.currency",
		"info_payment" => "bs_orders.info_payment",
		"info_contact" => "bs_orders.info_contact",
		"delivery_id" => "bs_orders.delivery_id",
		"delivery_code" => "bs_deliveries.code"
	);
			
			*/
			break;
		case "monthly":
			break;
		case "yearly":
			break;
		case "custom":	
	}
	?>
	</tbody>
</table>

<?php
	
?>












