fn.app.incomingplan.plan.show = function() {
	App.startLoading();
    $.post("apps/incomingplan/xhr/action-load-show.php",
	{
		created_date_form:$('input[name=created_date_form]').val(),
		created_date_to:$('input[name=created_date_to]').val(),
		date_form:$('input[name=date_form]').val(),
		date_to:$('input[name=date_to]').val()
	},
	function(html){
		$("#display_area").html(html);
		//fn.app.sigmargin.overview.calculate();
		App.stopLoading();
	},"html");
};
$('input[type=date]').change(function(){
	fn.app.incomingplan.plan.show();
})


	


fn.app.incomingplan.alert = function(){
	bootbox.confirm("แจ้งตอนนี้", function(result){
		if(result){
			$.post("apps/incomingplan/xhr/action-alert.php",function(html){
				window.location.reload();
			},"html");
		}
	})
};
