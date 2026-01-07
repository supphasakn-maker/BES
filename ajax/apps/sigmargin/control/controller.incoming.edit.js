	fn.app.sigmargin.incoming.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.incoming.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_incoming"});
			}
		});
	};

	fn.app.sigmargin.incoming.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-incoming.php",$("form[name=form_editincoming]").serialize(),function(response){
			if(response.success){
				$("#tblIncoming").DataTable().draw();
				$("#dialog_edit_incoming").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
