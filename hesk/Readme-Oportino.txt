
# ตั้ง crontab

 เข้าไปใน container
docker exec -it bes-web-1 sh
# หรือ bash ถ้ามี
docker exec -it bes-web-1 bash

# เปิด editor ของ cron สำหรับ user ปัจจุบัน
crontab -e

# ทุกวัน 9 โมง
0 9 * * * /usr/local/bin/php /var/www/html/hesk/scripts/send_reminder.php >> /var/log/cron.log 2>&1



# หรือจากภายนอก
# crontab ของ host
0 9 * * * docker exec bes-web-1 php /var/www/html/hesk/scripts/send_reminder.php >> /var/log/hesk_cron.log 2>&1

