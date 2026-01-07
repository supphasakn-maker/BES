	
	fn.app.bank.weeklyreport.load_page = function() {
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();
		$.ajax({
			url: "apps/bank/xhr/action-load-weekly-report.php",
			data : { date_from: date_from, date_to: date_to },
			type: "POST",
			dataType: "html",
			success: function(html){
				$("#output").html(html);
			}
		});
	};

