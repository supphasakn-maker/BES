
fn.app.report_sigmargin_stx.generate = function () {
	App.startLoading()
	$.post("apps/report_sigmargin_stx/view/report.php", $("form[name=report]").serialize(), function (html) {
		App.stopLoading();
		$("#output").html(html);


	}, "html");

}

$("select[name=period]").change(function () {
	$(".display-group").hide();
	$("div[display-group=" + $(this).val() + "]").show();
	$("div[display-group=default]").show();
}).change();


