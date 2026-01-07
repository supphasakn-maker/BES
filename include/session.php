<?php
if (empty($_SESSION['auth']['user_id'])) {
	echo '<script>
		setTimeout(function() {
		swal({
		title: "คุณหมดเวลาการใช้งานแล้วโปรดเข้าสู่ระบบใหม่อีกครั้ง",
		type: "error"
		}, function() {
		window.location.reload();
		});
		}, 1000);
		</script>';
	exit();
}
