	fn.app.bank.statement.dialog_edit = function(id) {
		$.ajax({
			url: "apps/bank/view/dialog.statement.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_statement"});
			}
		});
	};

	fn.app.bank.statement.edit = function(){
		$.post("apps/bank/xhr/action-edit-statement.php",$("form[name=form_editstatement]").serialize(),function(response){
			if(response.success){
				$("#tblStatement").DataTable().draw();
				$("#dialog_edit_statement").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
