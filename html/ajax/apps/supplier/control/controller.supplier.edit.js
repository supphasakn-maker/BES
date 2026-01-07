	fn.app.supplier.supplier.dialog_edit = function(id) {
		$.ajax({
			url: "apps/supplier/view/dialog.supplier.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_supplier"});
			}
		});
	};

	fn.app.supplier.supplier.edit = function(){
		$.post("apps/supplier/xhr/action-edit-supplier.php",$("form[name=form_editsupplier]").serialize(),function(response){
			if(response.success){
				$("#tblSupplier").DataTable().draw();
				$("#dialog_edit_supplier").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
