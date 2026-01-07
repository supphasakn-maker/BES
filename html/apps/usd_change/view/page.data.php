<style>
	:root {
		--primary-color: #00204E;
		--primary-gradient: linear-gradient(135deg, #00204E 0%, #003366 100%);
		--secondary-color: #003366;
		--accent-color: #0056b3;
		--light-bg: #f8f9fa;
		--border-color: #e9ecef;
		--text-muted: #6c757d;
		--success-color: #28a745;
		--warning-color: #ffc107;
		--danger-color: #dc3545;
	}

	.purchase-container {
		background: #fff;
		border-radius: 12px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
		overflow: hidden;
		margin-bottom: 2rem;
	}

	.form-section {
		background: var(--light-bg);
		padding: 2rem;
		border-right: 1px solid var(--border-color);
		height: 100%;
	}

	.form-header {
		background: var(--primary-gradient);
		color: white;
		padding: 1.5rem 2rem;
		margin: -2rem -2rem 2rem -2rem;
		border-radius: 12px 12px 0 0;
	}

	.form-header h4 {
		margin: 0;
		font-weight: 600;
		font-size: 1.0rem;
		display: flex;
		align-items: center;
	}

	.form-header i {
		margin-right: 12px;
		background: rgba(255, 255, 255, 0.2);
		padding: 8px;
		border-radius: 6px;
	}

	.table-form {
		background: white;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
		border: none;
		overflow: hidden;
	}

	.table-form tbody tr {
		border: none;
		transition: background-color 0.2s ease;
	}

	.table-form tbody tr:hover {
		background-color: rgba(0, 32, 78, 0.02);
	}

	.table-form td {
		border: none;
		padding: 1.5rem 1.2rem;
		vertical-align: middle;
		border-bottom: 1px solid #f0f0f0;
	}

	.table-form td:first-child {
		background: linear-gradient(90deg, rgba(0, 32, 78, 0.05) 0%, rgba(0, 32, 78, 0.02) 100%);
		font-weight: 600;
		color: var(--primary-color);
		width: 180px;
		border-right: 2px solid rgba(0, 32, 78, 0.1);
	}

	.table-form label {
		margin: 0;
		font-size: 0.9rem;
		color: var(--primary-color);
		font-weight: 600;
	}

	.form-control,
	.form-select {
		border: 2px solid #e9ecef;
		border-radius: 6px;
		padding: 0.75rem 1rem;
		font-size: 0.95rem;
		transition: all 0.3s ease;
		background: white;
	}

	.form-control:focus,
	.form-select:focus {
		border-color: var(--accent-color);
		box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
		outline: none;
	}

	.submit-btn {
		background: var(--primary-gradient);
		color: white;
		border: none;
		padding: 16px 24px;
		border-radius: 8px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s ease;
		width: 100%;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		min-height: 56px;
		margin-top: 24px;
		box-shadow: 0 4px 12px rgba(0, 32, 78, 0.3);
	}

	.submit-btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(0, 32, 78, 0.4);
	}

	.submit-btn:active {
		transform: translateY(0);
	}

	.data-section {
		background: white;
		padding: 2rem;
	}

	.data-header {
		background: var(--primary-gradient);
		color: white;
		padding: 1.5rem 2rem;
		margin: -2rem -2rem 2rem -2rem;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.data-header h4 {
		margin: 0;
		font-weight: 600;
		font-size: 1.0rem;
		display: flex;
		align-items: center;
	}

	.data-header i {
		margin-right: 12px;
		background: rgba(255, 255, 255, 0.2);
		padding: 8px;
		border-radius: 6px;
	}

	.data-stats {
		display: flex;
		gap: 1rem;
		font-size: 0.9rem;
		opacity: 0.9;
	}

	.data-stats span {
		background: rgba(255, 255, 255, 0.15);
		padding: 4px 8px;
		border-radius: 4px;
	}

	#tblUsd {
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
		border: none;
	}

	#tblUsd thead {
		background: var(--primary-gradient) !important;
	}

	#tblUsd thead th {
		border: none;
		font-weight: 600;
		font-size: 0.9rem;
		padding: 1rem 0.75rem;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		position: relative;
	}

	#tblUsd thead th:not(:last-child)::after {
		content: '';
		position: absolute;
		right: 0;
		top: 25%;
		height: 50%;
		width: 1px;
		background: rgba(255, 255, 255, 0.2);
	}

	#tblUsd tbody tr {
		transition: all 0.2s ease;
		border: none;
	}

	#tblUsd tbody tr:hover {
		background-color: rgba(0, 32, 78, 0.03);
		transform: scale(1.001);
	}

	#tblUsd tbody td {
		border: none;
		padding: 1rem 0.75rem;
		border-bottom: 1px solid #f0f0f0;
		font-size: 0.9rem;
	}

	#tblUsd tbody tr:last-child td {
		border-bottom: none;
	}

	.currency-badge {
		display: inline-block;
		padding: 4px 8px;
		border-radius: 4px;
		font-size: 0.8rem;
		font-weight: 600;
		text-transform: uppercase;
	}

	.currency-usd {
		background: rgba(40, 167, 69, 0.1);
		color: var(--success-color);
		border: 1px solid rgba(40, 167, 69, 0.2);
	}

	.currency-thb {
		background: rgba(0, 86, 179, 0.1);
		color: var(--accent-color);
		border: 1px solid rgba(0, 86, 179, 0.2);
	}

	.type-badge {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 4px;
		font-size: 0.8rem;
		font-weight: 500;
		text-transform: capitalize;
	}

	.type-physical {
		background: rgba(40, 167, 69, 0.1);
		color: var(--success-color);
	}

	.type-stock {
		background: rgba(0, 86, 179, 0.1);
		color: var(--accent-color);
	}

	.type-trade {
		background: rgba(255, 193, 7, 0.1);
		color: #856404;
	}

	.type-defer {
		background: rgba(220, 53, 69, 0.1);
		color: var(--danger-color);
	}

	.USDShow,
	.THBShow {
		transition: all 0.3s ease;
	}

	.fade-in {
		animation: fadeIn 0.3s ease-in;
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.text-right {
		text-align: right !important;
	}

	.text-center {
		text-align: center !important;
	}

	/* Responsive adjustments */
	@media (max-width: 768px) {

		.form-section,
		.data-section {
			padding: 1rem;
		}

		.form-header,
		.data-header {
			margin: -1rem -1rem 1.5rem -1rem;
			padding: 1rem;
		}

		.table-form td:first-child {
			width: auto;
		}
	}
</style>

<div class="btn-area btn-group mb-2"></div>
<div class="data-section">

	<div class="table-responsive">
		<table id="tblUsd" class="table table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center text-white hidden-xs">
						<span type="checkall" control="chk_usd" class="far fa-lg fa-square"></span>
					</th>
					<th class="text-center text-white">Confirm</th>
					<th class="text-center text-white">Purchase</th>
					<th class="text-center text-white">Start Date</th>
					<th class="text-center text-white">Duedate</th>
					<th class="text-center text-white">Amount</th>
					<th class="text-center text-white">Exchange</th>
					<th class="text-center text-white">Finance Rate</th>
					<th class="text-center text-white">Type</th>
					<th class="text-center text-white">Bank</th>
					<th class="text-center text-white">User</th>
					<th class="text-center text-white">Ref</th>
					<th class="text-center text-white">Comment</th>
					<th class="text-center text-white">Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
</a>