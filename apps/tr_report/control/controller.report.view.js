
fn.app.tr_report.report.load_page = function() {
	$.post("apps/tr_report/xhr/action-load-report.php",$("form[name=filter]").serialize(),function(response){
		$("#output").html(response)
		/*
		$("#tblTRMain").DataTable({
			//responsive: true,
			"bStateSave": true,
			"autoWidth" : true
		});
		*/
	},"html");
}
