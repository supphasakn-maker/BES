<style>
	:root {
		--primary-color: #00204E;
		--primary-light: #003d7a;
		--white: #ffffff;
		--light-gray: #f8f9fa;
		--medium-gray: #e9ecef;
		--dark-gray: #6c757d;
		--success: #28a745;
		--warning: #ffc107;
		--danger: #dc3545;
	}

	body {
		background: var(--light-gray);
	}

	.page-container {
		background: var(--white);
		border-radius: 15px;
		box-shadow: 0 10px 30px rgba(0, 32, 78, 0.15);
		padding: 0;
		overflow: hidden;
		margin: 20px auto;
		max-width: 1700px;
	}

	.page-header {
		background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
		color: var(--white);
		padding: 25px 30px;
		position: relative;
		overflow: hidden;
	}

	.page-header::before {
		content: '';
		position: absolute;
		top: -50%;
		left: -50%;
		width: 200%;
		height: 200%;
		background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
		animation: shimmer 4s ease-in-out infinite;
		pointer-events: none;
	}

	@keyframes shimmer {

		0%,
		100% {
			transform: translateX(-100%) translateY(-100%) rotate(0deg);
		}

		50% {
			transform: translateX(0%) translateY(0%) rotate(180deg);
		}
	}

	.page-title {
		font-size: 2rem;
		font-weight: 700;
		margin: 0;
		position: relative;
		z-index: 1;
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.page-title i {
		font-size: 1.0rem;
	}

	.page-content {
		padding: 30px;
	}

	/* Filter Section */
	.btn-area {
		background: linear-gradient(135deg, var(--light-gray) 0%, var(--medium-gray) 100%);
		border-radius: 12px;
		padding: 25px;
		margin-bottom: 25px;
		border-left: 5px solid var(--primary-color);
		box-shadow: 0 5px 15px rgba(0, 32, 78, 0.1);
	}

	.filter-title {
		color: var(--primary-color);
		font-weight: 700;
		font-size: 1.1rem;
		margin-bottom: 15px;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.form-inline {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 15px;
	}

	.form-inline label {
		color: var(--primary-color);
		font-weight: 600;
		font-size: 0.95rem;
		margin: 0;
		white-space: nowrap;
	}

	.form-control {
		border: 2px solid var(--medium-gray);
		border-radius: 8px;
		padding: 10px 15px;
		font-size: 1rem;
		transition: all 0.3s ease;
		background: var(--white);
		min-width: 160px;
	}

	.form-control:focus {
		border-color: var(--primary-color);
		box-shadow: 0 0 0 3px rgba(0, 32, 78, 0.15);
		outline: none;
		transform: translateY(-1px);
	}

	.form-control:hover {
		border-color: var(--primary-color);
	}

	.btn {
		padding: 10px 20px;
		border-radius: 8px;
		font-weight: 600;
		font-size: 0.95rem;
		transition: all 0.3s ease;
		border: none;
		cursor: pointer;
		display: inline-flex;
		align-items: center;
		gap: 8px;
	}

	.btn-warning {
		background: linear-gradient(135deg, var(--warning) 0%, #ffb300 100%);
		color: #333;
		border: 2px solid transparent;
	}

	.btn-warning:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 16px rgba(255, 193, 7, 0.3);
		background: linear-gradient(135deg, #ffb300 0%, var(--warning) 100%);
	}

	/* Table Styling */
	.table-container {
		background: var(--white);
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 8px 25px rgba(0, 32, 78, 0.1);
		border: 1px solid var(--medium-gray);
	}

	.table {
		margin: 0;
		border-collapse: separate;
		border-spacing: 0;
	}

	.table thead th {
		background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
		color: var(--white);
		font-weight: 700;
		font-size: 0.9rem;
		padding: 15px 12px;
		border: none;
		text-align: center;
		vertical-align: middle;
		white-space: nowrap;
		position: relative;
	}

	.table thead th:first-child {
		border-radius: 0;
	}

	.table thead th:last-child {
		border-radius: 0;
	}

	.table thead th::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		height: 2px;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
	}

	.table tbody td {
		padding: 12px;
		vertical-align: middle;
		border-bottom: 1px solid var(--medium-gray);
		font-size: 0.9rem;
		color: #333;
		transition: all 0.3s ease;
	}

	.table tbody tr {
		transition: all 0.3s ease;
	}

	.table tbody tr:hover {
		background: rgba(0, 32, 78, 0.05);
		transform: translateY(-1px);
		box-shadow: 0 5px 15px rgba(0, 32, 78, 0.1);
	}

	.table tbody tr:nth-child(even) {
		background: rgba(248, 249, 250, 0.5);
	}

	.table tbody tr:nth-child(even):hover {
		background: rgba(0, 32, 78, 0.05);
	}

	/* Checkbox Styling */
	.far.fa-square,
	.fas.fa-check-square {
		color: var(--primary-color);
		cursor: pointer;
		transition: all 0.3s ease;
		font-size: 1.2rem;
	}

	.far.fa-square:hover,
	.fas.fa-check-square:hover {
		color: var(--primary-light);
		transform: scale(1.1);
	}

	/* Badge Styling */
	.badge {
		padding: 6px 12px;
		border-radius: 20px;
		font-size: 0.8rem;
		font-weight: 600;
		letter-spacing: 0.5px;
	}

	.badge-primary {
		background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
		color: var(--white);
	}

	.badge-success {
		background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
		color: var(--white);
	}

	.badge-danger {
		background: linear-gradient(135deg, var(--danger) 0%, #e74c3c 100%);
		color: var(--white);
	}

	/* Button in Table */
	.btn-xs {
		padding: 5px 10px;
		font-size: 0.8rem;
		border-radius: 6px;
	}

	.btn-outline-primary {
		border: 2px solid var(--primary-color);
		color: var(--primary-color);
		background: transparent;
	}

	.btn-outline-primary:hover {
		background: var(--primary-color);
		color: var(--white);
		transform: translateY(-1px);
	}

	.btn-outline-success {
		border: 2px solid var(--success);
		color: var(--success);
		background: transparent;
	}

	.btn-outline-success:hover {
		background: var(--success);
		color: var(--white);
		transform: translateY(-1px);
	}

	.btn-outline-danger {
		border: 2px solid var(--danger);
		color: var(--danger);
		background: transparent;
	}

	.btn-outline-danger:hover {
		background: var(--danger);
		color: var(--white);
		transform: translateY(-1px);
	}

	/* DataTable specific */
	.dataTables_wrapper {
		padding: 0;
	}

	.dataTables_wrapper .dataTables_length,
	.dataTables_wrapper .dataTables_filter,
	.dataTables_wrapper .dataTables_info,
	.dataTables_wrapper .dataTables_paginate {
		padding: 15px;
		color: var(--primary-color);
	}

	.dataTables_wrapper .dataTables_paginate .paginate_button {
		padding: 8px 12px;
		margin: 0 2px;
		border-radius: 6px;
		border: 2px solid var(--medium-gray);
		color: var(--primary-color);
		background: var(--white);
		transition: all 0.3s ease;
	}

	.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
		border-color: var(--primary-color);
		background: var(--primary-color);
		color: var(--white);
		transform: translateY(-1px);
	}

	.dataTables_wrapper .dataTables_paginate .paginate_button.current {
		background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
		color: var(--white);
		border-color: var(--primary-color);
	}

	/* Loading Animation */
	.dataTables_processing {
		background: rgba(0, 32, 78, 0.9);
		color: var(--white);
		border-radius: 8px;
		padding: 20px;
		font-weight: 600;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.page-container {
			margin: 10px;
			border-radius: 10px;
		}

		.page-content {
			padding: 20px;
		}

		.form-inline {
			flex-direction: column;
			align-items: stretch;
		}

		.form-control {
			min-width: auto;
			width: 100%;
			margin-bottom: 10px;
		}

		.btn {
			width: 100%;
			justify-content: center;
		}

		.table-responsive {
			border-radius: 8px;
		}

		.page-title {
			font-size: 1.0rem;
		}
	}

	/* Animation */
	.fade-in {
		animation: fadeIn 0.6s ease-in;
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(20px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}
</style>
</head>

<body>
	<?php global $dbc;
	if ($this->GetSection() == "edit") {
		include "view/page.edit.php";
	} else { ?>

		<div class="page-container fade-in">
			<div class="page-header">
				<h4 class="page-title">
					<i class="fas fa-truck"></i>
					เตรียมส่งของ
				</h4>
			</div>

			<div class="page-content">
				<div class="btn-area">
					<form name="filter" class="form-inline" onsubmit="return 0;">
						<label class="mr-sm-2">วันที่เริ่มต้น</label>
						<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", time() - (7 * 86400)); ?>">

						<label class="mr-sm-2">วันที่สิ้นสุด</label>
						<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d", time() + (7 * 86400)); ?>">

						<button type="button" class="btn btn-warning" onclick='$("#tblDelivery").DataTable().draw();'>
							<i class="fas fa-search"></i>
							ค้นหา
						</button>
					</form>
				</div>

				<div class="table-container">
					<div class="table-responsive">
						<table id="tblDelivery" class="table table-striped table-bordered table-hover table-middle" width="100%">
							<thead>
								<tr>
									<th class="text-center">
										<span type="checkall" control="chk_delivery" class="far fa-lg fa-square"></span>
									</th>
									<th class="text-center">Delivery No.</th>
									<th class="text-center">Order No.</th>
									<th class="text-center">ประเภท</th>
									<th class="text-center">ชื่อลูกค้า</th>
									<th class="text-center">แท่ง</th>
									<th class="text-center">ราคาขาย</th>
									<th class="text-center">ราคาขาย - ส่วนลด</th>
									<th class="text-center">วันที่สั่งซื้อ</th>
									<th class="text-center">วันที่ส่ง</th>
									<th class="text-center">เงื่อนไขการชำระเงิน</th>
									<th class="text-center">บิล</th>
									<th class="text-center">สถานะ</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>