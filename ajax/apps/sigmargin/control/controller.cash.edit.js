	fn.app.sigmargin.cash.dialog_edit = function(id) {
		$.ajax({
		url: "apps/sigmargin/view/dialog.cash.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_cash"});
			}
		});
	};

	fn.app.sigmargin.cash.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-cash.php",$("form[name=form_editcash]").serialize(),function(response){
			if(response.success){
				$("#tblCash").DataTable().draw();
				$("#dialog_edit_cash").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
