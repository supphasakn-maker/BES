<?php
define('IN_SCRIPT',1);
define('HESK_PATH','../');

require(HESK_PATH.'hesk_settings.inc.php');
require(HESK_PATH.'inc/common.inc.php');
require(HESK_PATH.'inc/admin_functions.inc.php');

hesk_load_database_functions();
hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();
hesk_checkPermission('can_man_settings'); // ปรับ permission ตามต้องการ

header('Content-Type: application/json');

$id         = intval($_POST['id'] ?? 0);
$title      = trim($_POST['title'] ?? '');
$event_date = $_POST['event_date'] ?? '';
$event_time = $_POST['event_time'] ?? '';

if(!$title || !$event_date){
    echo json_encode(['success'=>false,'message'=>'Title and Date are required']);
    exit;
}

$dbp = hesk_dbEscape($hesk_settings['db_pfix']);

if($id){ // update
    $sql = "UPDATE {$dbp}calendar_events
            SET title='".hesk_dbEscape($title)."',
                event_date='".hesk_dbEscape($event_date)."',
                event_time='".hesk_dbEscape($event_time)."'
            WHERE id=$id
            LIMIT 1";
    $res = hesk_dbQuery($sql);
    echo json_encode(['success'=>true]);
    exit;
}else{ // insert new
    $sql = "INSERT INTO {$dbp}calendar_events(title,event_date,event_time)
            VALUES('".hesk_dbEscape($title)."',
                   '".hesk_dbEscape($event_date)."',
                   '".hesk_dbEscape($event_time)."')";
    $res = hesk_dbQuery($sql);
    echo json_encode(['success'=>true]);
    exit;
}
