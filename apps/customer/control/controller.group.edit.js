	fn.app.customer.group.dialog_edit = function(id) {
		$.ajax({
			url: "apps/customer/view/dialog.group.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_group"});
			}
		});
	};

	fn.app.customer.group.edit = function(){
		$.post("apps/customer/xhr/action-edit-group.php",$("form[name=form_editgroup]").serialize(),function(response){
			if(response.success){
				$("#tblGroup").DataTable().draw();
				$("#dialog_edit_group").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
