	fn.app.sales.packing.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.packing.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_packing"});
			}
		});
	};

	fn.app.sales.packing.edit = function(){
		$.post("apps/sales/xhr/action-edit-packing.php",$("form[name=form_editpacking]").serialize(),function(response){
			if(response.success){
				$("#tblPacking").DataTable().draw();
				$("#dialog_edit_packing").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
