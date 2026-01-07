	fn.app.match.silver.dialog_filter = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.silver.filter.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_filter_silver"});
			}
		});
	};

	fn.app.match.silver.filter = function(){
		$.post("apps/match/xhr/action-filter-silver.php",$("form[name=form_filtersilver]").serialize(),function(response){
			if(response.success){
				$("#tblSilver").DataTable().draw();
				$("#dialog_filter_silver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
