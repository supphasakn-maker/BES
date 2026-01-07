	fn.app.sigmargin.transfer.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.transfer.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_transfer"});
			}
		});
	};

	fn.app.sigmargin.transfer.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-transfer.php",$("form[name=form_edittransfer]").serialize(),function(response){
			if(response.success){
				$("#tblTransfer").DataTable().draw();
				$("#dialog_edit_transfer").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
