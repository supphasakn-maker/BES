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

$dbp = hesk_dbEscape($hesk_settings['db_pfix']);

/* ================= LOAD EVENTS ================= */
$events = [];
$res = hesk_dbQuery("SELECT id, event_date, event_time, title FROM {$dbp}calendar_events ORDER BY event_date ASC");
while($row = hesk_dbFetchAssoc($res)){
    $date = $row['event_date'];
    if(!isset($events[$date])) $events[$date] = [];
    $events[$date][] = $row;
}

/* ================= CALENDAR RENDER ================= */
function render_month($year,$month,$events){
    $first = strtotime("$year-$month-01");
    $days  = date('t',$first);
    $start = date('N',$first);

    echo '<table class="cal">';
    echo '<tr><th colspan="7">'.date('F Y',$first).'</th></tr>';
    echo '<tr class="week">
        <td>Mo</td><td>Tu</td><td>We</td>
        <td>Th</td><td>Fr</td><td>Sa</td><td>Su</td>
    </tr><tr>';

    for($i=1;$i<$start;$i++) echo '<td></td>';

    for($d=1;$d<=$days;$d++){
        $dateStr = sprintf('%04d-%02d-%02d',$year,$month,$d);
        $list = $events[$dateStr] ?? [];
        $cnt  = count($list);

        echo '<td class="day'.($cnt?' has-event':'').'" data-date="'.$dateStr.'">';
        echo '<div class="num">'.$d.'</div>';

        if($cnt){
            echo '<span class="badge">'.$cnt.'</span>';
            echo '<div class="tooltip">';
            foreach($list as $e){
                echo '<div>'.$e['title'].' '.$e['event_time'].' <button class="edit-btn" data-id="'.$e['id'].'" data-title="'.htmlspecialchars($e['title']).'" data-date="'.$e['event_date'].'" data-time="'.$e['event_time'].'">Edit</button></div>';
            }
            echo '</div>';
        }
        echo '</td>';

        if((($d+$start-1)%7)==0) echo '</tr><tr>';
    }

    echo '</tr></table>';
}

/* Header + Nav */
require(HESK_PATH.'inc/header.inc.php');
require(HESK_PATH.'inc/show_admin_nav.inc.php');
?>

<style>
.calendar{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:40px}
.cal{width:100%;border-collapse:collapse;table-layout:fixed;font-size:12px}
.cal th{background:#1f4fa3;color:#fff;height:44px;text-align:center}
.cal td{border:1px solid #e0e0e0;height:72px;position:relative;vertical-align:top;cursor:pointer}
.num{position:absolute;top:6px;left:8px;color:#555}
.has-event{background:#fff}
.badge{
    position:absolute;
    top:6px;
    right:6px;
    background:#e74c3c;
    color:#fff;
    font-size:11px;
    border-radius:10px;
    padding:2px 6px;
}
.tooltip{
    display:none;
    position:absolute;
    left:6px;
    top:28px;
    min-width:260px;
    max-width:420px;
    max-height:220px;
    overflow:auto;
    background:#1d2125;
    color:#fff;
    padding:10px 12px;
    border-radius:6px;
    font-size:12px;
    line-height:1.5;
    box-shadow:0 8px 24px rgba(0,0,0,.35);
    z-index:10;
}
.tooltip div{
    padding:6px 4px;
    border-bottom:1px solid rgba(255,255,255,.1);
}
.tooltip div:last-child{border-bottom:none;}

/* Modal */
#eventModal{
    display:none;
    position:fixed;
    top:50%; left:50%;
    transform:translate(-50%,-50%);
    background:#fff;
    padding:20px;
    border-radius:8px;
    max-width:500px;
    width:90%;
    box-shadow:0 0 20px rgba(0,0,0,.4);
    z-index:9999;
}
#eventModal header{
    font-weight:bold;
    margin-bottom:10px;
}
#eventModal input, #eventModal button{
    width:100%; padding:6px 8px; margin-bottom:10px;
}
#eventModal button{cursor:pointer}
#modalClose{position:absolute;top:10px;right:10px;cursor:pointer;font-weight:bold}
</style>

<h1>Event Calendar</h1>
<div class="calendar">
<?php
$year = date('Y');
for($m=1;$m<=12;$m++) render_month($year,$m,$events);
?>
</div>

<!-- Modal -->
<div id="eventModal">
    <span id="modalClose">&times;</span>
    <header id="modalTitle">New Event</header>
    <form id="eventForm">
        <input type="hidden" name="id" id="eventId">
        <input type="text" name="title" id="eventTitle" placeholder="Title">
        <input type="date" name="event_date" id="eventDate">
        <input type="time" name="event_time" id="eventTime">
        <button type="button" id="saveEventBtn">Save</button>
    </form>
</div>

<script>
const events = <?=json_encode($events)?>;
const modal = document.getElementById('eventModal');
const modalClose = document.getElementById('modalClose');
const eventId = document.getElementById('eventId');
const eventTitle = document.getElementById('eventTitle');
const eventDate = document.getElementById('eventDate');
const eventTime = document.getElementById('eventTime');
const saveEventBtn = document.getElementById('saveEventBtn');

modalClose.onclick = ()=>modal.style.display='none';

/* Tooltip */
document.querySelectorAll('.day.has-event').forEach(d=>{
    d.onmouseenter = ()=>{
        const tip = d.querySelector('.tooltip');
        if(tip) tip.style.display='block';
    };
    d.onmouseleave = ()=>{
        const tip = d.querySelector('.tooltip');
        if(tip) tip.style.display='none';
    };
});

/* Edit Buttons in Tooltip */
document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.onclick = (e)=>{
        e.stopPropagation(); // prevent day click
        eventId.value = btn.dataset.id;
        eventTitle.value = btn.dataset.title;
        eventDate.value = btn.dataset.date;
        eventTime.value = btn.dataset.time;
        modal.style.display='block';
    }
});

/* Click day to create new event */
document.querySelectorAll('.cal td').forEach(d=>{
    d.onclick = ()=>{
        const date = d.dataset.date;
        eventId.value = '';
        eventTitle.value = '';
        eventDate.value = date;
        eventTime.value = '12:00';
        modal.style.display='block';
    }
});

/* Save Event */
saveEventBtn.onclick = ()=>{
    const formData = new FormData();
    formData.append('id', eventId.value);
    formData.append('title', eventTitle.value);
    formData.append('event_date', eventDate.value);
    formData.append('event_time', eventTime.value);

    fetch('calendar_event_save.php',{
        method:'POST',
        body:formData
    }).then(r=>r.json()).then(data=>{
        if(data.success){
            alert('Saved!');
            location.reload();
        }else{
            alert('Error: '+data.message);
        }
    }).catch(e=>{
        alert('Save failed: '+e);
    });
};
</script>

<?php require(HESK_PATH.'inc/footer.inc.php'); ?>
