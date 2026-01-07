	fn.app.bank.static.dialog_edit = function(id) {
		$.ajax({
			url: "apps/bank/view/dialog.static.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_static"});
			}
		});
	};

	fn.app.bank.static.edit = function(){
		$.post("apps/bank/xhr/action-edit-static.php",$("form[name=form_editstatic]").serialize(),function(response){
			if(response.success){
				$("#tblStatic").DataTable().draw();
				$("#dialog_edit_static").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
