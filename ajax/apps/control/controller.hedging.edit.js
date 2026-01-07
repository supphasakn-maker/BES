	fn.app.purchase.hedging.dialog_edit = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.hedging.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_hedging"});
			}
		});
	};

	fn.app.purchase.hedging.edit = function(){
		$.post("apps/purchase/xhr/action-edit-hedging.php",$("form[name=form_edithedging]").serialize(),function(response){
			if(response.success){
				$("#tblHedging").DataTable().draw();
				$("#dialog_edit_hedging").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
