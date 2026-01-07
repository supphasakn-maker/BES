	fn.app.sigmargin.transaction.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.transaction.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_transaction"});
			}
		});
	};

	fn.app.sigmargin.transaction.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-transaction.php",$("form[name=form_edittransaction]").serialize(),function(response){
			if(response.success){
				$("#tblTransaction").DataTable().draw();
				$("#dialog_edit_transaction").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
