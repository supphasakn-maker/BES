<?php
function DateThaiFullNotime($strDate)
{
	$strYear = date("Y", strtotime($strDate)) + 543;
	$strMonth = date("n", strtotime($strDate));
	$strDay = date("j", strtotime($strDate));
	$strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
	$strMonthThai = $strMonthCut[$strMonth];
	return "$strDay $strMonthThai $strYear";
}

function TimeNodate($strDate)
{
	$strHour = date("H", strtotime($strDate));
	$strMinute = date("i", strtotime($strDate));
	return "$strHour:$strMinute";
}
$dd = date("Y-m-d");
$aa = date('Y-m-d', strtotime("-1 days"));
$sql = "SELECT * , (LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS' , (buy +- LAG(buy) OVER (ORDER BY id)) AS 'PREVIOUS_PRICE' FROM bs_announce_silver WHERE status = '1'  ORDER BY id DESC LIMIT 1";
$rst = $dbc->Query($sql);
// $silver = $dbc->Fetch($rst);
if ($rst->num_rows > 0)
	while ($silver = $rst->fetch_assoc()) {

		$allvat = $silver['sell'] * 7 / 100;
		$all = $silver['sell'] + $allvat;

		$created = $silver['created'];
		$timestamp = strtotime($created);
		$new_date = date("d-m-Y H:i", $timestamp);

?>

	<style>
		@font-face {
			font-family: 'FC Subject Condensed Bold';
			src: url('../../../font/FC-Subject/FCSubjectCondensed-Bold.eot');
			src: url('../../../font/FC-Subject/FCSubjectCondensed-Bold.eot?#iefix') format('embedded-opentype'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Bold.woff2') format('woff2'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Bold.woff') format('woff'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Bold.ttf') format('truetype');
			font-weight: bold;
			font-style: normal;
			font-display: swap;
		}

		@font-face {
			font-family: 'FC Subject Condensed Medium';
			src: url('../../../font/FC-Subject/FCSubjectCondensed-Medium.eot');
			src: url('../../../font/FC-Subject/FCSubjectCondensed-Medium.eot?#iefix') format('embedded-opentype'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Medium.woff2') format('woff2'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Medium.woff') format('woff'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Medium.ttf') format('truetype');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}

		@font-face {
			font-family: 'FC Subject Condensed Light';
			src: url('../../../font/FC-Subject/FCSubjectCondensed-Light.eot');
			src: url('../../../font/FC-Subject/FCSubjectCondensed-Light.eot?#iefix') format('embedded-opentype'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Light.woff2') format('woff2'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Light.woff') format('woff'),
				url('../../../font/FC-Subject/FCSubjectCondensed-Light.ttf') format('truetype');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}

		.bg {
			background-color: #12284C !important;
			padding: 35px;
		}

		.pricess {
			margin: 0;
			padding: 0;
			border: 0;
			background-image: url('../../../img/ประกาศราคาใหม่2025.png');
			background-repeat: no-repeat;
			background-color: #12284C !important;
			width: 1080px !important;
		}

		.text-announce-date {
			position: absolute;
			top: 220px;
			left: 335px;
			font-family: 'FC Subject Condensed Medium', sans-serif;
			font-size: 60px !important;
			color: #12284C;
		}

		.text-announce-time {
			position: absolute;
			top: 330px;
			left: 220px;
			font-family: 'FC Subject Condensed Medium', sans-serif;
			font-size: 60px !important;
		}

		.text-announce-no {
			position: absolute;
			top: 330px;
			left: 640px;
			font-family: 'FC Subject Condensed Medium', sans-serif;
			font-size: 65px !important;
		}

		.text-announce-price {
			position: absolute;
			top: 425px;
			left: 400px;
			font-family: 'FC Subject Condensed Bold', sans-serif;
			font-size: 115px !important;
		}

		.text-announce-previous_price {
			position: absolute;
			top: 675px;
			left: 175px;
			font-family: 'FC Subject Condensed Bold', sans-serif;
			font-size: 130px !important;
			color: #ED1C24;
		}

		.text-announce-sell-price {
			position: absolute;
			top: 675px;
			left: 600px;
			font-family: 'FC Subject Condensed Bold', sans-serif;
			font-size: 130px !important;
			color: #00A651;
		}

		.text-announce-sell-vats {
			position: absolute;
			top: 860px;
			left: 758px;
			font-family: 'FC Subject Condensed Bold', sans-serif;
			font-size: 58px !important;
			color: #FFFF;
		}

		/* Enhanced input styling */
		.copy-section {
			margin: 20px 0;
			padding: 20px;
			background-color: #f8f9fa;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		}

		.copy-section h5 {
			margin-bottom: 15px;
			color: #333;
			font-weight: 600;
		}

		#copyText {
			width: 100%;
			max-width: 500px;
			padding: 12px;
			margin-bottom: 15px;
			border: 1px solid #ddd;
			border-radius: 8px;
			font-size: 14px;
			line-height: 1.4;
			resize: vertical;
			min-height: 120px;
		}

		#copyText1 {
			width: 100%;
			max-width: 500px;
			padding: 12px;
			margin-bottom: 15px;
			border: 1px solid #ddd;
			border-radius: 8px;
			font-size: 14px;
			line-height: 1.4;
			resize: vertical;
			min-height: 80px;
		}

		.btn-copy {
			padding: 10px 20px;
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			color: #fff;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			font-weight: 500;
			transition: all 0.3s ease;
			margin-right: 10px;
		}

		.btn-copy:hover {
			background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
		}

		.btn-fetch {
			padding: 10px 20px;
			background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
			color: #fff;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			font-weight: 500;
			transition: all 0.3s ease;
			margin-right: 10px;
		}

		.btn-fetch:hover {
			background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
		}

		.btn-fetch:disabled {
			background: #6c757d;
			cursor: not-allowed;
			transform: none;
			box-shadow: none;
		}

		/* Loading state */
		.loading {
			opacity: 0.6;
			pointer-events: none;
		}

		/* Success/Error messages */
		.message {
			padding: 10px;
			border-radius: 5px;
			margin-bottom: 10px;
			font-size: 14px;
		}

		.message.success {
			background-color: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}

		.message.error {
			background-color: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}

		/* Responsive design */
		@media (max-width: 768px) {
			.copy-section {
				margin: 10px;
				padding: 15px;
			}

			#copyText,
			#copyText1 {
				width: 100%;
				font-size: 12px;
			}

			.btn-copy,
			.btn-fetch {
				width: 100%;
				margin-bottom: 10px;
			}
		}
	</style>

	<section class="pricess" id="html2canvas">
		<div class="text-announce-date">
			วันที่ <?php echo DateThaiFullNotime($created); ?>
		</div>
		<div class="text-announce-time text-white">
			เวลา <?php echo TimeNodate($created); ?> น.
		</div>
		<div class="text-announce-no text-white">
			ครั้งที่ <?php echo $silver['no']; ?>
		</div>
		<div class="text-announce-price">
			<?php if ($silver['PREVIOUS_PRICE'] < 0) {
				echo '<img src="../../../img/AW_ราคาตลาด_2025_Final_ลูกศร_แดง.png" class="img-fluid" alt="..."><font color="#BA1924; -webkit-text-stroke: 1px rgb(182, 179, 179);">' . abs($silver['PREVIOUS_PRICE']) . '</font>';
			} else {
				echo '<img src="../../../img/AW_ราคาตลาด_2025_Final_ลูกศร_เขียว.png" class="img-fluid" alt="..."><font color="#009245">' . number_format($silver['PREVIOUS_PRICE'], 0) . '</font>';
			}
			?>
		</div>
		<div class="text-announce-sell-price ">
			<?php
			echo '<span class="text-price-up">' . number_format($silver['sell'], 0) . '</span>';
			?>
		</div>
		<div class="text-announce-previous_price">
			<?php
			echo '<span class="text-price-up">' . number_format($silver['buy'], 0) . '</span>';
			?>
		</div>
		<div class="text-announce-sell-vats">
			<?php
			echo '<span class="text-price-up">' . number_format($all, 2) . '</span>';
			?>
		</div>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
	</section>
	<br>

	<div class="copy-section">
		<h5><i class="fas fa-coins"></i> ข้อความประกาศราคาเงิน</h5>
		<div id="message1" class="message" style="display: none;"></div>
		<textarea id="copyText" rows="6" readonly>วันที่ <?php echo DateThaiFullNotime($created); ?> 
ประกาศราคา ครั้งที่ <?php echo $silver['no']; ?> เวลา <?php echo TimeNodate($created); ?> น. 
ราคารับซื้อ <?php echo number_format($silver['buy'], 0); ?> บาท
ราคาขายออก <?php echo number_format($silver['sell'], 0); ?> บาท
ราคารวม VAT+ <?php echo number_format($all, 2); ?> บาท
สามารถดูราคาซิลเวอร์ย้อนหลังได้ที่นี่!👇
https://bowinsgroup.com/announce-price</textarea>
		<br>
		<button class="btn-copy" onclick="copyText()">
			<i class="fas fa-copy"></i> คัดลอกข้อความ
		</button>
	</div>

	<!-- Bowins Design Price Section -->
	<div class="copy-section">
		<h5><i class="fas fa-gem"></i> ราคาแท่งเงิน Bowins Design</h5>
		<div id="message2" class="message" style="display: none;"></div>
		<textarea id="copyText1" rows="6" placeholder="กดปุ่มดึงข้อมูลเพื่อโหลดราคา..."></textarea>
		<br>
		<button class="btn-fetch" id="fetchButton" onclick="fetchBowinsDesignPrice()">
			<i class="fas fa-download"></i> <span id="fetchButtonText">ดึงข้อมูลราคา</span>
		</button>
		<button class="btn-copy" onclick="copyText1()" id="copyButton1" disabled>
			<i class="fas fa-copy"></i> คัดลอกข้อความ
		</button>
		<button class="btn-copy" onclick="startAutoUpdate()" id="autoUpdateButton" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
			<i class="fas fa-sync-alt"></i> <span id="autoUpdateText">เริ่มอัพเดทอัตโนมัติ</span>
		</button>
	</div>

<?php
	}
?>

<br>
<button class="btn btn-danger" onclick="downloadByHtml2Canvas()">
	<i class="fas fa-download"></i> Download Image
</button>

<div class="card mb-2">
	<div class="card-body">
		<a href="javascript:;" class="" onclick="$('.hidebyclick').toggle()">Toggle Detail</a>
		<table class="table table-bordered" style="width:100%">
			<thead class="bg-dark">
				<th class="text-center text-white" style="height: 50px;overflow:hidden; font-weight: bold;">วันที่</th>
				<th class="text-center text-white" style="height: 50px;overflow:hidden; font-weight: bold;">ครั้งที่</th>
				<th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาซื้อเข้า</th>
				<th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาขายออก</th>
				<th class="text-center text-white" style="height: 50px; overflow:hidden; font-weight: bold;">ราคาเปลี่ยนแปลง</th>
			</thead>
			<?php
			$sql = "select * from bs_announce_silver where date =  '" . $aa . "' ORDER by no DESC limit 1 ";
			$rss = $dbc->Query($sql);
			$lastdate = $dbc->Fetch($rss);
			if (!isset($lastdate['buy']) || is_null($lastdate['buy'])) {
				$lastdate['buy'] = 0;
			}

			$sql1 = "select *, (lag(buy, 1, '" . $lastdate['buy'] . "') over (order by id)) as previos , (buy +- lag(buy, 1,'" . $lastdate['buy'] . "') over (order by id)) as previosprice from bs_announce_silver WHERE date = '" . $dd . "' ORDER BY id DESC";

			$rsm = $dbc->Query($sql1);
			while ($line = $dbc->Fetch($rsm)) {
				$created = $line['created'];
				$timestamp = strtotime($created);
				$new_date = date("d-m-Y H:i", $timestamp);
				$price = $line['previosprice'];
			?>
				<tbody>
					<tr class="hidebyclick" style="display: none">
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $new_date; ?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $line['no']; ?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($line['buy'], 2); ?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo number_format($line['sell'], 2); ?></span></td>
						<td class="text-center" style="overflow:hidden; vertical-align: middle;"><span style="height: 60px; overflow:hidden;"><?php echo $price; ?></span></td>
					</tr>
				</tbody>
			<?php
			}
			?>
		</table>
	</div>
</div>

<script>
	let autoUpdateInterval = null;
	let isAutoUpdating = false;
	let retryCount = 0;
	const maxRetries = 3;

	async function fetchBowinsDesignPrice() {
		const url = 'https://www.bowinsgroup.com/ipn/proxy_bwd.php';
		const outputElement = document.getElementById('copyText1');
		const fetchButton = document.getElementById('fetchButton');
		const fetchButtonText = document.getElementById('fetchButtonText');
		const copyButton = document.getElementById('copyButton1');
		const messageDiv = document.getElementById('message2');

		if (fetchButton) {
			fetchButton.disabled = true;
			fetchButton.classList.add('loading');
			fetchButtonText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังดึงข้อมูล...';
		}

		if (outputElement) {
			outputElement.value = 'กำลังดึงข้อมูลราคา...';
		}

		try {
			const controller = new AbortController();
			const timeoutId = setTimeout(() => controller.abort(), 15000);

			const response = await fetch(url, {
				method: 'GET',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
				},
				signal: controller.signal
			});

			clearTimeout(timeoutId);

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}

			const data = await response.json();

			if (data.success && data.data) {
				const prices = data.data;

				function formatNumberWithCommas(number) {
					if (number === null || number === undefined || isNaN(number)) {
						return 'ไม่มีข้อมูล';
					}
					return parseFloat(number).toLocaleString('th-TH', {
						minimumFractionDigits: 0,
						maximumFractionDigits: 2
					});
				}

				const price15 = prices['15'] ? formatNumberWithCommas(prices['15']) : 'ไม่มีข้อมูล';
				const price50 = prices['50'] ? formatNumberWithCommas(prices['50']) : 'ไม่มีข้อมูล';
				const price150 = prices['150'] ? formatNumberWithCommas(prices['150']) : 'ไม่มีข้อมูล';

				const outputText = `แจ้งราคาแท่งเงิน Bowins Design ปัจจุบันค่ะ
*ราคารวมVat 7% 

ขนาด 15 กรัม (น้ำหนัก 0.98 บาท) - ${price15} บาท
ขนาด 50 กรัม (น้ำหนัก 3.28 บาท) - ${price50} บาท
ขนาด 150 กรัม (น้ำหนัก 9.84 บาท) - ${price150} บาท

อัพเดทเมื่อ: ${new Date().toLocaleString('th-TH')}`;

				if (outputElement) {
					outputElement.value = outputText;
				}

				if (copyButton) {
					copyButton.disabled = false;
				}

				showMessage('ดึงข้อมูลราคาสำเร็จ!', 'success', 'message2');

				retryCount = 0;

			} else {
				throw new Error(data.errors || 'ไม่พบข้อมูลราคา');
			}

		} catch (error) {
			let errorMessage = 'เกิดข้อผิดพลาดในการดึงข้อมูล';

			if (error.name === 'AbortError') {
				errorMessage = 'การดึงข้อมูลใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
			} else if (error.message.includes('HTTP error')) {
				errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้';
			} else if (error.message.includes('Failed to fetch')) {
				errorMessage = 'ไม่สามารถเชื่อมต่ออินเทอร์เน็ตได้';
			}

			console.error('Error fetching Bowins Design price:', error);

			if (outputElement) {
				outputElement.value = `ไม่สามารถดึงราคาสินค้า Bowins Design ได้\nสาเหตุ: ${errorMessage}`;
			}

			showMessage(errorMessage, 'error', 'message2');

			if (retryCount < maxRetries && isAutoUpdating) {
				retryCount++;
				setTimeout(() => {
					fetchBowinsDesignPrice();
				}, 5000); 
			}

		} finally {
			if (fetchButton) {
				fetchButton.disabled = false;
				fetchButton.classList.remove('loading');
				fetchButtonText.innerHTML = ' ดึงข้อมูลราคา';
			}
		}
	}

	function showMessage(text, type, elementId) {
		const messageDiv = document.getElementById(elementId);
		if (messageDiv) {
			messageDiv.textContent = text;
			messageDiv.className = `message ${type}`;
			messageDiv.style.display = 'block';

			setTimeout(() => {
				messageDiv.style.display = 'none';
			}, 5000);
		}
	}

	function copyText1() {
		const copyTextarea = document.getElementById("copyText1");
		if (copyTextarea.value.trim() === '' || copyTextarea.value.includes('กำลังดึงข้อมูล')) {
			showMessage('กรุณาดึงข้อมูลราคาก่อนคัดลอก', 'error', 'message2');
			return;
		}

		copyTextarea.select();
		copyTextarea.setSelectionRange(0, copyTextarea.value.length);

		try {
			document.execCommand("copy");
			showMessage('คัดลอกข้อความ Bowins Design สำเร็จ!', 'success', 'message2');
		} catch (err) {
			showMessage('ไม่สามารถคัดลอกได้', 'error', 'message2');
		}
	}

	function copyText() {
		const copyTextElement = document.getElementById("copyText");
		copyTextElement.select();
		copyTextElement.setSelectionRange(0, 99999);

		try {
			document.execCommand("copy");
			showMessage('คัดลอกข้อความประกาศราคาสำเร็จ!', 'success', 'message1');
		} catch (err) {
			showMessage('ไม่สามารถคัดลอกได้', 'error', 'message1');
		}
	}

	function startAutoUpdate() {
		const autoUpdateButton = document.getElementById('autoUpdateButton');
		const autoUpdateText = document.getElementById('autoUpdateText');

		if (!isAutoUpdating) {
			isAutoUpdating = true;
			autoUpdateInterval = setInterval(fetchBowinsDesignPrice, 30000); 
			autoUpdateButton.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
			autoUpdateText.innerHTML = '<i class="fas fa-stop"></i> หยุดอัพเดทอัตโนมัติ';
			showMessage('เริ่มอัพเดทอัตโนมัติทุก 30 วินาที', 'success', 'message2');
		} else {
			stopAutoUpdate();
		}
	}

	function stopAutoUpdate() {
		if (autoUpdateInterval) {
			clearInterval(autoUpdateInterval);
			autoUpdateInterval = null;
		}
		isAutoUpdating = false;
		const autoUpdateButton = document.getElementById('autoUpdateButton');
		const autoUpdateText = document.getElementById('autoUpdateText');
		autoUpdateButton.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
		autoUpdateText.innerHTML = ' เริ่มอัพเดทอัตโนมัติ';
		showMessage('หยุดอัพเดทอัตโนมัติแล้ว', 'success', 'message2');
	}

	function downloadByHtml2Canvas() {
		html2canvas(document.querySelector('#html2canvas')).then((canvas) => {
			const name = 'price';
			let today = new Date();
			let dd = today.getDate();
			let mm = today.getMonth() + 1;
			let fullYear = today.getFullYear();
			if (dd < 10) {
				dd = '0' + dd;
			}
			if (mm < 10) {
				mm = '0' + mm;
			}
			today = fullYear + '-' + mm + '-' + dd;
			let img = canvas.toDataURL('image/png');
			downloadImage(img, `${name}_${today}`);
		});
	}

	function downloadImage(blob, fileName) {
		const fakeLink = window.document.createElement('a');
		fakeLink.style = 'display:none;';
		fakeLink.download = fileName;
		fakeLink.href = blob;
		document.body.appendChild(fakeLink);
		fakeLink.click();
		document.body.removeChild(fakeLink);
		fakeLink.remove();
	}

	document.addEventListener('DOMContentLoaded', function() {
		setTimeout(fetchBowinsDesignPrice, 1000);
	});

	window.addEventListener('beforeunload', function() {
		if (autoUpdateInterval) {
			clearInterval(autoUpdateInterval);
		}
	});
</script>