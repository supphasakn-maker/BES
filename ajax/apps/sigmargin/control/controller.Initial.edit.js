	fn.app.sigmargin.Initial.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.Initial.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_Initial"});
			}
		});
	};

	fn.app.sigmargin.Initial.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-Initial.php",$("form[name=form_editInitial]").serialize(),function(response){
			if(response.success){
				$("#tblInitial").DataTable().draw();
				$("#dialog_edit_Initial").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
