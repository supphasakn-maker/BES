<div class="mb-2">
	<button class="btn btn-outline-dark " onclick="window.history.back()">Back</button>
	<button class="btn btn-danger " onclick="fn.app.delivery.delivery.prepare()">Save</button>
<div>
<?php
	global $os;
	$iform = new iform($dbc,$this->auth);
	$delivery = $dbc->GetRecord("bs_deliveries","*","id=".$_GET['id']);
	$order = $dbc->GetRecord("bs_orders","*","delivery_id=".$delivery['id']);
	$aTruckType = json_decode($os->load_variable("db_truck_type","json"),true);
	$aTruckLicense = json_decode($os->load_variable("db_truck_license","json"),true);
	
	echo '<div id="template_TruckType" class="d-none">';
		$iform->EchoItem(array(
			"type" => "comboboxdatabank",
			"source" => "db_truck_type",
			"class" => "form-control-sm",
			"name" => "truck_type[]",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			)
		)); 
	echo '</div>';
	echo '<div id="template_TruckLicense" class="d-none">';
		$iform->EchoItem(array(
			"type" => "comboboxdatabank",
			"source" => "db_truck_license",
			"class" => "form-control-sm",
			"name" => "truck_license[]",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			)
		)); 
	echo '</div>';
	
	echo '<table id="tblDeliveryDetail" data-id="'.$delivery['id'].'" class="table table-bordered table-sm mt-2">';
		echo '<tr>';
			echo '<th class="text-center">ใบส่งของ</th>';
			echo '<th class="text-center">หมายเลขบิล</th>';
			echo '<th class="text-center">ชื่อลูกค้า</th>';
			echo '<th class="text-center">จำนวน</th>';
			echo '<th class="text-center">วันที่ส่ง</th>';
		echo '</tr>';
		echo '<tr>';
			echo '<td class="text-center">';
				echo $delivery['code'];
			echo '</td>';
			echo '<td class="text-center">';
				echo $delivery['code'];
			echo '</td>';
			echo '<td class="text-center">';
				echo $order['customer_name'];
			echo '</td>';
			echo '<td class="text-center">';
				echo $delivery['amount'];
			echo '</td>';
			echo '<td class="text-center">';
				echo $delivery['delivery_date'];
			echo '</td>';
		echo '</tr>';
	echo '</table>';
	echo '<hr>';
	echo '<form name="form_preparedelivery">';
		echo '<input type="hidden" name="id" value="'.$delivery['id'].'">';
		echo '<div class="col-md-4 offset-4">';
			echo '<div class="input-group input-group-sm mb-3">';
				$iform->EchoItem(array(
					"name" => "driver",
					"type" => "comboboxdb",
					"source" => array(
						"name" => "fullname",
						"value" => "id",
						"table" => "bs_employees",
						"where" => "department = 2"
					)
				)); 
				echo '<div class="input-group-append">';
					echo '<button type="button" class="btn btn-warning" onclick="fn.app.delivery.delivery.append_driver()">Append</button>';
				echo '</div>';
			echo '</div>';
			
			echo '</div>';
			echo '<div class="container">';
				echo '<table id="tblDriver" class="table table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center"></th>';
							echo '<th class="text-center">ผู้ส่ง</th>';
							echo '<th class="text-center">เวลาออกจากบริษัท</th>';
							echo '<th class="text-center">เวลาถึงลูกค้า</th>';
							echo '<th class="text-center">ประเภทรถ</th>';
							echo '<th class="text-center">ทะเบีบยรถ</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					
					$sql = "SELECT * FROM bs_deliveries_drivers WHERE delivery_id = ".$delivery['id'];
					$rst = $dbc->Query($sql);
					while($item = $dbc->Fetch($rst)){
						$employee = $dbc->GetRecord("bs_employees","*","id=".$item['emp_driver']);
						echo '<tr>';
							echo '<td><button class="btn btn-xs btn-danger" onclick="fn.app.delivery.delivery.remove_driver(this)" type="button">Remove</button></td>';
							echo '<td>';
								echo '<input type="hidden" name="emp_driver[]" value="'.$item['emp_driver'].'">';
								echo '<input type="hidden" xname="item_id" name="item_id[]" value="'.$item['id'].'">';
								echo '<input type="hidden" xname="action" name="action[]" value="">';
								echo '<input class="form-control form-control-sm" readonly value="'.$employee['fullname'].'">';
							echo '</td>';
							echo '<td><input type="time" name="time_departure[]" class="form-control form-control-sm" value="'.$item['time_departure'].'"></td>';
							echo '<td><input type="time" name="time_arrive[]" class="form-control form-control-sm" value="'.$item['time_arrive'].'"></td>';
							echo '<td>';
								echo '<select class="form-control form-control-sm" name="truck_type[]">';
									echo '<option value="">ไม่ระบุ</otpion>';
									foreach($aTruckType as $option){
										echo '<option'.($option == $item['truck_type']?" selected":"").'>'.$option.'</option>';
									}
								echo '</select>';
							echo '</td>';
							echo '<td>';
								echo '<select class="form-control form-control-sm" name="truck_license[]">';
									echo '<option value="">ไม่ระบุ</otpion>';
									foreach($aTruckLicense as $option){
										echo '<option'.($option == $item['truck_license']?" selected":"").'>'.$option.'</option>';
									}
								echo '</select>';
							echo '</td>';
						echo '</tr>';
					}
					
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
	echo '<hr>';
		echo '<div class="row">';
		echo '<div class="col-4">';
			echo '<div class="row">';
				echo '<div class="col-md-3 mb-1">';
					echo '<label>รอบการผลิต</label>';
					echo '<select name="round_filter" type="text" class="form-control" >';
						echo '<option value="">No Filter</option>';
						$sql = "SELECT DISTINCT bs_productions.id ,bs_productions.round 
						FROM bs_productions 
						LEFT OUTER JOIN bs_packing_items ON bs_productions.id = bs_packing_items.production_id 
						LEFT OUTER JOIN bs_delivery_pack_items ON bs_packing_items.id = bs_delivery_pack_items.item_id 
						WHERE bs_delivery_pack_items.delivery_id IS NULL AND bs_packing_items.status > -1 ORDER BY `round` DESC";
						$rst = $dbc->Query($sql);
						while($item = $dbc->Fetch($rst)){
							echo '<option value="'.$item['id'].'">'.$item['round'].'</option>';
						}
					echo '</select>';
				echo '</div>';
			
				echo '<div class="col-md-5">';
					echo '<label>เลือกจากหมายเลขถุง</label>';
					echo '<select name="code_search" type="text" class="form-control" placeholder="Product Item" onchange="fn.app.delivery.delivery.mapping()"></select>';
				echo '</div>';
				echo '<div class="col-md-1">';
					echo '<label class="text-white">No</label>';
						echo '<button onclick="fn.app.delivery.delivery.mapping()" class="btn btn-primary mr-2">Append</button>';
						//echo '<button onclick="fn.app.delivery.delivery.mapping()" class="btn btn-primary">Append</button>';
				echo '</div>';
			echo '</div>';
			
			echo '<div class="row">';
				echo '<div id="select_list_stock" class="col-md-12 form-inline"></div>';
				echo '<div class="col-md-12">';
					echo '<button onclick="fn.app.delivery.delivery.toggle_check()" class="btn btn-warning mr-2">Toggle</button>';
					echo '<button onclick="fn.app.delivery.delivery.append_bulk()" class="btn btn-primary mr-2">Append</button>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="col-8">';
			echo '<div>';
				echo '<table id="tblPackitem" class="table table-form table-sm table-bordered">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center">รหัสถุง</th>';
							echo '<th class="text-center">ประเภทถุง</th>';
							echo '<th class="text-center">รายการถุง</th>';
							echo '<th class="text-center">นำหนัก</th>';
							echo '<th class="text-center">น้ำหนักแพ็คเสร็จ</th>';
							echo '<th class="text-center">ดำเนินการ</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		echo '</div>';
		echo '</div>';
	echo '';


?>

		<ul class="list-group list-group-horizontal">
			<li class="list-group-item flex-fill text-center">
				<div class="text-secondary">Order Value</div><strong><?php echo number_format($delivery['amount'],4);?></strong>
			</li>
			<li class="list-group-item flex-fill text-center">
				<div class="text-secondary">Packing</div><strong id="amount_total">0.00</strong>
			</li>
			<li class="list-group-item flex-fill text-center">
				<div class="text-secondary">Remain</div><strong id="amount_remain">0.00</strong>
			</li>
		</ul>