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


	if($_POST['date']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please select date!'
		));
	}else if(!isset($_POST['sales'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please select sales!'
		));
	}else if(!isset($_POST['purchase'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please select purchase!'
		));
	}else if(!isset($_POST['purchase_new'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please select purchase new!'
		));
	}else{
		
		$sales = array();
		$purchase = array();
		$purchase_new = array();
		
		$amount_sales = 0;
		$amount_purchase = 0;
		$amount_purchase_new = 0;
		
		$total_sales = 0;
		$total_purchase = 0;
		$total_purchase_new = 0;
		
		$value_sales = 0;
		$value_purchase = 0;
		$value_purchase_new = 0;
		
		$discount_sales = 0;
		$discount_purchase = 0;
		$discount_purchase_new = 0;
		
		
		
		//let value_profit = data.purchase_value-data.new_value;
		
		
		foreach($_POST['sales'] as $sales_id){
			$sales_item = $dbc->GetRecord("bs_sales_spot","*","id=".$sales_id);
			array_push($sales,$sales_item);
			$amount_sales += $sales_item['amount'];
			$total_sales += $sales_item['amount']*($sales_item['rate_spot']+$sales_item['rate_pmdc']);
			
			$value_sales += $sales_item['amount']*$sales_item['rate_spot'];
			$discount_sales += $sales_item['amount']*$sales_item['rate_pmdc'];
		}
		
		foreach($_POST['purchase'] as $purchase_id){
			$purchase_item = $dbc->GetRecord("bs_purchase_spot","*","id=".$purchase_id);
			array_push($purchase,$purchase_item);
			$amount_purchase += $purchase_item['amount'];
			$total_purchase += $purchase_item['amount']*($purchase_item['rate_spot']+$purchase_item['rate_pmdc']);
			
			$value_purchase += $purchase_item['amount']*$purchase_item['rate_spot'];
			$discount_purchase += $purchase_item['amount']*$purchase_item['rate_pmdc'];
		}
		
		foreach($_POST['purchase_new'] as $purchase_id){
			$purchase_item = $dbc->GetRecord("bs_purchase_spot","*","id=".$purchase_id);
			array_push($purchase_new,$purchase_item);
			$amount_purchase_new += $purchase_item['amount'];
			$total_purchase_new += $purchase_item['amount']*($purchase_item['rate_spot']+$purchase_item['rate_pmdc']);
			
			$value_purchase_new += $purchase_item['amount']*$purchase_item['rate_spot'];
			$value_purchase_new += $purchase_item['amount']*$purchase_item['rate_pmdc'];
		}
		
		
		if($amount_sales != $amount_purchase){
			echo json_encode(array(
				'success'=>false,
				'msg' => "Amount is not match!"
			));
		}else if($amount_sales != $amount_purchase_new){
			echo json_encode(array(
				'success'=>false,
				'msg' => "Amount is not match!"
			));
		}else{
			
			

			$data = array(
				'#id' => "DEFAULT",
				"date_adjust" => $_POST['date'],
				'#created' => 'NOW()',
				'#updated' => 'NOW()',
				"#value_amount" => $amount_purchase,
				"#value_buy" => $total_purchase,
				"#value_sell" => $total_sales,
				"#value_new" => $total_purchase_new,
				// value old - new
				"#value_profit" => $value_purchase-$value_purchase_new,
				"#value_adjust_cost" => $value_purchase-$value_purchase_new,
				"#value_adjust_discount" => $discount_purchase-$discount_purchase_new,
				"#value_net" => $total_purchase-$total_sales,  // Spot dif
				"#user" => $os->auth['id']
			);

			if($dbc->Insert("bs_adjust_cost",$data)){
				$adjust_id = $dbc->GetID();
				echo json_encode(array(
					'success'=>true,
					'msg'=> $adjust_id
				));
				
				foreach($sales as $item){
					$dbc->Update("bs_sales_spot",array("#adjust_id"=>$adjust_id),"id=".$item['id']);
				}
				
				foreach($purchase as $item){
					$dbc->Update("bs_purchase_spot",array("#adjust_id"=>$adjust_id,"adjust_type"=>"old"),"id=".$item['id']);
				}
				
				foreach($purchase_new as $item){
					$dbc->Update("bs_purchase_spot",array("#adjust_id"=>$adjust_id,"adjust_type"=>"new"),"id=".$item['id']);
				}				

				$adjust = $dbc->GetRecord("bs_adjust_cost","*","id=".$adjust_id);
				$os->save_log(0,$_SESSION['auth']['user_id'],"adjust-add",$adjust_id,array("adjusts" => $adjust));
			}else{
				echo json_encode(array(
					'success'=>false,
					'msg' => "Insert Error"
				));
			}
		}
	}

	$dbc->Close();
?>
