	fn.app.purchase.usd.dialog_edit = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.usd.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_usd"});
			}
		});
	};

	fn.app.purchase.usd.edit = function(){
		$.post("apps/purchase/xhr/action-edit-usd.php",$("form[name=form_editusd]").serialize(),function(response){
			if(response.success){
				$("#tblUsd").DataTable().draw();
				$("#tblPurchase").DataTable().draw();
				$("#tblPending").DataTable().draw();
				$("#dialog_edit_usd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
