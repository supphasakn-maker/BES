	fn.app.audit.match.dialog_edit = function(id) {
		$.ajax({
			url: "apps/audit/view/dialog.match.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_match"});
			}
		});
	};

	fn.app.audit.match.edit = function(){
		$.post("apps/audit/xhr/action-edit-match.php",$("form[name=form_editmatch]").serialize(),function(response){
			if(response.success){
				$("#tblMatch").DataTable().draw();
				$("#dialog_edit_match").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
