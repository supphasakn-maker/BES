	
	fn.app.report_financial.generate = function(){
		App.startLoading()
		$.post("apps/report_financial/view/report.php",$("form[name=report]").serialize(),function(html){
			$("#output").html(html);
			$("#output table").DataTable({
				//responsive: true,
				"bStateSave": true,
				"autoWidth" : true
			});
			App.stopLoading();
		},"html");
		
	}
	
	$("select[name=period]").change(function(){
		$(".display-group").hide();
		$("div[display-group="+$(this).val()+"]").show();
		$("div[display-group=default]").show();
	}).change();


