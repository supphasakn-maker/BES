	fn.app.repack.repack.dialog_edit = function(id) {
		$.ajax({
			url: "apps/repack/view/dialog.repack.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_repack"});
			}
		});
	};

	fn.app.repack.repack.edit = function(){
		$.post("apps/repack/xhr/action-edit-repack.php",$("form[name=form_editrepack]").serialize(),function(response){
			if(response.success){
				$("#tblRepack").DataTable().draw();
				$("#dialog_edit_repack").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
