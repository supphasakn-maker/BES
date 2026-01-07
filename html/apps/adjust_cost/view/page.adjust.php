<!DOCTYPE html>
<html lang="th">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">
	<title>Adjust Cost Report</title>

	<style>
		/*
        * CSS Global Styles & Base
        */
		body {
			color: #333;
			/* สีข้อความทั่วไป */
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			margin: 0;
			padding: 5px;
			/* คงระยะขอบ 5px ตามคำขอ */
			box-sizing: border-box;
			min-width: 320px;
			/* ป้องกัน layout เสียหายบนจอเล็กมากๆ */
			background-color: #f8f9fa;
			/* พื้นหลังสีอ่อน */
		}

		/*
        * Bootstrap-like Grid System (simplified)
        */
		.row {
			display: flex;
			flex-wrap: wrap;
			margin-right: -15px;
			margin-left: -15px;
		}

		.col-12,
		.col-5,
		.col-7,
		.col-6 {
			position: relative;
			width: 100%;
			padding-right: 15px;
			padding-left: 15px;
			box-sizing: border-box;
		}

		/*
        * Table General Styles
        */
		.table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 1.5rem;
			color: #212529;
			background-color: #fff;
			/* ลบ border-radius และ box-shadow ออกจาก .table โดยตรง */
		}

		.table th,
		.table td {
			padding: 0.8rem;
			vertical-align: middle;
			border-top: 1px solid #e9ecef;
			text-align: center;
		}

		.table thead th {
			vertical-align: bottom;
			border-bottom: 2px solid #e9ecef;
			font-weight: 600;
			color: #495057;
			background-color: #f2f2f2;
		}

		.table-striped tbody tr:nth-of-type(odd) {
			background-color: rgba(0, 0, 0, 0.03);
		}

		.table-hover tbody tr:hover {
			background-color: rgba(0, 0, 0, 0.06);
		}

		.table-bordered th,
		.table-bordered td {
			border: 1px solid #dee2e6;
		}

		.table-sm th,
		.table-sm td {
			padding: 0.5rem;
			font-size: 0.875rem;
		}

		.table-dark {
			background-color: #343a40 !important;
			color: #fff;
		}

		/*
        * Responsive Table Container (สำหรับเลื่อนตารางแนวนอนบนจอเล็ก)
        */
		.table-responsive-container {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			margin-bottom: 20px;
			border: 1px solid #dee2e6;
			/* ยังคง border ของ container ไว้ */
			border-radius: 8px;
			/* ยังคง rounded corners ของ container ไว้ */
			/* ลบ box-shadow ออกจาก container ด้วย */
		}

		.table-responsive-container>.table {
			margin-bottom: 0;
			border: none;
			/* ตารางใน container ไม่มี border ซ้ำซ้อน */
		}

		/*
        * Specific Adjustments for .table-middle
        */
		.table-middle th,
		.table-middle td {
			font-size: 0.95rem;
			padding: 10px 8px;
			white-space: nowrap;
		}

		/*
        * *** การแก้ไขสีไอคอน Font Awesome ***
        * กำหนดสีของไอคอนโดยตรงภายในปุ่ม
        */
		.btn .far {
			/* สำหรับไอคอน Font Awesome Regular (far) ภายในองค์ประกอบที่มีคลาส .btn */
			color: inherit;
			/* ไอคอนจะรับสีจากข้อความของปุ่ม */
			/* ถ้ายังเป็นสีเทา ลองใช้ !important: */
			/* color: #343a40 !important; /* สำหรับปุ่มสีเข้ม */
			/* color: #dc3545 !important; /* สำหรับปุ่มสีแดง */
		}

		/* หากใช้ Font Awesome 6.x Solid icon (fa-solid) */
		/* .btn .fa-solid {
            color: inherit;
        } */


		/*
        * Form Inline Styles
        */
		.form-inline {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			margin-bottom: 1.5rem;
			padding: 15px;
			background-color: #e9ecef;
			border-radius: 8px;
			/* ลบ box-shadow ออกจาก form-inline */
		}

		.form-inline label {
			margin-right: 1rem;
			margin-bottom: 0.5rem;
			font-weight: 500;
			color: #495057;
		}

		.form-inline .form-control {
			flex: 1;
			min-width: 180px;
			margin-right: 1rem;
			margin-bottom: 0.5rem;
			height: calc(1.5em + 0.75rem + 2px);
			padding: 0.375rem 0.75rem;
			font-size: 1rem;
			line-height: 1.5;
			color: #495057;
			background-color: #fff;
			border: 1px solid #ced4da;
			border-radius: 0.25rem;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		}

		.form-inline .form-control:focus {
			border-color: #80bdff;
			outline: 0;
			box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
		}

		.form-inline button {
			margin-bottom: 0.5rem;
			padding: 0.375rem 0.75rem;
			font-size: 1rem;
			line-height: 1.5;
			border-radius: 0.25rem;
			cursor: pointer;
			transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		}

		/* Bootstrap Button Styles (simplified) */
		.btn {
			display: inline-block;
			font-weight: 400;
			color: #212529;
			text-align: center;
			vertical-align: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			background-color: transparent;
			border: 1px solid transparent;
			line-height: 1.5;
			border-radius: 0.25rem;
		}

		/* Outline Buttons */
		.btn-outline-danger {
			color: #dc3545;
			border-color: #dc3545;
		}

		.btn-outline-danger:hover {
			color: #fff;
			background-color: #dc3545;
			border-color: #dc3545;
		}

		.btn-outline-dark {
			color: #343a40;
			border-color: #343a40;
		}

		.btn-outline-dark:hover {
			color: #fff;
			background-color: #343a40;
			border-color: #343a40;
		}

		/* Solid Buttons */
		.btn-warning {
			color: #212529;
			background-color: #ffc107;
			border-color: #ffc107;
		}

		.btn-warning:hover {
			color: #212529;
			background-color: #e0a800;
			border-color: #d39e00;
		}

		.btn-danger {
			color: #fff;
			background-color: #dc3545;
			border-color: #dc3545;
		}

		.btn-danger:hover {
			color: #fff;
			background-color: #c82333;
			border-color: #bd2130;
		}


		/*
        * Summary Table (.table-form) Specific Styles
        */
		.table-form {
			width: 100%;
			border-collapse: separate;
			border-spacing: 0;
			background-color: #fff;
			border-radius: 8px;
			/* ลบ box-shadow ออกจาก table-form */
			overflow: hidden;
		}

		.table-form th,
		.table-form td {
			padding: 10px 12px;
			font-size: 0.95rem;
			vertical-align: middle;
			border: 1px solid #e9ecef;
		}

		.table-form th {
			background-color: #f2f2f2;
			text-align: left;
			font-weight: 500;
			color: #343a40;
		}

		.table-form td {
			text-align: right;
		}

		.table-form tbody tr td input {
			width: 100%;
			border: none;
			background-color: transparent;
			text-align: right;
			padding: 0;
			font-size: inherit;
			color: inherit;
		}

		.table-form tbody tr td:empty {
			background-color: #f8f9fa;
		}


		/*
        * Horizontal Rules
        */
		hr {
			border: 0;
			height: 1px;
			background-color: #e9ecef;
			margin-top: 2rem;
			margin-bottom: 2rem;
		}

		/*
        * Media Queries for Responsive Layout
        */

		/* On screens smaller than 768px (most phones and some tablets in portrait) */
		@media (max-width: 767px) {
			body {
				padding: 5px;
				/* คงเดิม */
			}

			.col-5,
			.col-7,
			.col-6,
			.col-12 {
				flex: 0 0 100%;
				max-width: 100%;
				margin-bottom: 20px;
				padding-right: 15px;
				padding-left: 15px;
			}

			/* Adjustments for .table-form on small screens */
			.table-form tbody tr {
				display: flex;
				flex-wrap: wrap;
				border-bottom: 1px solid #dee2e6;
				padding-bottom: 10px;
				margin-bottom: 10px;
			}

			.table-form tbody tr:last-child {
				border-bottom: none;
				margin-bottom: 0;
			}

			.table-form tbody tr th,
			.table-form tbody tr td {
				flex: 0 0 100%;
				/* Each header/data takes full width */
				max-width: 100%;
				border: none;
				padding: 5px 0;
				font-size: 0.9rem;
			}

			.table-form tbody tr th {
				text-align: left;
				font-weight: bold;
				color: #555;
				background-color: transparent;
			}

			.table-form tbody tr td {
				text-align: right;
			}

			.table-form tbody tr td:empty {
				display: none;
			}

			.table-form .form-control-sm {
				text-align: right;
				background-color: #f8f9fa;
			}
		}

		/* --- สำหรับ iPad Pro โดยเฉพาะ (Portrait: 768px - 1024px) --- */
		@media (min-width: 768px) and (max-width: 1024px) {
			body {
				padding: 5px;
				/* คงเดิม */
			}

			/* กำหนดความกว้างคอลัมน์ให้เหมาะสมกับ iPad Pro */
			.col-5 {
				flex: 0 0 41.666667%;
				max-width: 41.666667%;
			}

			.col-7 {
				flex: 0 0 58.333333%;
				max-width: 58.333333%;
			}

			.col-6 {
				flex: 0 0 50%;
				max-width: 50%;
			}

			/* ปรับขนาด Font และ Padding สำหรับตารางหลัก (table-middle) บน iPad Pro */
			.table-middle th,
			.table-middle td {
				font-size: 0.8rem;
				/* ลดขนาด font เพื่อให้ข้อมูลแสดงผลได้เยอะขึ้น */
				padding: 6px 4px;
				/* ลด padding เพื่อให้ตารางกระชับขึ้น */
			}

			/* ปรับขนาด Font และ Padding สำหรับตารางสรุปผล (table-form) บน iPad Pro */
			.table-form th,
			.table-form td {
				padding: 8px 10px;
				font-size: 0.9rem;
			}

			/* ปรับความกว้างของคอลัมน์ในตารางสรุปผล (table-form) */
			.table-form tbody tr th:nth-child(odd) {
				width: 45%;
				/* ให้พื้นที่ข้อความมากขึ้น */
				white-space: normal;
				/* อนุญาตให้ข้อความขึ้นบรรทัดใหม่ */
				text-align: left;
			}

			.table-form tbody tr td:nth-child(even) {
				width: 25%;
				/* ปรับพื้นที่สำหรับค่าตัวเลข */
			}
		}

		/* For larger screens (iPad Pro Landscape, Desktop monitors like 24-inch) */
		@media (min-width: 1025px) {
			body {
				padding: 5px;
				/* คงเดิม */
			}

			/* ปรับขนาด Font และ Padding สำหรับตารางหลัก (table-middle) บนคอมพิวเตอร์ */
			.table-middle th,
			.table-middle td {
				font-size: 0.9rem;
				padding: 10px 8px;
			}

			/* ปรับขนาด Font และ Padding สำหรับตารางสรุปผล (table-form) บนคอมพิวเตอร์ */
			.table-form th,
			.table-form td {
				white-space: normal;
				font-size: 1rem;
				padding: 12px 15px;
			}

			/* ปรับความกว้างของคอลัมน์ในตารางสรุปผล (table-form) */
			.table-form tbody tr th:nth-child(odd) {
				width: 35%;
			}

			.table-form tbody tr td:nth-child(even) {
				width: 15%;
			}
		}
	</style>
