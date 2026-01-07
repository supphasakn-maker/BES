	$("select[name=period]").change(function(){
		$(".display-group").hide();
		$("div[display-group="+$(this).val()+"]").show();
	}).change();

	// Search Button
	$("form[name=report] select[name=type]").change(function(){
		$.post("apps/report_sales/view/"+$(this).val()+".php",$("form[name=report]").serialize(),function(html){
			$("#report_zone").html(html);
		},"html");
	}).change();

