	
	fn.app.bank.dailyreport.load_page = function() {
		$.ajax({
			url: "apps/bank/xhr/action-load-daily-report.php",
			data : {
				date : $("form[name=filter] [name=date]").val()
			},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("#output").html(html);
			}
		});
	};

