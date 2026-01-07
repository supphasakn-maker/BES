	fn.app.sigmargin.rollover.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.rollover.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_rollover"});
			}
		});
	};

	fn.app.sigmargin.rollover.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-rollover.php",$("form[name=form_editrollover]").serialize(),function(response){
			if(response.success){
				$("#tblRollover").DataTable().draw();
				$("#dialog_edit_rollover").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
