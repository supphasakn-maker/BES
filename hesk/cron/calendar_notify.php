<?php
require('../inc/common.inc.php');

$res = hesk_dbQuery("
SELECT *
FROM hesk_calendar_events
WHERE notify_before IS NOT NULL
AND TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(event_date,' ',event_time)) = notify_before
");

while ($e = hesk_dbFetchAssoc($res)) {
    hesk_mail(
        $hesk_settings['admin_email'],
        'Event Reminder: '.$e['title'],
        $e['description']
    );
}
