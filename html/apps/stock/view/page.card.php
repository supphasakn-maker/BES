<?php
@ini_set('display_errors', 1);
require_once "../../config/define.php";
require_once "../../include/db.php";

$dbc = new dbc;
$dbc->Connect();

/* ====== พารามิเตอร์แบบเดิม ====== */
$defaultDays = 15;
$view  = $_GET['view'] ?? 'card';
$year  = isset($_GET['year']) ? (int)$_GET['year'] : null;
$from  = $_GET['from'] ?? date('Y-m-d', strtotime('-' . ($defaultDays - 1) . ' days'));
$days  = isset($_GET['days']) ? max(1, min(365, (int)$_GET['days'])) : $defaultDays;

/* โหมดรายปี */
if ($year) {
	$from = sprintf('%04d-01-01', $year);
	$endOfYear = sprintf('%04d-12-31', $year);
	$days = (int)((strtotime($endOfYear) - strtotime($from)) / 86400) + 1; // รวมวันสุดท้าย
}

/* ช่วงที่แสดง (visible window) */
$today          = strtotime($from);
$totaldate      = $days - 1;
$start_date_str = date('Y-m-d', $today);
$end_date_str   = date('Y-m-d', strtotime("$from +$totaldate day"));
$start_date_dt  = "$start_date_str 00:00:00";
$end_exclusive  = date('Y-m-d', strtotime("$end_date_str +1 day")) . ' 00:00:00';
$yesterday_str  = date('Y-m-d', strtotime("$from -1 day"));

/* ====== ตั้งค่าสินค้า & ยอดตั้งต้น ====== */
$PID      = [1, 3, 4, 5, 2, 6, 7, 8];
$aBalance = [904, 1210.8493, 0.009, 1.3003, 42, 0, 0, 0]; // ยอดตั้งต้นตามตารางเดิม
$aProduct = [];
$sqlProducts = "SELECT * FROM bs_products WHERE id IN (" . implode(',', $PID) . ")
               ORDER BY FIELD(id," . implode(',', $PID) . ")";
$rs = $dbc->Query($sqlProducts);
while ($r = $dbc->Fetch($rs)) $aProduct[] = $r;
if (count($aProduct) < count($PID)) {
	$have = array_column($aProduct, 'id');
	foreach ($PID as $pid) if (!in_array($pid, $have, true)) $aProduct[] = ['id' => $pid, 'name' => 'Unknown Product'];
}

/* ====== helpers ====== */
function fetch_data($dbc, $sql)
{
	$r = $dbc->Query($sql);
	$o = [];
	while ($x = $dbc->Fetch($r)) $o[] = $x;
	return $o;
}
function map_sum_by_pid($rows, $pidKey = 'product_id', $valKey = 't')
{
	$o = [];
	foreach ($rows as $r) $o[(int)$r[$pidKey]] = (float)$r[$valKey];
	return $o;
}
function map_daily($rows, $pidKey = 'product_id', $dateKey = 'd', $valKey = 't')
{
	$o = [];
	foreach ($rows as $r) $o[(int)$r[$pidKey]][$r[$dateKey]] = (float)$r[$valKey];
	return $o;
}

/* =======================================================================
   A) คำนวณ “อดีตก่อนช่วงที่แสดง” (OFFSETS) ให้ BF ตรง
   ======================================================================= */
$history_start = strtotime("2022-05-05"); // วันเริ่มระบบ (ปรับได้ถ้ามี config)
$history_start_dt = date('Y-m-d', $history_start) . ' 00:00:00';
$visible_start_dt = $start_date_dt;

