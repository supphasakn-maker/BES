<?php
define('IN_SCRIPT',1);
define('HESK_PATH','../');

require(HESK_PATH.'hesk_settings.inc.php');
require(HESK_PATH.'inc/common.inc.php');

hesk_load_database_functions();
hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

$date = hesk_dbEscape($_GET['date']);
$db   = hesk_dbEscape($hesk_settings['db_pfix']);

$res = hesk_dbQuery("SELECT * FROM {$db}calendar_events WHERE event_date='$date'");
?>

<h4>Events on <?php echo $date ?></h4>

<form method="post" action="calendar_event_save.php">
<input type="hidden" name="event_date" value="<?php echo $date ?>">
<div>Title <input name="title" required></div>
<div>Time <input type="time" name="event_time" required></div>
<div>Notes <textarea name="notes"></textarea></div>
<button>Save</button>
</form>

<hr>

<?php while($e = hesk_dbFetchAssoc($res)){ ?>
<div>
<b><?php echo hesk_htmlspecialchars($e['title']) ?></b>
(<?php echo $e['event_time'] ?>)
</div>
<?php } ?>
