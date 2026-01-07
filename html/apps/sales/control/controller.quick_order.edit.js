	fn.app.sales.quick_order.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.quick_order.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_quick_order"});
			}
		});
	};

	fn.app.sales.quick_order.edit = function(){
		$.post("apps/sales/xhr/action-edit-quick_order.php",$("form[name=form_editquick_order]").serialize(),function(response){
			if(response.success){
				$("#tblQuick_order").DataTable().draw();
				$("#tblQuickOrder").DataTable().draw();
				$("#dialog_edit_quick_order").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
