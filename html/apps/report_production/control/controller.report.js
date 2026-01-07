fn.app.report_production.generate = function () {
	App.startLoading();
	$.post(
		"apps/report_production/view/" +
		$("select[name=type]").val() +
		"/output.php",
		$("form[name=report]").serialize(),
		function (html) {
			$("#output").html(html);
			App.stopLoading();
		},
		"html"
	);
};

$("select[name=type]")
	.change(function () {
		$("#output").html(
			'<div class="alert alert-warning">กดปุ่ม Search เพื่อทำรายงาน</div>'
		);
		$(".display-group").hide();
		switch ($(this).val()) {
			case "crucible_daily":
				$("div[display-group=daily]").show();
				break;
			case "crucible_monthly":
				$("div[display-group=monthly]").show();
				break;
			case "crucible":
				$("div[display-group=yearly]").show();
				break;
			case "processingloss":
				$("div[display-group=custom]").show();
				break;
			case "processingloss_product":
				$("div[display-group=custom]").show();
				$("div[display-group=product]").show();
				break;
			case "silver_over_monthly":
				$("div[display-group=monthly]").show();
				break;
		}
	})
	.change();
