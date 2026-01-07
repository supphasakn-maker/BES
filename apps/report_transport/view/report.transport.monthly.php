<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
		<table class="table table-sm table-bordered table-striped">
		<thead>
		<tr>
		<th colspan="10" class="text-center"><h1>มอเตอร์ไซต์</h1></th>
		</tr>

		<tr>
		<th class="text-center">วันที่จัดส่ง</th>
		<th class="text-center">ผู้ส่ง</th>
		<th class="text-center">Total</th>

		</tr>
		</thead>
		<tbody>
			<?php
			$aSum = array(0,0);
			$sql="SELECT COUNT(bs_employees.fullname) AS countall,bs_employees.fullname AS fullname , bs_deliveries.delivery_date AS delivery_date
			FROM bs_deliveries 
			LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
			LEFT JOIN bs_deliveries_drivers ON bs_deliveries.id = bs_deliveries_drivers.delivery_id
			LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
			WHERE bs_deliveries_drivers.truck_type LIKE '%มอ%' AND DATE_FORMAT(bs_orders.delivery_date,'%Y-%m') = '".$_POST['month']."'  AND bs_deliveries_drivers.emp_driver = '".$_POST['user_group_id']."' 
			AND  bs_orders.status > 0 GROUP BY delivery_date, fullname ORDER BY delivery_date ASC";
			$rst = $dbc->Query($sql);
			while($order = $dbc->Fetch($rst)){
			?>
			<tr>
			<td><?php echo $order['delivery_date'];?></td>
			<td class="text-center"><?php echo $order['fullname'];?></td>
			<td class="text-center"><?php echo $order['countall'];?></td>

			</tr>
		<?php
				$aSum[0] += 1;
				$aSum[1] += $order['countall'];
			}
			?>
		</tbody>
		<tfoot>
			<tr>
			<th class="text-center" colspan="1">ทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-center" colspan="1">รวม</th>
			<th class="text-center" colspan="1"><?php echo $aSum[1]; ?></th>
			</tr>
		</tfoot>
		</table>
        </div>
		<div class="col-sm-6">
		<table class="table table-sm table-bordered table-striped">
		<thead>
		<tr>
		<th colspan="10" class="text-center"><h1>รถยนต์</h1></th>
		</tr>

		<tr>
		<th class="text-center">วันที่จัดส่ง</th>
		<th class="text-center">ผู้ส่ง</th>
		<th class="text-center">Total</th>

		</tr>
		</thead>
		<tbody>
			<?php
			$aSum = array(0,0);
			$sql="SELECT COUNT(bs_employees.fullname) AS countall,bs_employees.fullname AS fullname ,bs_deliveries.delivery_date AS delivery_date
			FROM bs_deliveries 
			LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
			LEFT JOIN bs_deliveries_drivers ON bs_deliveries.id = bs_deliveries_drivers.delivery_id
			LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
			WHERE bs_deliveries_drivers.truck_type LIKE '%รถ%' AND DATE_FORMAT(bs_orders.delivery_date,'%Y-%m') = '".$_POST['month']."'  AND bs_deliveries_drivers.emp_driver = '".$_POST['user_group_id']."' 
			AND  bs_orders.status > 0 GROUP BY delivery_date, fullname ORDER BY delivery_date ASC";
			$rst = $dbc->Query($sql);
			while($order = $dbc->Fetch($rst)){
			?>
			<tr>
			<td><?php echo $order['delivery_date'];?></td>
			<td class="text-center"><?php echo $order['fullname'];?></td>
			<td class="text-center"><?php echo $order['countall'];?></td>

			</tr>
		<?php
				$aSum[0] += 1;
				$aSum[1] += $order['countall'];
			}	
			?>
		</tbody>
		<tfoot>
			<tr>
			<th class="text-center" colspan="1">ทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-center" colspan="1">รวม</th>
			<th class="text-center" colspan="1"><?php echo $aSum[1]; ?></th>
			</tr>
		</tfoot>
		</table>
        </div>
  </div>
</div>
