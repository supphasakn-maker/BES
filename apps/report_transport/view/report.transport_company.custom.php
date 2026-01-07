<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
		<table class="table table-sm table-bordered table-striped">
		<thead>
		<tr>
		<th colspan="6" class="text-center"><h1>มอเตอร์ไซต์</h1></th>
		</tr>

		<tr>
		<td class="text-center">วันที่จัดส่ง</td>
		<th class="text-center">ลูกค้า</th>
		<th class="text-center">ผู้ส่ง</th>
		<th class="text-center">เวลาไป</th>
		<th class="text-center">เวลากลับ</th>
		</tr>
		</thead>
		<tbody>

		<?php
		$aSum = array(0,0);
		$sql = "SELECT DISTINCT (bs_orders.customer_name) AS customer_name,
		bs_orders.delivery_date AS date, 
		 bs_deliveries_drivers.truck_type AS truck_type, 
		 bs_employees.fullname AS fullname, 
		 bs_deliveries_drivers.time_departure AS time_departure,
		 bs_deliveries_drivers.time_arrive AS time_arrive
		FROM bs_deliveries 
		LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
		LEFT JOIN bs_deliveries_drivers ON bs_deliveries.id = bs_deliveries_drivers.delivery_id
		LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
		WHERE  bs_orders.delivery_date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."' AND bs_deliveries_drivers.emp_driver = '".$_POST['user_group_id']."' 
		AND bs_deliveries_drivers.truck_type LIKE '%มอ%'
		AND  bs_orders.status > 0 ORDER BY bs_orders.delivery_date ASC";

		$rst_driver = $dbc->Query($sql);
		while($driver = $dbc->Fetch($rst_driver)){
		?>
		<tr>
		<td><?php echo $driver['date'];?></td>
		<td><?php echo $driver['customer_name'];?></td>
		<td><?php echo $driver['fullname'];?></td>
		<td><?php echo $driver['time_departure'];?></td>
		<td><?php echo $driver['time_arrive'];?></td>
		</tr>
		<?php	
			$aSum[0] += 1;
			$aSum[1] += 1 * 50;
		}

		?>
		</tbody>
		<tfoot>
			<tr>
			<th class="text-center" colspan="3">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-center" colspan="2">รวมเป็นเงิน <?php echo $aSum[1]; ?>  บาท</th>
			</tr>
		</tfoot>
		</table>
    </div>
    <div class="col-sm-6">
	<table class="table table-sm table-bordered table-striped">
		<thead>
		<tr>
		<th colspan="6" class="text-center"><h1>รถกระบะ</h1></th>
		</tr>

		<tr>
		<td class="text-center">วันที่จัดส่ง</td>
		<th class="text-center">ลูกค้า</th>
		<th class="text-center">ผู้ส่ง</th>
		<th class="text-center">เวลาไป</th>
		<th class="text-center">เวลากลับ</th>
		</tr>
		</thead>
		<tbody>

		<?php
		$aSum = array(0,0);
		$sql = "SELECT DISTINCT (bs_orders.customer_name) AS customer_name,
		bs_orders.delivery_date AS date, 
		bs_deliveries_drivers.truck_type AS truck_type, 
		bs_employees.fullname AS fullname, 
		bs_deliveries_drivers.time_departure AS time_departure,
		bs_deliveries_drivers.time_arrive AS time_arrive

		FROM bs_deliveries 
		LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
		LEFT JOIN bs_deliveries_drivers ON bs_deliveries.id = bs_deliveries_drivers.delivery_id
		LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
		WHERE  bs_orders.delivery_date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."' AND bs_deliveries_drivers.emp_driver = '".$_POST['user_group_id']."' 
		AND bs_deliveries_drivers.truck_type LIKE '%รถกระ%'
		AND  bs_orders.status > 0 ORDER BY bs_orders.delivery_date ASC";
		$rst_driver = $dbc->Query($sql);
		while($driver = $dbc->Fetch($rst_driver)){
		?>
		<tr>
		<td><?php echo $driver['date'];?></td>
		<td><?php echo $driver['customer_name'];?></td>
		<td><?php echo $driver['fullname'];?></td>
		<td><?php echo $driver['time_departure'];?></td>
		<td><?php echo $driver['time_arrive'];?></td>
		</tr>
		<?php	
			$aSum[0] += 1;
			$aSum[1] += 1 * 20;
		}

		?>
		</tbody>
		<tfoot>
			<tr>
			<th class="text-center" colspan="3">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-center" colspan="2">รวมเป็นเงิน  <?php echo $aSum[1]; ?> บาท</th>
			</tr>
		</tfoot>
		</table>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
		<table class="table table-sm table-bordered table-striped">
		<thead>
		<tr>
		<th colspan="6" class="text-center"><h1>มารับ</h1></th>
		</tr>

		<tr>
		<td class="text-center">วันที่จัดส่ง</td>
		<td class="text-center">เวลาจัดส่ง</td>
		<th class="text-center">ลูกค้า</th>
		<th class="text-center">ผู้ส่ง</th>
		<th class="text-center">เวลาไป</th>
		<th class="text-center">เวลากลับ</th>
		</tr>
		</thead>
		<tbody>

		<?php
		$aSum = array(0);
		$sql = "SELECT DISTINCT (bs_orders.customer_name) AS customer_name,
		 bs_orders.delivery_date AS date, bs_orders.delivery_time AS time, 
		 bs_deliveries_drivers.truck_type AS truck_type, 
		 bs_employees.fullname AS fullname, 
		 bs_deliveries_drivers.time_departure AS time_departure,
		 bs_deliveries_drivers.time_arrive AS time_arrive

		FROM bs_deliveries 
		LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
		LEFT JOIN bs_deliveries_drivers ON bs_deliveries.id = bs_deliveries_drivers.delivery_id
		LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
		WHERE  bs_orders.delivery_date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."' AND bs_deliveries_drivers.emp_driver = '".$_POST['user_group_id']."' 
		AND bs_deliveries_drivers.truck_type LIKE '%มารับ%'
		AND  bs_orders.status > 0 ORDER BY bs_orders.delivery_date ASC";
		$rst_driver = $dbc->Query($sql);
		while($driver = $dbc->Fetch($rst_driver)){
		?>
		<tr>
		<td><?php echo $driver['date'];?></td>
		<td><?php echo $driver['time'];?></td>
		<td><?php echo $driver['customer_name'];?></td>
		<td><?php echo $driver['fullname'];?></td>
		<td><?php echo $driver['time_departure'];?></td>
		<td><?php echo $driver['time_arrive'];?></td>
		</tr>
		<?php	
			$aSum[0] += 1;
		}

		?>
		</tbody>
		<tfoot>
			<tr>
			<th class="text-center" colspan="6">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			</tr>
		</tfoot>
		</table>
    </div>
    <div class="col-sm-6">
	<table class="table table-sm table-bordered table-striped">
		<thead>
		<tr>
		<th colspan="6" class="text-center"><h1>ไม่ลงข้อมูล</h1></th>
		</tr>

		<tr>
		<td class="text-center">วันที่จัดส่ง</td>
		<td class="text-center">เวลาจัดส่ง</td>
		<th class="text-center">ลูกค้า</th>
		<th class="text-center">ผู้ส่ง</th>
		<th class="text-center">เวลาไป</th>
		<th class="text-center">เวลากลับ</th>
		</tr>
		</thead>
		<tbody>

		<?php
		$aSum = array(0);
		$sql = "SELECT
		DISTINCT (bs_orders.customer_name) AS customer_name,
		bs_orders.delivery_date AS date,
		bs_orders.delivery_time AS time,
		bs_orders.code AS order_code,
		bs_deliveries.code AS delivery_code,
		bs_deliveries.id AS delivery_id,
		bs_deliveries_drivers.truck_type AS truck_type,
		bs_employees.fullname AS fullname,
		bs_deliveries_drivers.time_departure AS time_departure,
		bs_deliveries_drivers.time_arrive AS time_arrive

		FROM bs_deliveries 
		LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
		LEFT JOIN bs_deliveries_drivers ON bs_deliveries.id = bs_deliveries_drivers.delivery_id
		LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
		WHERE  bs_orders.delivery_date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."' AND bs_deliveries_drivers.emp_driver = '".$_POST['user_group_id']."' 
		AND bs_deliveries_drivers.truck_type = ''
		AND  bs_orders.status > 0 ORDER BY bs_orders.delivery_date ASC";
		$rst_driver = $dbc->Query($sql);
		while($driver = $dbc->Fetch($rst_driver)){
		?>
		<tr>
		<td><?php echo $driver['date'];?></td>
		<td><?php echo $driver['time'];?></td>
		<td><?php echo $driver['customer_name'];?></td>
		<td><?php echo $driver['fullname'];?></td>
		<td><?php echo $driver['time_departure'];?></td>
		<td><?php echo $driver['time_arrive'];?></td>
		</tr>
		<?php	
			$aSum[0] += 1;
		}

		?>
		</tbody>
		<tfoot>
			<tr>
			<th class="text-center" colspan="6">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			</tr>
		</tfoot>
		</table>
    </div>
  </div>
</div>






