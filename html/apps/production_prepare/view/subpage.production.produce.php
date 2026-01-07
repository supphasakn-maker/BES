	
	<div class="col-6">
	<table id="form-second-process" class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>ปะการังออกจากเซฟ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_safe","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>ปะการังออกจากเซฟ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_safe","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_plate","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_plate","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_nugget","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_nugget","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blacknugget","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blacknugget","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_whitedust","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_whitedust","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blackdust","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blackdust","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>Refine</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_refine","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>Refine</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_refine","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 1</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_1","value"=>0.00,"class"=>"text-right"));?></td>
						<td><label>นน. Packing รวม</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_packing","value"=>0.00,"class"=>"text-right"));?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 2</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_2","value"=>0.00,"class"=>"text-right"));?></td>
						<td class="bg-warning"><label>รวม นน. ออกทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_total","value"=>0.00,"class"=>"text-right","readonly"=>true));?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 3</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_3","value"=>0.00,"class"=>"text-right"));?></td>
						<td class="bg-danger"><label>Processing Loss</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_margin","value"=>0.00,"class"=>"text-right","readonly"=>true));?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 4</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_4","value"=>0.00,"class"=>"text-right"));?></td>
						<td rowspan="2"><label>Remark</label></td>
						<td rowspan="2"><?php $ui_form->EchoItem(array("name" => "remark","type"=>"textarea"));?></td>
					</tr>
					<tr>
						<td class="bg-warning"><label>รวม นน. เข้าทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_total","value"=>0.00,"class"=>"text-right","readonly"=>true));?></td>
					</tr>
					<tr>
						
					</tr>
				</tbody>
			</table>
		</div>