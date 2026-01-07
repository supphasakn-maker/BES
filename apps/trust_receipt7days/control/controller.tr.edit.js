	fn.app.trust_receipt.tr.dialog_edit = function(id) {
		$.ajax({
			url: "apps/trust_receipt7days/view/dialog.tr.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_tr"});
			}
		});
	};

	fn.app.trust_receipt.tr.edit = function(){
		$.post("apps/trust_receipt7days/xhr/action-edit-tr.php",$("form[name=form_edittr]").serialize(),function(response){
			if(response.success){
				$("#tblTr").DataTable().draw();
				$("#dialog_edit_tr").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.trust_receipt.tr.dialog_change_date = function(id) {
		$.ajax({
			url: "apps/trust_receipt7days/view/dialog.tr.change_date.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_tr"});
			}
		});
	};

	fn.app.trust_receipt.tr.change_date = function(){
		$.post("apps/trust_receipt7days/xhr/action-edit-payment-change_date.php",$("form[name=form_edittr]").serialize(),function(response){
			if(response.success){
				$("#tblTr").DataTable().draw();
				
				$("#dialog_edit_tr").modal("hide");
				fn.app.trust_receipt.tr.load();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
