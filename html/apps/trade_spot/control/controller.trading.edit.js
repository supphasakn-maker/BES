	fn.app.trade_spot.trading.dialog_edit = function(id) {
		$.ajax({
			url: "apps/trade_spot/view/dialog.trading.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_trading"});
			}
		});
	};

	fn.app.trade_spot.trading.edit = function(){
		$.post("apps/trade_spot/xhr/action-edit-trading.php",$("form[name=form_edittrading]").serialize(),function(response){
			if(response.success){
				$("#tblTrading").DataTable().draw();
				$("#dialog_edit_trading").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
