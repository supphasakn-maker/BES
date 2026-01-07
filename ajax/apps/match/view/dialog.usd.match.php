<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			if(isset($this->param['purchase_spot']) && isset($this->param['purchase_usd'])){
			
			
			echo '<form name="form_matchusd">';
			echo '<div class="form-group row">';
				echo '<label class="col-sm-2 col-form-label text-right">Mapping Date</label>';
				echo '<div class="col-sm-5">';
					echo '<input type="date" class="form-control" name="date" value="'.date("Y-m-d").'">';
				echo '</div>';
			echo '</div>';
			echo '<div class="row">';
				echo '<div class="col-6">';
					echo '<table class="table table-sm table-bordered">';
						echo '<tfoot>';
							echo '<tr>';
								echo '<th colspan="3" class="text-right">';
									echo 'Total <span id="usd_total_match_spot" class="badge">100</span>';
							
								echo '</th>';
							echo '</tr>';
						echo '</tfoot>';
						echo '<thead>';
							echo '<tr>';
								echo '<th class="text-center">Date</th>';
								echo '<th class="text-center">Purchase Amount</th>';
								echo '<th class="text-center">Match Amount</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						
						foreach($this->param['purchase_spot'] as $purchase_spot){
							if(strpos($purchase_spot, 'm')!== false){
								$purchase_spot = substr($purchase_spot,2);
								$mapping = $dbc->GetRecord("bs_mapping_usd_spots","*","id=".$purchase_spot." AND mapping_id IS NULL");
								$purchase = $dbc->GetRecord("bs_purchase_spot","*","id=".$mapping['purchase_id']);
								$amount = $purchase['amount']*($purchase['rate_spot']+$purchase['rate_pmdc'])*32.1507;
								
								echo '<tr>';
									echo '<input type="hidden" name="spot_mapping_id[]" value="'.$mapping['id'].'">';
									echo '<input type="hidden" name="spot_id[]" value="'.$purchase['id'].'">';
									echo '<td class="text-center align-middle text-nowrap">'.$purchase['date'].'</td>';
									echo '<td class="text-center align-middle">'.number_format($mapping['amount'],2).'/'.number_format($amount,2).'</td>';
									echo '<td class="p-0">';
										$class = "form-control form-control-sm text-center rounded-0 border-dark";
										$onchange = "fn.app.match.usd.match_calculation()";
										echo '<input xname="spot_amount" name="spot_amount[]" class="'.$class.'" onchange="'.$onchange.'" value="'.number_format($mapping['amount'],4,null,"").'">';
									echo '</td>';
								echo '</tr>';
							}else{
								$purchase = $dbc->GetRecord("bs_purchase_spot","*","id=".$purchase_spot);
								$amount = $purchase['amount']*($purchase['rate_spot']+$purchase['rate_pmdc'])*32.1507;
								if($dbc->HasRecord("bs_mapping_usd_spots","purchase_id=".$purchase['id']." AND mapping_id IS NULL")){
									$mapping = $dbc->GetRecord("bs_mapping_usd_spots","*","purchase_id=".$purchase['id']." AND mapping_id IS NULL");
									echo '<tr>';
										echo '<input type="hidden" name="spot_mapping_id[]" value="'.$mapping['id'].'">';
										echo '<input type="hidden" name="spot_id[]" value="'.$purchase['id'].'">';
										echo '<td class="text-center align-middle text-nowrap">'.$purchase['date'].'</td>';
										echo '<td class="text-center align-middle">'.number_format($mapping['amount'],2).'/'.number_format($amount,2).'</td>';
										echo '<td class="p-0">';
											$class = "form-control form-control-sm text-center rounded-0 border-dark";
											$onchange = "fn.app.match.usd.match_calculation()";
											echo '<input xname="spot_amount" name="spot_amount[]" class="'.$class.'" onchange="'.$onchange.'" value="'.number_format($mapping['amount'],4,null,"").'">';
										echo '</td>';
									echo '</tr>';
								}else{
									echo '<tr>';
										echo '<input type="hidden" name="spot_mapping_id[]" value="">';
										echo '<input type="hidden" name="spot_id[]" value="'.$purchase['id'].'">';
										
										echo '<td class="text-center align-middle text-nowrap">'.$purchase['date'].'</td>';
										echo '<td class="text-center align-middle">'.number_format($amount,2).'</td>';
										echo '<td class="p-0">';
											$class = "form-control form-control-sm text-center rounded-0 border-dark";
											$onchange = "fn.app.match.usd.match_calculation()";
											echo '<input xname="spot_amount" name="spot_amount[]" class="'.$class.'" onchange="'.$onchange.'" value="'.number_format($amount,4,null,"").'">';
										echo '</td>';
									echo '</tr>';
								}
							}
						}
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
				echo '<div class="col-6">';
					echo '<table class="table table-sm table-bordered">';
						echo '<tfoot>';
							echo '<tr>';
								echo '<th colspan="3" class="text-right">';
									echo 'Total <span id="usd_total_match_usd" class="badge">100</span>';
							
								echo '</th>';
							echo '</tr>';
						echo '</tfoot>';
						echo '<thead>';
							echo '<tr>';
								echo '<th class="text-center">Purchase Date</th>';
								echo '<th class="text-center">Purchase Amount</th>';
								echo '<th class="text-center">Match Amount</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($this->param['purchase_usd'] as $purchase){
							$purchase = $dbc->GetRecord("bs_purchase_usd","*","id=".$purchase);
							if($dbc->HasRecord("bs_mapping_usd_purchases","purchase_id=".$purchase['id']." AND mapping_id IS NULL")){
								$mapping = $dbc->GetRecord("bs_mapping_usd_purchases","*","purchase_id=".$purchase['id']." AND mapping_id IS NULL");
								
								echo '<tr>';
									echo '<input type="hidden" name="purchase_mapping_id[]" value="'.$mapping['id'].'">';
									echo '<input type="hidden" name="purchase_id[]" value="'.$purchase['id'].'">';
									echo '<td class="text-center align-middle text-nowrap">'.$purchase['date'].'</td>';
									echo '<td class="text-center align-middle">'.number_format($mapping['amount'],2).'/'.number_format($purchase['amount'],2).'</td>';
									
									echo '<td class="p-0">';
										$class = "form-control form-control-sm text-center rounded-0 border-dark";
										$onchange = "fn.app.match.usd.match_calculation()";
										echo '<input xname="purchase_amount" name="purchase_amount[]" class="'.$class.'" onchange="'.$onchange.'" value="'.number_format($mapping['amount'],2,null,"").'">';
									echo '</td>';
									
								echo '</tr>';
							}else{
								echo '<tr>';
									echo '<input type="hidden" name="purchase_mapping_id[]" value="">';
									echo '<input type="hidden" name="purchase_id[]" value="'.$purchase['id'].'">';
									echo '<td class="text-center align-middle text-nowrap">'.$purchase['date'].'</td>';
									echo '<td class="text-center align-middle">'.number_format($purchase['amount'],2).'</td>';
									echo '<td class="p-0">';
										$class = "form-control form-control-sm text-center rounded-0 border-dark";
										$onchange = "fn.app.match.usd.match_calculation()";
										echo '<input xname="purchase_amount" name="purchase_amount[]" class="'.$class.'" onchange="'.$onchange.'" value="'.number_format($purchase['amount'],2,null,"").'">';
									echo '</td>';
								echo '</tr>';
							}
						}
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
				echo '<div class="col-12">';
					echo '<textarea name="remark" class="form-control" placeholder="Remark"></textarea>';
				echo '</div>';
			echo '</div>';
			echo '</form>';
			}else{
				echo '<div class="alert alert-danger">ข้อมูลไม่ครบ</div>';
			}
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_match_usd","Match Usd");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Match","fn.app.match.usd.match()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
