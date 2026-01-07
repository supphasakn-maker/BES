	fn.app.match.usd.dialog_filter = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.usd.filter.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_filter_usd"});
			}
		});
	};

	fn.app.match.usd.filter = function(){
		$.post("apps/match/xhr/action-filter-usd.php",$("form[name=form_filterusd]").serialize(),function(response){
			if(response.success){
				$("#tblUsd").DataTable().draw();
				$("#dialog_filter_usd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
