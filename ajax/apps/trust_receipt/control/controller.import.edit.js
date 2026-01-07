	fn.app.import.import.dialog_edit = function(id) {
		$.ajax({
			url: "apps/import/view/dialog.import.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_import"});
			}
		});
	};

	fn.app.import.import.edit = function(){
		$.post("apps/import/xhr/action-edit-import.php",$("form[name=form_editimport]").serialize(),function(response){
			if(response.success){
				$("#tblImport").DataTable().draw();
				$("#dialog_edit_import").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
