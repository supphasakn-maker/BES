<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "a_public_holiday.id",
    "FisYear" => "a_public_holiday.FisYear",
    "PublicHoliday" => "a_public_holiday.PublicHoliday",
    "Descripiton" => "a_public_holiday.Descripiton",
);

$where = "1=1"; 

error_log("GET parameters: " . print_r($_GET, true));

if (!empty($_GET['search']['value'])) {
    $search = trim($_GET['search']['value']);
    $search_clean = str_replace(',', '', $search);

    $search_escaped = addslashes($search);
    $search_clean_escaped = addslashes($search_clean);

    $search_conditions = array();

    $search_conditions[] = "a_public_holiday.id LIKE '%$search_escaped%'";
    $search_conditions[] = "a_public_holiday.FisYear LIKE '%$search_escaped%'";
    $search_conditions[] = "a_public_holiday.Descripiton LIKE '%$search_escaped%'";
    $search_conditions[] = "DATE_FORMAT(a_public_holiday.PublicHoliday, '%Y-%m-%d') LIKE '%$search_escaped%'";
    $search_conditions[] = "DATE_FORMAT(a_public_holiday.PublicHoliday, '%d/%m/%Y') LIKE '%$search_escaped%'";

    $where .= " AND (" . implode(" OR ", $search_conditions) . ")";

    error_log("Search term: $search");
    error_log("Search WHERE: " . $where);
}

if (isset($_GET['year']) && !empty($_GET['year'])) {
    $year = addslashes($_GET['year']);
    $where .= " AND a_public_holiday.FisYear = '$year'";

    error_log("Filter by FisYear: $year");
}

$table = array(
    "index" => "id",
    "name" => "a_public_holiday",
    "where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], null);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$result = $dbc->GetResult();

echo json_encode($result);

$dbc->Close();
