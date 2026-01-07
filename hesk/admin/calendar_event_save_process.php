<?php
define('IN_SCRIPT',1);
define('HESK_PATH','../');

require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');

hesk_load_database_functions();
hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();
hesk_checkPermission('can_man_settings');

$dbp = hesk_dbEscape($hesk_settings['db_pfix']);

/* รับค่าจากฟอร์ม */
$id    = isset($_POST['id']) ? intval($_POST['id']) : 0;
$date  = hesk_dbEscape($_POST['event_date']);
$time  = hesk_dbEscape($_POST['event_time']);
$title = hesk_dbEscape($_POST['title']);

/* บันทึกลง DB */
if($id){
    hesk_dbQuery("UPDATE {$dbp}calendar_events 
                  SET event_date='{$date}', event_time='{$time}', title='{$title}' 
                  WHERE id={$id}");
} else {
    hesk_dbQuery("INSERT INTO {$dbp}calendar_events (event_date,event_time,title) 
                  VALUES ('{$date}','{$time}','{$title}')");
}

/* redirect กลับไปหน้า calendar */
header('Location: calendar_events.php');
exit();
