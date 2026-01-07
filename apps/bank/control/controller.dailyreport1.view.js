	
	fn.app.bank.dailyreport1.load_page = function() {
		$.ajax({
			url: "apps/bank/xhr/action-load-daily-report1.php",
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

