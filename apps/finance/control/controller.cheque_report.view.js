
fn.app.finance.cheque_report.load_page = function() {
	var from = $('#from').val();
	var to = $('#to').val();
	$.ajax({
		url: "apps/finance/xhr/action-load-cheque-report.php",
		data : {from: from, to: to},
		type: "POST",
		dataType: "html",
		success: function(html){
			$("#output").html(html);
		}
	});
};