</head>

<body>
	<!-- โค้ด HTML ส่วนที่ให้มาทั้งหมด -->
	<div class="btn-area btn-group mb-2"></div>
	<div class="row">
		<div class="col-5">
			<div class="table-responsive-container">
				<table id="tblPurchase" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center table-dark" colspan="9">Buy Side</th>
						</tr>
						<tr>
							<th class="text-center hidden-xs">
								<span type="checkall" control="chk_purchase" class="far fa-lg fa-square"></span>
							</th>
							<th class="text-center">Date</th>
							<th class="text-center">Supplier</th>
							<th class="text-center">Spot</th>
							<th class="text-center">Pmdc</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Spot Value</th>
							<th class="text-center">Discount Value</th>
							<th class="text-center">Net Spot Value</th>

						</tr>
					</thead>
					<tbody>
						<!-- Table content will be rendered here by your application -->
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-7">
			<div class="table-responsive-container">
				<table id="tblAdjusted" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center">Date</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Purchase</th>
							<th class="text-center">Sales</th>
							<th class="text-center">New</th>
							<th class="text-center">Profit From Trade</th>
							<th class="text-center">Ajust Cost</th>
							<th class="text-center">Ajust Discount</th>
							<th class="text-center">Net Profit</th>
							<th class="text-center">Supplier</th>
							<th class="text-center">Products</th>
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						<!-- Table content will be rendered here by your application -->
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<hr>
			<form name="adding" class="form-inline mr-2 " onsubmit="fn.app.adjust_cost.adjust.add();return false;">
				<button type="button" onclick="fn.app.adjust_cost.adjust.calcuate()" class="btn btn-warning mr-2">Calcuate</button>
				<label class="mr-sm-2">วันที่</label>
				<input name="date" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d"); ?>">
				<button type="submit" class="btn btn-danger">Match</button>

			</form>
			<hr>
			<div>
				<table class="table table-form table-sm table-bordered">
					<tbody>
						<tr>
							<th>1. ยอดส่วนต่างระหว่างราคาซื้อครั้งเก่าและใหม่ (PO เก่า - PO ใหม่ รวม discount)</th>
							<td><input class="form-control form-control-sm text-center" name="value_a" value="0" readonly></td>
							<th>Profit from trade at standard</th>
							<td><input class="form-control form-control-sm text-center" name="value_profit" value="0" readonly></td>
						</tr>
						<tr>
							<th>2. กำไรขาดทุนที่เกิดจากการ adj cost (Sell - PO ใหม่ ไม่รวม discount)</th>
							<td><input class="form-control form-control-sm text-center" name="value_b" value="0" readonly></td>
							<th>Cost decrease(increase) from adj cost (PO เก่า - PO ใหม่ ไม่รวม discount)</th>
							<td><input class="form-control form-control-sm text-center" name="cost_a" value="0" readonly></td>
						</tr>
						<tr>
							<th>3. กำไรขาดทุนเพิ่มจากการ discount (discount ใหม่ - discount เก่า)</th>
							<td><input class="form-control form-control-sm text-center" name="value_c" value="0" readonly></td>
							<th>Cost decrease(increase) from adj discount</th>
							<td><input class="form-control form-control-sm text-center" name="cost_b" value="0" readonly></td>
						</tr>
						<tr>
							<th>4. ยอด 1+2+3</th>
							<td><input class="form-control form-control-sm text-center" name="value_d" value="0" readonly></td>
							<th>Net profit</th>
							<td><input class="form-control form-control-sm text-center" name="value_netprofit" value="0" readonly></td>
						</tr>
						<tr>
							<th>Sell-PO เก่าไม่รวม discount</th>
							<td><input class="form-control form-control-sm text-center" name="value_e" value="0" readonly></td>
							<th></th>
							<td></td>
						</tr>
					</tbody>

				</table>
			</div>
			<hr>
		</div>
		<div class="col-6">
			<div class="table-responsive-container">
				<table id="tblSales" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center table-dark" colspan="9">Sell Side</th>
						</tr>
						<tr>
							<th class="text-center hidden-xs">
								<span type="checkall" control="chk_spot" class="far fa-lg fa-square"></span>
							</th>
							<th class="text-center">Date</th>
							<th class="text-center">Supplier</th>
							<th class="text-center">Spot</th>
							<th class="text-center">Pmdc</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Spot Value</th>
							<th class="text-center">Discount Value</th>
							<th class="text-center">Net Spot Value</th>
						</tr>
					</thead>
					<tbody>
						<!-- Table content will be rendered here by your application -->
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-6">
			<div class="table-responsive-container">
				<table id="tblPurchaseNew" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
					<thead>
						<tr>
							<th class="text-center table-dark" colspan="9">Buy Side</th>
						</tr>
						<tr>
							<th class="text-center hidden-xs">
								<span type="checkall" control="chk_new" class="far fa-lg fa-square"></span>
							</th>
							<th class="text-center">Date</th>
							<th class="text-center">Supplier</th>
							<th class="text-center">Spot</th>
							<th class="text-center">Pmdc</th>
							<th class="text-center">Amount</th>
							<th class="text-center">Spot Value</th>
							<th class="text-center">Discount Value</th>
							<th class="text-center">Net Spot Value</th>

						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>

</html>