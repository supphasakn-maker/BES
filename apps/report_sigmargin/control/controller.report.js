	
	fn.app.report_sigmargin.generate = function(){
		App.startLoading()
		$.post("apps/report_sigmargin/view/report.php",$("form[name=report]").serialize(),function(html){
			App.stopLoading();
			$("#output").html(html);

			
		},"html");
		
	}
	
	$("select[name=period]").change(function(){
		$(".display-group").hide();
		$("div[display-group="+$(this).val()+"]").show();
		$("div[display-group=default]").show();
	}).change();


