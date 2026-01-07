fn.app.report_bwd.generate = function () {
	App.startLoading()
	$.post("apps/report_bwd/view/" + $("select[name=type]").val() + "/output.php", $("form[name=report_bwd]").serialize(), function (html) {
		$("#output").html(html);

		App.stopLoading();
	}, "html");

}

$("select[name=type]").change(function () {
	$("#output").html('<div class="alert alert-danger">กดปุ่ม Search เพื่อทำรายงาน</div>');
	$(".display-group").hide();
	switch ($(this).val()) {
		case "sales_stock":
			$("div[display-group=monthly]").show();
			break;
		case "sales_product":
			$("div[display-group=yearly]").show();
			$("div[display-group=product]").show();
			break;
		case "sales_all_product":
			$("div[display-group=yearly]").show();
			break;
		case "sale_monthly":
			$("div[display-group=monthly]").show();
			break;
		case "sale_order_daily":
			$("div[display-group=daily]").show();
			break;
		case "sale_order_monthly":
			$("div[display-group=monthly]").show();
			break;
		case "sale_order_yearly":
			$("div[display-group=yearly]").show();
			break;
		case "sale_delivery_daily":
			$("div[display-group=daily]").show();
			break;
		case "sale_delivery_monthly":
			$("div[display-group=monthly]").show();
			break;
		case "sale_delivery_yearly":
			$("div[display-group=yearly]").show();
			break;
		case "sale_order_platform_daily":
			$("div[display-group=daily]").show();
			$("div[display-group=platform]").show();
			break;
		case "sale_order_platform_monthly":
			$("div[display-group=monthly]").show();
			$("div[display-group=platform]").show();
			break;
		case "sale_platform_monthly":
			$("div[display-group=monthly]").show();
			break;
		case "sale_platform_yearly":
			$("div[display-group=yearly]").show();
			break;
	}


}).change();

