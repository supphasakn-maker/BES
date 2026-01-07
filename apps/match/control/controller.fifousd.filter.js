	fn.app.match.fifousd.dialog_filter = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.fifousd.filter.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_filter_fifousd"});
			}
		});
	};

	fn.app.match.fifousd.filter = function(){
		$.post("apps/match/xhr/action-filter-fifousd.php",$("form[name=form_filterfifousd]").serialize(),function(response){
			if(response.success){
				$("#tblFifousd").DataTable().draw();
				$("#dialog_filter_fifousd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
