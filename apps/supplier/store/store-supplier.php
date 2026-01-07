<?php

/**
 * Server-side for DataTables: bs_suppliers
 * ฟิลเตอร์สถานะด้วย GET: ?status_filter=1|2
 * - ถ้าไม่ฟิลเตอร์: ส่งผลจาก datastore ตรง ๆ (ชัวร์ว่ามีข้อมูล)
 * - ถ้าฟิลเตอร์: ดึงทั้งหมด, กรองเอง, paginate เอง
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore();
$dbc->Connect();


$columns = array(
	"id"         => "bs_suppliers.id",
	"name"       => "bs_suppliers.name",
	"created"    => "bs_suppliers.created",
	"updated"    => "bs_suppliers.updated",
	"comment"    => "bs_suppliers.comment",
	"type"       => "bs_suppliers.type",
	"gid"        => "bs_suppliers.gid",
	"status"     => "bs_suppliers.status",
	"group_name" => "bs_supplier_groups.name"
);


$table = array(
	"index" => "id",
	"name"  => "bs_suppliers",
	"join"  => array(
		array(
			"field" => "gid",
			"table" => "bs_supplier_groups",
			"with"  => "id"
		)
	)
);


$draw       = isset($_GET['draw']) ? (int)$_GET['draw'] : 0;
$start      = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$length     = isset($_GET['length']) ? (int)$_GET['length'] : 10;
$order      = $_GET['order']   ?? [];
$columnsReq = $_GET['columns'] ?? [];
$searchTop  = $_GET['search']  ?? ['value' => '', 'regex' => 'false'];

$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '1';
$wantFilter = ($status_filter === '1' || $status_filter === '2');


$extractRows = function ($out) {
	if (isset($out['data']) && is_array($out['data']))   return $out['data'];
	if (isset($out['aaData']) && is_array($out['aaData'])) return $out['aaData'];

	if (is_array($out)) {
		foreach (['rows', 'result', 'items'] as $k) {
			if (isset($out[$k]) && is_array($out[$k])) return $out[$k];
		}
	}
	return [];
};


$getStatusFromRow = function ($row) {

	if (is_array($row)) {

		foreach (['status', 'bs_suppliers.status'] as $k) {
			if (array_key_exists($k, $row)) {
				return $row[$k];
			}
		}

		if (array_key_exists(4, $row)) {
			return $row[4];
		}
	}
	return null;
};


if (!$wantFilter) {
	$dbc->SetParam($table, $columns, $order, $columnsReq, $searchTop);
	$dbc->SetLimit($length, $start);

	ob_start();
	$dbc->Processing();
	$out  = $dbc->GetResult();
	$leak = ob_get_clean();
	if ($leak) {
		$out['_debug'] = $leak;
	}


	if (!isset($out['draw'])) $out['draw'] = $draw;

	echo json_encode($out, JSON_UNESCAPED_UNICODE);
	$dbc->Close();
	exit;
}


$pullLimit = 100000;
$dbc->SetParam($table, $columns, $order, $columnsReq, $searchTop);
$dbc->SetLimit($pullLimit, 0);

ob_start();
$dbc->Processing();
$outAll = $dbc->GetResult();
$leak   = ob_get_clean();

$rowsAll = $extractRows($outAll);


if (empty($rowsAll)) {
	$dbc->SetParam($table, $columns, $order, $columnsReq, $searchTop);
	$dbc->SetLimit($length, $start);

	ob_start();
	$dbc->Processing();
	$out  = $dbc->GetResult();
	$leak2 = ob_get_clean();

	if ($leak || $leak2) {
		$out['_debug'] = ($out['_debug'] ?? '') . $leak . $leak2;
	}
	if (!isset($out['draw'])) $out['draw'] = $draw;

	echo json_encode($out, JSON_UNESCAPED_UNICODE);
	$dbc->Close();
	exit;
}


$want = (int)$status_filter;
$rowsFiltered = array_values(array_filter($rowsAll, function ($r) use ($getStatusFromRow, $want) {
	$val = $getStatusFromRow($r);
	if ($val === null) return false;
	return (int)$val === $want;
}));


$totalAll      = isset($outAll['recordsTotal']) ? (int)$outAll['recordsTotal'] : count($rowsAll);
$filteredCount = count($rowsFiltered);
$pageRows      = array_slice($rowsFiltered, $start, $length);


$result = array(
	'draw'            => $draw,
	'recordsTotal'    => $totalAll,
	'recordsFiltered' => $filteredCount,
	'data'            => $pageRows
);
if ($leak) {
	$result['_debug'] = $leak;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
$dbc->Close();
