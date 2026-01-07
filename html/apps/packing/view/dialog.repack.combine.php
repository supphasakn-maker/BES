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
			if(isset($this->param['items']) && count($this->param['items'])>1){
				$total = 0;
				echo '<form name="form_combine">';
					echo '<ul>';
					foreach($this->param['items'] as $item){
						$pack_item = $dbc->GetRecord("bs_packing_items","*","id=".$item);
						echo '<li>'.$pack_item['id'].':'.$pack_item['code'];
						
							echo '<input type="hidden" name="items[]" value="'.$item.'">';
						echo '</li>';
						$total += $pack_item['weight_actual'];
					}
					echo '</ul>';
					echo '<table class="table table-form table-bordered">';
						echo '<tbody>';
							echo '<tr>';
								echo '<th>Total Weight</th>';
								echo '<td><input type="name" name="weight" readonly class="form-control" value="'.$total.'"></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<th>Code</th>';
								echo '<td><input type="name" name="code" class="form-control"></td>';
							echo '</tr>';
						echo '</tbody>';
					echo '</table>';
				echo '</form>';
				
				
			}else{
				echo "<div class='alert alert-danger'>Please Select morethan 2 to combine <div>";
			}
			
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_combine_repack","Combine Repack");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Combine","fn.app.packing.repack.combine()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