/* ผลิต / รับเข้าจากโรงอื่น / รับแท่ง / PMR / Orders (ก่อนช่วงแสดง) */
$pre_prod    = map_sum_by_pid(fetch_data($dbc, "
  SELECT product_id, SUM(weight_out_packing) AS t
  FROM bs_productions
  WHERE submited >= '$history_start_dt' AND submited < '$visible_start_dt'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id
"));
$pre_prod_in = map_sum_by_pid(fetch_data($dbc, "
  SELECT product_id, SUM(weight_out_total) AS t
  FROM bs_productions_in
  WHERE submited >= '$history_start_dt' AND submited < '$visible_start_dt'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id
"));
$pre_silver  = map_sum_by_pid(fetch_data($dbc, "
  SELECT product_id, SUM(weight_actual) AS t
  FROM bs_stock_silver
  WHERE submited >= '$history_start_dt' AND submited < '$visible_start_dt'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id
"));
$pre_pmr     = map_sum_by_pid(fetch_data($dbc, "
  SELECT product_id, SUM(weight_out_packing) AS t
  FROM bs_productions_pmr
  WHERE submited >= '$history_start_dt' AND submited < '$visible_start_dt'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id
"));
$pre_orders  = map_sum_by_pid(fetch_data($dbc, "
  SELECT product_id, SUM(amount) AS t
  FROM bs_orders
  WHERE delivery_date >= '$history_start_dt' AND delivery_date < '$visible_start_dt'
    AND status > 0
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id
"));

/* รายการปรับสต็อก */
$deposit_all   = fetch_data($dbc, "SELECT id,name FROM bs_stock_adjuest_types WHERE type=1 ORDER BY sort_id");
$deposit_all_ids = array_map('intval', array_column($deposit_all, 'id'));
$deposit_affect_ids = array_map(
	'intval',
	array_column(fetch_data($dbc, "SELECT id FROM bs_stock_adjuest_types WHERE type=1 AND id!=9"), 'id')
);
$memo_types = fetch_data($dbc, "SELECT id,name FROM bs_stock_adjuest_types WHERE type=2 ORDER BY sort_id");
$memo_ids   = array_map('intval', array_column($memo_types, 'id'));
$luck_types = fetch_data($dbc, "SELECT id,name FROM bs_stock_adjuest_types WHERE type=3 ORDER BY sort_id");
$luck_ids   = array_map('intval', array_column($luck_types, 'id'));

/* deposit ที่กระทบ balance (type=1 id!=9) ก่อนช่วงแสดง */
$pre_deposit_balance = [];
if ($deposit_affect_ids) {
	$pre_deposit_balance = map_sum_by_pid(fetch_data($dbc, "
    SELECT product_id, SUM(amount) AS t
    FROM bs_stock_adjusted
    WHERE date >= '$history_start_dt' AND date < '$visible_start_dt'
      AND type_id IN (" . implode(',', $deposit_affect_ids) . ")
      AND product_id IN (" . implode(',', $PID) . ")
    GROUP BY product_id
  "));
}

/* deposit offsets (ทุก id ของ type=1) */
$offset_deposit = []; // [pid][type_id]
if ($deposit_all_ids) {
	foreach (
		fetch_data($dbc, "
    SELECT product_id, type_id, SUM(amount) AS t
    FROM bs_stock_adjusted
    WHERE date >= '$history_start_dt' AND date < '$visible_start_dt'
      AND type_id IN (" . implode(',', $deposit_all_ids) . ")
      AND product_id IN (" . implode(',', $PID) . ")
    GROUP BY product_id, type_id
  ") as $r
	) {
		$offset_deposit[(int)$r['product_id']][(int)$r['type_id']] = (float)$r['t'];
	}
}

/* memo offsets */
$offset_memo = []; // [pid][type_id]
if ($memo_ids) {
	foreach (
		fetch_data($dbc, "
    SELECT product_id, type_id, SUM(amount) AS t
    FROM bs_stock_adjusted
    WHERE date >= '$history_start_dt' AND date < '$visible_start_dt'
      AND type_id IN (" . implode(',', $memo_ids) . ")
      AND product_id IN (" . implode(',', $PID) . ")
    GROUP BY product_id, type_id
  ") as $r
	) {
		$offset_memo[(int)$r['product_id']][(int)$r['type_id']] = (float)$r['t'];
	}
}

/* luck offsets */
$offset_luck = []; // [pid][type_id]
if ($luck_ids) {
	foreach (
		fetch_data($dbc, "
    SELECT product_id, type_id, SUM(amount) AS t
    FROM bs_stock_adjusted
    WHERE date >= '$history_start_dt' AND date < '$visible_start_dt'
      AND type_id IN (" . implode(',', $luck_ids) . ")
      AND product_id IN (" . implode(',', $PID) . ")
    GROUP BY product_id, type_id
  ") as $r
	) {
		$offset_luck[(int)$r['product_id']][(int)$r['type_id']] = (float)$r['t'];
	}
}

/* type 12 amount2 offset */
$type12_offset = map_sum_by_pid(fetch_data($dbc, "
  SELECT product_id, SUM(amount2) AS t
  FROM bs_stock_adjusted
  WHERE date >= '$history_start_dt' AND date < '$visible_start_dt'
    AND type_id = 12
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id
"));

/* รวม “อดีตก่อนช่วงแสดง” เข้า aBalance เริ่มต้น (ไม่รวม type12/memo/luck) */
foreach ($PID as $i => $pid) {
	$aBalance[$i] += ($pre_prod[$pid]    ?? 0)
		+ ($pre_prod_in[$pid] ?? 0)
		+ ($pre_silver[$pid]  ?? 0)
		+ ($pre_deposit_balance[$pid] ?? 0)
		- ($pre_pmr[$pid]     ?? 0)
		- ($pre_orders[$pid]  ?? 0);
}

/* =======================================================================
   B) โหลด “ข้อมูลรายวันภายในช่วงแสดง”
   ======================================================================= */
/* ผลิต */
$production_data = map_daily(fetch_data($dbc, "
  SELECT product_id, DATE(submited) AS d, SUM(weight_out_packing) AS t
  FROM bs_productions
  WHERE submited >= '$start_date_dt' AND submited < '$end_exclusive'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id, DATE(submited)
"));
/* รับเข้าจากโรงอื่น */
$production_in_data = map_daily(fetch_data($dbc, "
  SELECT product_id, DATE(submited) AS d, SUM(weight_out_total) AS t
  FROM bs_productions_in
  WHERE submited >= '$start_date_dt' AND submited < '$end_exclusive'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id, DATE(submited)
"));
/* รับเข้าแท่ง-อื่นๆ */
$stock_silver_data = map_daily(fetch_data($dbc, "
  SELECT product_id, DATE(submited) AS d, SUM(weight_actual) AS t
  FROM bs_stock_silver
  WHERE submited >= '$start_date_dt' AND submited < '$end_exclusive'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id, DATE(submited)
"));
/* PMR */
$pmr_data = map_daily(fetch_data($dbc, "
  SELECT product_id, DATE(submited) AS d, SUM(weight_out_packing) AS t
  FROM bs_productions_pmr
  WHERE submited >= '$start_date_dt' AND submited < '$end_exclusive'
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id, DATE(submited)
"));
/* Orders */
$orders_data = map_daily(fetch_data($dbc, "
  SELECT product_id, DATE(delivery_date) AS d, SUM(amount) AS t
  FROM bs_orders
  WHERE delivery_date >= '$start_date_dt' AND delivery_date < '$end_exclusive'
    AND status > 0
    AND product_id IN (" . implode(',', $PID) . ")
  GROUP BY product_id, DATE(delivery_date)
"));

/* Adjustments ภายในช่วงแสดง (สำหรับแสดง/อัปเดต running) */
$adjustments_data = []; // [pid][type_id][Y-m-d] = ['amount'=>.., 'amount2'=>..]
$all_type_ids = array_values(array_unique(array_merge($deposit_all_ids, $memo_ids, $luck_ids, [12])));
if ($all_type_ids) {
	foreach (
		fetch_data($dbc, "
    SELECT product_id, type_id, DATE(date) AS d, SUM(amount) AS a, SUM(amount2) AS a2
    FROM bs_stock_adjusted
    WHERE date >= '$start_date_dt' AND date < '$end_exclusive'
      AND product_id IN (" . implode(',', $PID) . ")
      AND type_id IN (" . implode(',', $all_type_ids) . ")
    GROUP BY product_id, type_id, DATE(date)
  ") as $r
	) {
		$pid = (int)$r['product_id'];
		$tid = (int)$r['type_id'];
		$d = $r['d'];
		$adjustments_data[$pid][$tid][$d] = ['amount' => (float)$r['a'], 'amount2' => (float)$r['a2']];
	}
}

/* =======================================================================
   C) เตรียมตัวแปรผลลัพธ์ & Running เริ่มต้นจาก offsets
   ======================================================================= */
$aDate = [];
$aTotal = [];
$aaData_BF = [];
$aaData_In = [];
$aaData_SB = [];
$aaData_MR = [];
$aaData_Out = [];
$aaData_CF = [];
$aaData_Total = [];
$aaData_MemoSum = [];
$aaData_Type12_Amount2 = [];

foreach ($PID as $pid) {
	$aaData_BF[$pid] = [];
	$aaData_In[$pid] = [];
	$aaData_SB[$pid] = [];
	$aaData_MR[$pid] = [];
	$aaData_Out[$pid] = [];
	$aaData_CF[$pid] = [];
	$aaData_Total[$pid] = [];
	$aaData_MemoSum[$pid] = [];
	$aaData_Type12_Amount2[$pid] = [];
}

/* ตารางแสดงสะสม Deposit/Luck/Memo */
$aaaDeposit = [];
foreach ($deposit_all as $t) {
	$aaaDeposit[] = [$t];
	foreach ($PID as $pid) $aaaDeposit[count($aaaDeposit) - 1][$pid] = [];
}
$aaaLuck = [];
foreach ($luck_types as $t) {
	$aaaLuck[] = [$t];
	foreach ($PID as $pid) $aaaLuck[count($aaaLuck) - 1][$pid] = [];
}
$aaaMemo = [];
foreach ($memo_types as $t) {
	$aaaMemo[] = [$t];
	foreach ($PID as $pid) $aaaMemo[count($aaaMemo) - 1][$pid] = [];
}

/* Running (ตั้งต้น = offsets ก่อนช่วงแสดง) */
$runningType12 = [];
$runningDeposit = [];
$runningLuckgems = [];
$runningMemo = [];
foreach ($PID as $pid) {
	$runningType12[$pid] = $type12_offset[$pid] ?? 0.0;

	foreach ($deposit_all as $t) {
		$tid = (int)$t['id'];
		$runningDeposit[$pid][$tid] = $offset_deposit[$pid][$tid] ?? 0.0;
	}
	foreach ($luck_types as $t) {
		$tid = (int)$t['id'];
		$runningLuckgems[$pid][$tid] = $offset_luck[$pid][$tid] ?? 0.0;
	}
	foreach ($memo_types as $t) {
		$tid = (int)$t['id'];
		$runningMemo[$pid][$tid] = $offset_memo[$pid][$tid] ?? 0.0;
	}
}

/* =======================================================================
   D) วนรายวัน (BF ของวันนี้ = aBalance + runningType12(ถึงเมื่อวาน))
   ======================================================================= */
for ($i = 0; $i < $days; $i++) {
	$date = date('Y-m-d', strtotime("$from +$i day"));
	$aDate[] = strtotime($date);
	$sum_out_today = 0.0;

	foreach ($PID as $j => $pid) {
		/* BF: balance ณ ต้นวัน + type12 สะสมถึงวันก่อนหน้า */
		$aaData_BF[$pid][] = $aBalance[$j] + $runningType12[$pid];

		/* เข้า (ผลิต + รับเข้าจากโรงอื่น) */
		$in_today = ($production_data[$pid][$date] ?? 0) + ($production_in_data[$pid][$date] ?? 0);
		$aBalance[$j] += $in_today;
		$aaData_In[$pid][] = $in_today;

		/* รับแท่ง-อื่นๆ */
		$sb_today = ($stock_silver_data[$pid][$date] ?? 0);
		$aBalance[$j] += $sb_today;
		$aaData_SB[$pid][] = $sb_today;

		/* PMR */
		$mr_today = ($pmr_data[$pid][$date] ?? 0);
		$aBalance[$j] -= $mr_today;
		$aaData_MR[$pid][] = $mr_today;

		/* ออก (Orders) */
		$out_today = ($orders_data[$pid][$date] ?? 0);
		$aBalance[$j] -= $out_today;
		$aaData_Out[$pid][] = $out_today;
		$sum_out_today += $out_today;

		/* Deposit (สะสม + กระทบ balance เฉพาะ id!=9) */
		foreach ($deposit_all as $k => $t) {
			$tid = (int)$t['id'];
			$amt = $adjustments_data[$pid][$tid][$date]['amount'] ?? 0.0;
			$runningDeposit[$pid][$tid] += $amt;
			if ($tid != 9) $aBalance[$j] += $amt;
			$aaaDeposit[$k][$pid][] = $runningDeposit[$pid][$tid]; // แสดงสะสม
		}

		/* Luckgems (สะสม, ไม่กระทบ balance) */
		foreach ($luck_types as $k => $t) {
			$tid = (int)$t['id'];
			$amt = $adjustments_data[$pid][$tid][$date]['amount'] ?? 0.0;
			$runningLuckgems[$pid][$tid] += $amt;
			$aaaLuck[$k][$pid][] = $runningLuckgems[$pid][$tid];
		}

		/* Type 12 Amount2 (สะสม) */
		$a2 = $adjustments_data[$pid][12][$date]['amount2'] ?? 0.0;
		$runningType12[$pid] += $a2;
		$aaData_Type12_Amount2[$pid][] = $runningType12[$pid];

		/* Memo (สะสม, ไม่กระทบ balance) */
		$memosum = 0.0;
		foreach ($memo_types as $k => $t) {
			$tid = (int)$t['id'];
			$amt = $adjustments_data[$pid][$tid][$date]['amount'] ?? 0.0;
			$runningMemo[$pid][$tid] += $amt;
			$aaaMemo[$k][$pid][] = $runningMemo[$pid][$tid];
			$memosum += $runningMemo[$pid][$tid];
		}
		$aaData_MemoSum[$pid][] = $memosum;

		/* CF / Total (หลังรับผลวันนี้ทั้งหมด) */
		$aaData_CF[$pid][]    = $aBalance[$j] + $runningType12[$pid];
		$aaData_Total[$pid][] = $aBalance[$j] + $memosum + $runningType12[$pid];
	}

	$aTotal[] = $sum_out_today;
}

/* =======================================================================
   E) UI helpers + ลิงก์แบบ hash router
   ======================================================================= */
function dayHead($ts)
{
	$c = ['Monday' => '#ffe594', 'Tuesday' => '#ffc4e8', 'Wednesday' => '#afe8bb', 'Thursday' => '#fc946b', 'Friday' => '#a6ddff', 'Saturday' => '#deabff', 'Sunday' => '#ef4d4d'];
	return 'background-color:' . ($c[date('l', $ts)] ?? '#eee');
}
function dayLight($ts)
{
	$c = ['Monday' => '#fffef7', 'Tuesday' => '#fef7fc', 'Wednesday' => '#f8fff8', 'Thursday' => '#fffcf7', 'Friday' => '#f7fbff', 'Saturday' => '#fcf7ff', 'Sunday' => '#fff7f8'];
	return 'background-color:' . ($c[date('l', $ts)] ?? '#fff');
}
function borderCol($ts)
{
	$c = ['Monday' => '#D4AC00', 'Tuesday' => '#E91E63', 'Wednesday' => '#4CAF50', 'Thursday' => '#FF5722', 'Friday' => '#2196F3', 'Saturday' => '#9C27B0', 'Sunday' => '#F44336'];
	return ($c[date('l', $ts)] ?? '#495057');
}

/* Prev/Next values */
$prevFrom = date('Y-m-d', strtotime("$from -$days day"));
$nextFrom = date('Y-m-d', strtotime("$from +$days day"));
$yearNow  = $year ?: (int)date('Y', strtotime($from));
$prevYear = $yearNow - 1;
$nextYear = $yearNow + 1;

/* Year options (ย้อน 8 ปี + ล่วงหน้า 1 ปี) */
$yrNow = (int)date('Y');
$yearOptions = [];
for ($y = $yrNow + 1; $y >= $yrNow - 8; $y--) $yearOptions[] = $y;

?>
<!-- Toolbar -->
<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
	<!-- โหมดช่วงวัน -->
	<a class="btn btn-sm btn-outline-primary me-2"
		href="#apps/stock/index.php?view=card&from=<?php echo $prevFrom; ?>&days=<?php echo $days; ?>">◀ ย้อนหลัง</a>
	<a class="btn btn-sm btn-outline-primary me-3"
		href="#apps/stock/index.php?view=card&from=<?php echo $nextFrom; ?>&days=<?php echo $days; ?>">ถัดไป ▶</a>

	<span class="me-2 text-muted">
		ช่วงวันที่: <?php echo date('d/m/Y', strtotime($from)); ?> - <?php echo date('d/m/Y', strtotime("$from +$totaldate day")); ?>
	</span>

	<div class="vr mx-3"></div>

	<!-- โหมดรายปี -->
	<label class="me-2">รายปี:</label>
	<select id="year-select" class="form-select form-select-sm" style="width:auto;display:inline-block;">
		<?php foreach ($yearOptions as $y): ?>
			<option value="<?php echo $y; ?>" <?php echo ($y == $yearNow ? 'selected' : ''); ?>><?php echo $y; ?></option>
		<?php endforeach; ?>
	</select>
	<a class="btn btn-sm btn-outline-secondary ms-2" href="#apps/stock/index.php?view=card&year=<?php echo $prevYear; ?>">◀ ปีที่แล้ว</a>
	<a class="btn btn-sm btn-outline-secondary ms-2" href="#apps/stock/index.php?view=card&year=<?php echo $nextYear; ?>">ปีถัดไป ▶</a>
</div>

<!-- ตารางหลัก -->
<div class="card">
	<div class="card-body overflow-auto p-0">
		<table class="table table-bordered table-sm mb-0">
			<tbody>
				<!-- Header วันที่ -->
				<tr>
					<th class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">วันที่</th>
					<?php foreach ($aDate as $ts): ?>
						<td class="text-center text-dark font-weight-bold day-border"
							style="<?php echo dayHead($ts); ?>; --border-color: <?php echo borderCol($ts); ?>;"
							colspan="<?php echo count($PID); ?>">
							<?php echo date("l d/m/Y", $ts); ?>
						</td>
					<?php endforeach; ?>
				</tr>

				<!-- ชื่อสินค้า -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">สินค้า</td>
					<?php foreach ($aDate as $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($aProduct as $p): ?>
							<td class="text-center text-dark font-weight-bold"
								style="<?php echo $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo htmlspecialchars($p['name']); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- ยอดยกมา BF -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">ยอดยกมา</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): ?>
							<td class="text-right font-weight-bold"
								style="<?php echo $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($aaData_BF[$pid][$i] ?? 0, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- เข้า -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">เข้า</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_In[$pid][$i] ?? 0; ?>
							<td class="text-right"
								style="<?php echo $style; ?>; <?php echo $v > 0 ? 'color:#28a745;font-weight:bold;' : ''; ?> border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- รับเข้า (แท่ง-อื่นๆ) -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">รับเข้า (แท่ง-อื่นๆ)</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_SB[$pid][$i] ?? 0; ?>
							<td class="text-right"
								style="<?php echo $style; ?>; <?php echo $v > 0 ? 'color:#28a745;font-weight:bold;' : ''; ?> border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- Deposit types (สะสม) -->
				<?php foreach ($aaaDeposit as $k => $deposit): ?>
					<tr>
						<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">
							<?php echo htmlspecialchars($deposit[0]['name']); ?>
						</td>
						<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
							$bd = borderCol($ts); ?>
							<?php foreach ($PID as $pid): $v = $deposit[$pid][$i] ?? 0; ?>
								<td class="text-right"
									style="<?php echo $style; ?>; <?php echo $v > 0 ? 'color:#28a745;font-weight:bold;' : ''; ?> border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
									<?php echo number_format($v, 4); ?>
								</td>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>

				<!-- Luckgems types (สะสม) -->
				<?php foreach ($aaaLuck as $k => $luck): ?>
					<tr>
						<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">
							<?php echo htmlspecialchars($luck[0]['name']); ?>
						</td>
						<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
							$bd = borderCol($ts); ?>
							<?php foreach ($PID as $pid): $v = $luck[$pid][$i] ?? 0; ?>
								<td class="text-right"
									style="<?php echo $style; ?>; <?php echo $v > 0 ? 'color:#28a745;font-weight:bold;' : ''; ?> border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
									<?php echo number_format($v, 4); ?>
								</td>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>

				<!-- PMR -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#dc3545;color:#fff;">PMR / ยืม</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_MR[$pid][$i] ?? 0; ?>
							<td class="text-right"
								style="<?php echo $style; ?>; <?php echo $v > 0 ? 'color:#dc3545;font-weight:bold;' : ''; ?> border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- ออก -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#dc3545;color:#fff;">ออก</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_Out[$pid][$i] ?? 0; ?>
							<td class="text-right"
								style="<?php echo $style; ?>; <?php echo $v > 0 ? 'color:#dc3545;font-weight:bold;' : ''; ?> border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- ยอดส่งรวม -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#ffc107;color:#000;">ยอดส่งรวม</td>
					<?php foreach ($aDate as $i => $ts): $bd = borderCol($ts); ?>
						<td class="text-center day-border font-weight-bold"
							style="background-color:#fff8e1; --border-color: <?php echo $bd; ?>;"
							colspan="<?php echo count($PID); ?>">
							<?php echo number_format($aTotal[$i] ?? 0, 4); ?>
						</td>
					<?php endforeach; ?>
				</tr>

				<!-- ยอดคงเหลือยกไป -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#28a745;color:#fff;">ยอดคงเหลือยกไป</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_CF[$pid][$i] ?? 0; ?>
							<td class="text-right font-weight-bold"
								style="<?php echo $v < 0 ? 'background:#f8d7da;color:#721c24;' : $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- Memo (รายละเอียดสะสม) -->
				<?php foreach ($aaaMemo as $k => $memo): ?>
					<tr class="hidebyclick" style="display:none;">
						<td class="sticky-left text-center" style="background-color:#ffc107;color:#000;">
							<?php echo htmlspecialchars($memo[0]['name']); ?>
						</td>
						<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
							$bd = borderCol($ts); ?>
							<?php foreach ($PID as $pid): $v = $memo[$pid][$i] ?? 0; ?>
								<td class="text-right"
									style="<?php echo $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
									<?php echo number_format($v, 4); ?>
								</td>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>

				<!-- รวม (MemoSum) -->
				<tr>
					<td class="sticky-left text-center" style="background-color:#007bff;color:#fff;">รวม</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_MemoSum[$pid][$i] ?? 0; ?>
							<td class="text-right"
								style="<?php echo $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- Type 12 Amount2 (รายละเอียดสะสม) -->
				<tr class="hidebyclick" style="display:none;">
					<td class="sticky-left text-center" style="background-color:#17a2b8;color:#fff;">Type 12 Amount2</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_Type12_Amount2[$pid][$i] ?? 0; ?>
							<td class="text-right"
								style="<?php echo $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>

				<!-- สินค้าคงคลัง -->
				<tr>
					<td class="sticky-left text-center font-weight-bold" style="background-color:#343a40;color:#fff;">สินค้าคงคลัง</td>
					<?php foreach ($aDate as $i => $ts): $style = dayLight($ts);
						$bd = borderCol($ts); ?>
						<?php foreach ($PID as $pid): $v = $aaData_Total[$pid][$i] ?? 0; ?>
							<td class="text-right font-weight-bold"
								style="<?php echo $v < 0 ? 'background:#f8d7da;color:#721c24;' : $style; ?>; border-left:1px solid <?php echo $bd; ?>; border-right:1px solid <?php echo $bd; ?>;">
								<?php echo number_format($v, 4); ?>
							</td>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<style>
	.table td,
	.table th {
		border: 1px solid #dee2e6;
		padding: 6px;
		font-size: 0.85rem;
		vertical-align: middle;
	}

	.sticky-left {
		position: sticky;
		left: 0;
		z-index: 10;
		min-width: 150px;
		background: #fff;
	}

	.hidebyclick {
		display: none;
	}

	.day-border {
		border-left: 3px solid var(--border-color, #495057) !important;
		border-right: 3px solid var(--border-color, #495057) !important;
	}

	@media (max-width:768px) {
		.table {
			font-size: .7rem;
		}

		.table td,
		.table th {
			padding: 4px;
		}
	}
</style>

<script>
	// เลือกปี -> ไปแบบ hash router
	(function() {
		const sel = document.getElementById('year-select');
		if (!sel) return;
		sel.addEventListener('change', function() {
			const y = this.value;
			window.location.hash = `#apps/stock/index.php?view=card&year=${encodeURIComponent(y)}`;
			window.location.reload();
		});
	})();
</script>