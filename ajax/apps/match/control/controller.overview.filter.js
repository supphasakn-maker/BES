	fn.app.match.overview.dialog_filter = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.overview.filter.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_filter_overview"});
			}
		});
	};

	fn.app.match.overview.filter = function(){
		$.post("apps/match/xhr/action-filter-overview.php",$("form[name=form_filteroverview]").serialize(),function(response){
			if(response.success){
				$("#tblOverview").DataTable().draw();
				$("#dialog_filter_overview").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
