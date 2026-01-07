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

/* ================= LOAD TICKETS ================= */
$sql = "SELECT id, trackid, subject, dt, status
        FROM {$hesk_settings['db_pfix']}tickets";

$res = hesk_dbQuery($sql);

$tickets = [];
while ($row = hesk_dbFetchAssoc($res)) {
    $date = substr($row['dt'],0,10);
    $tickets[$date][] = $row;
}

/* ================= CALENDAR ================= */
function render_month($year,$month,$tickets)
{
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
        $date = sprintf('%04d-%02d-%02d',$year,$month,$d);
        $list = $tickets[$date] ?? [];
        $cnt  = count($list);

        echo '<td class="day'.($cnt?' has-ticket':'').'" data-date="'.$date.'">';
        echo '<div class="num">'.$d.'</div>';

        if($cnt){
            echo '<span class="badge">'.$cnt.'</span>';
            echo '<div class="tooltip">';
            foreach($list as $t){
                echo '<div>#'.$t['id'].' '.$t['subject'].'</div>';
            }
            echo '</div>';
        }
        echo '</td>';

        if((($d+$start-1)%7)==0) echo '</tr><tr>';
    }
    echo '</tr></table>';
}
require(HESK_PATH.'inc/header.inc.php');
require(HESK_PATH.'inc/show_admin_nav.inc.php');
?>

<style>
.calendar{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.cal{width:100%;border-collapse:collapse;table-layout:fixed;font-size:12px}
.cal th{background:#1f4fa3;color:#fff;height:44px}
.cal td{border:1px solid #e0e0e0;height:72px;position:relative;vertical-align:top}
.num{position:absolute;top:6px;left:8px;color:#555}
.has-ticket{background:#fff}
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

.tooltip div:last-child{
    border-bottom:none;
}
.day.has-ticket{
    cursor:pointer;
}
.day.has-ticket:hover{
    background:#f4f8ff;
}

.day:hover .tooltip{display:block}

/* Drawer */
#drawer{
    position:fixed;
    top:0;
    right:-520px;
    width:520px;
    height:100%;
    background:#fff;
    box-shadow:-4px 0 12px rgba(0,0,0,.2);
    transition:.3s;
    z-index:9999;
}
#drawer.open{right:0}
#drawer header{
    padding:14px;
    background:#1f4fa3;
    color:#fff;
}
#drawer .content{
    padding:14px;
    overflow:auto;
    height:calc(100% - 56px);
}
.ticket{
    border-left:4px solid #1f4fa3;
    padding:8px;
    margin-bottom:8px;
    background:#f9fbff;
}
</style>

<h1>Ticket Calendar</h1>

<div class="calendar">
<?php
$year = date('Y');
for($m=1;$m<=12;$m++) render_month($year,$m,$tickets);
?>
</div>

<div id="drawer">
<header>
  <span id="d-title"></span>
  <button style="float:right" onclick="closeDrawer()">✕</button>
</header>
<div class="content" id="d-body"></div>
</div>

<script>
const tickets = <?=json_encode($tickets)?>;

document.querySelectorAll('.day.has-ticket').forEach(d=>{
  d.onclick=()=>{
    const date=d.dataset.date;
    document.getElementById('d-title').innerText='Tickets on '+date;
    const body=document.getElementById('d-body');
    body.innerHTML='';
    tickets[date].forEach(t=>{
      body.innerHTML+=`
      <div class="ticket">
        <b>#${t.id}</b> ${t.subject}<br>
        <a href="admin_ticket.php?track=${t.trackid}" target="_blank">
            Open ticket
        </a>
      </div>`;
    });
    document.getElementById('drawer').classList.add('open');
  }
});
function closeDrawer(){
  document.getElementById('drawer').classList.remove('open');
}
</script>

<?php require(HESK_PATH.'inc/footer.inc.php'); ?>
