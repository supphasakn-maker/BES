	
	fn.app.report_stock.generate = function(){
		App.startLoading()
		$.post("apps/report_stock/view/report.php",$("form[name=report]").serialize(),function(html){
			$("#output").html(html);
			$("#output table").DataTable({
				//responsive: true,
				"bStateSave": true,
				"autoWidth" : true,
				"searching": false,
				"paging": false,
				"bInfo" : false,
				"ordering": false

			});
			App.stopLoading();
		},"html");
		
	}
	
	$("select[name=period]").change(function(){
		$(".display-group").hide();
		$("div[display-group="+$(this).val()+"]").show();
	}).change();


