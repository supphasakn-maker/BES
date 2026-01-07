
	$(function(){
		$("select[name=period]").change(function(){
			var period = $(this).val();
			var from = $("input[name=from]");
			var to = $("input[name=to]");
			var start = new Date();
			var today = new Date();
			switch(period){
				case "today":
					from.val(moment(start).format("YYYY-MM-DD")).attr("readonly",true);
					to.val(moment(today).format("YYYY-MM-DD")).attr("readonly",true);
					break;
				case "yesterday":
					start.setDate(today.getDate()-1);
					from.val(moment(start).format("YYYY-MM-DD")).attr("readonly",true);
					to.val(moment(today).format("YYYY-MM-DD")).attr("readonly",true);
					break;
				case "week":
					start.setDate(today.getDate()-7);
					from.val(moment(start).format("YYYY-MM-DD")).attr("readonly",true);
					to.val(moment(today).format("YYYY-MM-DD")).attr("readonly",true);
					break;
				case "month":
					start.setDate(today.getDate()-30);
					from.val(moment(start).format("YYYY-MM-DD")).attr("readonly",true);
					to.val(moment(today).format("YYYY-MM-DD")).attr("readonly",true);
					break;
				case "quarter":
					start.setDate(today.getDate()-90);
					from.val(moment(start).format("YYYY-MM-DD")).attr("readonly",true);
					to.val(moment(today).format("YYYY-MM-DD")).attr("readonly",true);
					break;
				case "year":
					start.setDate(today.getDate()-365);
					from.val(moment(start).format("YYYY-MM-DD")).attr("readonly",true);
					to.val(moment(today).format("YYYY-MM-DD")).attr("readonly",true);
					break;
				case "custom":
					from.attr("readonly",true);
					to.attr("readonly",true);
					break;
			}
		}).change();
	});
	
	fn.app.logger.log.search = function() {
		$.post('apps/logger/xhr/action-search.php',$('form[name=search_filter]').serialize(),function(html){
			$("#report_zone").html(html);
			$("#tblReport").dataTable();
		},'html');
		return false;
	};

	
