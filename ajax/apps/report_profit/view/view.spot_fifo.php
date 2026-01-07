<?php
	$title = "รายงาน SPOT FIFO";
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
			<th>Date</th>
			<th>Time</th>
			<th>Type</th>
			<th>Bowins Invoice </th>
			<th>Supplier/customer order</th>
			<th>Matched Buy spot</th>
			<th>Reference</th>
			<th>Spot Rate (USD/ounce)</th>
			<th>Discount (USD/ounce)</th>
			<th>Real Spot rate (USD/ounce)</th>
			<th>Purchase from/ Sales to </th>
			<th>In</th>
			<th>Out</th>
			<th>Adj</th>
			<th>Balance (Kg)</th>
			<th>Ending Balance Silver (USD)</th>
			<th>Cost Silver purchased (USD)</th>
			<th>Cost Silver sold (USD)</th>
			<th>Cost Silver Adj (USD)</th>
			<th>Selling price (THB/Kg)</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>














