<?php
	$today = time();
	$order = $dbc->GetRecord("bs_orders_buy","*","id=".$_GET['order_id']);
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

<style>

.big-text{
	font-size: 16pt;
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