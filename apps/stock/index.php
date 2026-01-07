<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/session.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);

$panel->setApp("stock", "Stock");
/** default เป็น card */
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'card');

$panel->setMeta(array(
	array("adjust", "Adjust", "far fa-user"),
	array("type",   "Type",   "far fa-user"),
	array("card",   "Card",   "far fa-user"),
));
?>
<?php $panel->PageBreadcrumb(); ?>

<div class="row">
	<div class="col-xl-12">
		<?php $panel->EchoInterface(); ?>
		<!-- ที่วางเนื้อหา Card เมื่อเรียกผ่าน hash -->
		<div id="stock-root" class="mt-3"></div>
	</div>
</div>

<script>
	// ===== Loader ตาม hash (รองรับ: #apps/stock/index.php?view=card&from=...&days=... หรือ &year=YYYY) =====
	(function() {
		const STOCK_ROUTE = '#apps/stock/index.php';

		function parseHash() {
			const h = window.location.hash || '';
			const [path, query = ''] = h.split('?');
			return {
				path,
				params: new URLSearchParams(query)
			};
		}

		function wantCard() {
			const {
				path,
				params
			} = parseHash();
			return path === STOCK_ROUTE && (params.get('view') || 'card') === 'card';
		}

		function loadCardFromHash() {
			const {
				params
			} = parseHash();
			const q = new URLSearchParams();
			// map hash params -> real query สำหรับ PHP
			const from = params.get('from');
			const days = params.get('days');
			const year = params.get('year'); // โหมดรายปี

			if (year) q.set('year', year);
			if (from) q.set('from', from);
			if (days) q.set('days', days);

			// ให้ PHP รู้ว่าเป็น view=card (ถ้าเปิดไฟล์โดยตรงก็ใช้ได้)
			q.set('view', 'card');

			const url = '/apps/stock/card.php' + (q.toString() ? ('?' + q.toString()) : '');
			const root = document.getElementById('stock-root');
			root.innerHTML = '<div class="p-3 text-muted">กำลังโหลด...</div>';

			fetch(url, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(r => r.text())
				.then(html => {
					root.innerHTML = html;
					bindPager();
					bindYearSelector();
				})
				.catch(err => {
					console.error(err);
					root.innerHTML = '<div class="p-3 text-danger">โหลดไม่สำเร็จ</div>';
				});
		}

		function gotoCard(paramsObj) {
			const sp = new URLSearchParams(paramsObj);
			sp.set('view', 'card'); // บังคับอยู่หน้า card
			const nextHash = `${STOCK_ROUTE}?${sp.toString()}`;
			if (window.location.hash === nextHash) {
				loadCardFromHash();
			} else {
				window.location.hash = nextHash;
			}
		}

		function bindPager() {
			// ปุ่มเลื่อนช่วงวัน
			document.querySelectorAll('[data-stock-goto]').forEach(btn => {
				btn.addEventListener('click', () => {
					const p = {};
					if (btn.dataset.from) p.from = btn.dataset.from;
					if (btn.dataset.days) p.days = btn.dataset.days;
					// เคลียร์ year ถ้ามี (เข้าสู่โหมดช่วงวัน)
					p.view = 'card';
					gotoCard(p);
				});
			});
			// ปุ่มเลื่อน "ปี"
			document.querySelectorAll('[data-stock-goto-year]').forEach(btn => {
				btn.addEventListener('click', () => {
					const year = btn.dataset.stockGotoYear;
					gotoCard({
						year,
						view: 'card'
					});
				});
			});
		}

		function bindYearSelector() {
			const sel = document.getElementById('year-select');
			if (!sel) return;
			sel.addEventListener('change', () => {
				const year = sel.value;
				gotoCard({
					year,
					view: 'card'
				});
			});
		}

		window.addEventListener('hashchange', () => {
			if (wantCard()) loadCardFromHash();
		});

		// load ครั้งแรก
		if (wantCard()) loadCardFromHash();
	})();
</script>

<script>
	// โหลด plugin ต่างๆ ของระบบตามเดิม
	var plugins = [
		'apps/stock/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/select2/js/select2.min.js',
		'plugins/sweetalert/sweetalert-dev.js',
		'plugins/sweetalert/sweetalert.css',
		'plugins/moment/moment.min.js'
	];
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
		<?php
		switch ($panel->getView()) {
			case "adjust":
				include "control/controller.adjust.view.js";
				if ($os->allow("stock", "add")) include "control/controller.adjust.add.js";
				if ($os->allow("stock", "edit")) include "control/controller.adjust.edit.js";
				if ($os->allow("stock", "remove")) include "control/controller.adjust.remove.js";
				break;
			case "type":
				include "control/controller.type.view.js";
				if ($os->allow("stock", "add")) include "control/controller.type.add.js";
				if ($os->allow("stock", "edit")) include "control/controller.type.edit.js";
				if ($os->allow("stock", "remove")) include "control/controller.type.remove.js";
				break;
				// view=card จะถูกโหลดผ่าน hash loader ด้านบน
		}
		?>
	}).then(() => App.stopLoading())
</script>