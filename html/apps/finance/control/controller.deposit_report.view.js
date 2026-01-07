
fn.app.finance.deposit_report.load_page = function() {
		var from = $('#from').val();
		var to = $('#to').val();
	$.ajax({
		url: "apps/finance/xhr/action-load-deposit-report.php",
		data : { from: from, to: to },
		type: "POST",
		dataType: "html",
		success: function(html){
			$("#output").html(html);
		}
	});
};