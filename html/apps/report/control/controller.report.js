
fn.app.report.generate = function () {
	App.startLoading()
	$.post("apps/report/view/" + $("select[name=type]").val() + "/output.php", $("form[name=report]").serialize(), function (html) {
		$("#output").html(html);

		App.stopLoading();
	}, "html");

}

$("select[name=type]").change(function () {
	$("#output").html('<div class="alert alert-warning">กดปุ่ม Search เพื่อทำรายงาน</div>');
	$(".display-group").hide();
	switch ($(this).val()) {
		case "sales_keyclient":
			$("div[display-group=yearly]").show();
			$("div[display-group=customer]").show();
			break;
		case "sales_product":
			$("div[display-group=yearly]").show();
			$("div[display-group=product]").show();
			break;
		case "sales_newclient":
			$("div[display-group=yearly]").show();
			$("div[display-group=newclient]").show();
			break;
		case "sales_mountly":
			$("div[display-group=monthly]").show();
			break;
		case "sales_yearly":
			$("div[display-group=yearly]").show();
			break;
		case "sales_bygroup":
			$("div[display-group=yearly]").show();
			$("div[display-group=customer_group]").show();
			break;
		case "sales_compare_yearly":
			$("div[display-group=year_list]").show();
			break;
		case "sales_all_yearly":
			$("div[display-group=yearly]").show();
			$("div[display-group=sale_group]").show();
			break;
		case "sales_all_monthly":
			$("div[display-group=monthly]").show();
			$("div[display-group=sale_group]").show();
			break;
		case "sales_over":
			$("div[display-group=monthly]").show();
			break;


	}


}).change();

