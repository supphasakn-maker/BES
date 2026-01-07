	fn.app.trade_spot.history.dialog_edit = function(id) {
		$.ajax({
			url: "apps/trade_spot/view/dialog.history.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_history"});
			}
		});
	};

	fn.app.trade_spot.history.edit = function(){
		$.post("apps/trade_spot/xhr/action-edit-history.php",$("form[name=form_edithistory]").serialize(),function(response){
			if(response.success){
				$("#tblHistory").DataTable().draw();
				$("#dialog_edit_history").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
