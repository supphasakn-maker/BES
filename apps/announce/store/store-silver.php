<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_announce_silver.id",
	"no" => "bs_announce_silver.no",
	"created" => "bs_announce_silver.created",
	"date" => "bs_announce_silver.date",
	"rate_spot" => "FORMAT(bs_announce_silver.rate_spot,2)",
	"rate_exchange" => "FORMAT(bs_announce_silver.rate_exchange,2)",
	"rate_pmdc" => "FORMAT(bs_announce_silver.rate_pmdc,2)",
	"timeno" => "DATE_FORMAT(bs_announce_silver.created,'%H:%i')",
	"sell" => "FORMAT(bs_announce_silver.sell,2)",
	"buy" => "FORMAT(bs_announce_silver.buy,2)",
	"status" => "bs_announce_silver.status",
	"dif" => "FORMAT(COALESCE(bs_announce_silver.buy - (
        SELECT prev.buy 
        FROM bs_announce_silver prev 
        WHERE prev.status = 1 
        AND prev.created < bs_announce_silver.created 
        ORDER BY prev.created DESC 
        LIMIT 1
    ), 0), 2)",
);

$where = 'bs_announce_silver.status = 1';

error_log("GET parameters: " . print_r($_GET, true));


if (!empty($_GET['search']['value'])) {
	$search = trim($_GET['search']['value']);
	$search_clean = str_replace(',', '', $search);


	$search_escaped = addslashes($search);
	$search_clean_escaped = addslashes($search_clean);


	$search_conditions = array();


	$search_conditions[] = "bs_announce_silver.id LIKE '%$search_escaped%'";


	$search_conditions[] = "bs_announce_silver.date LIKE '%$search_escaped%'";
	$search_conditions[] = "DATE_FORMAT(bs_announce_silver.date, '%Y-%m-%d') LIKE '%$search_escaped%'";
	$search_conditions[] = "DATE_FORMAT(bs_announce_silver.date, '%d/%m/%Y') LIKE '%$search_escaped%'";


	if (is_numeric($search_clean)) {
		$search_conditions[] = "bs_announce_silver.buy = '$search_clean_escaped'";
		$search_conditions[] = "bs_announce_silver.sell = '$search_clean_escaped'";
		$search_conditions[] = "bs_announce_silver.rate_spot = '$search_clean_escaped'";
	}


	$search_conditions[] = "FORMAT(bs_announce_silver.buy,2) LIKE '%$search_escaped%'";
	$search_conditions[] = "FORMAT(bs_announce_silver.sell,2) LIKE '%$search_escaped%'";

	$where .= " AND (" . implode(" OR ", $search_conditions) . ")";


	error_log("Search term: $search");
	error_log("Search WHERE: " . $where);
}


if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
	$date_from = addslashes($_GET['date_from']);
	$date_to = addslashes($_GET['date_to']);
	$where .= " AND (bs_announce_silver.date BETWEEN '$date_from' AND '$date_to')";
}

$table = array(
	"index" => "id",
	"name" => "bs_announce_silver",
	"where" => $where
);



$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], null);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$result = $dbc->GetResult();




echo json_encode($result);

$dbc->Close();
