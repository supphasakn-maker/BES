	fn.app.sales.order.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.order.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_order"});
				
				$("input[name=delivery_lock]").change(function(){
					$("input[name=delivery_date]").prop('readOnly',$(this).prop('checked'));
				}).change();
			}
		});
	};

	fn.app.sales.order.edit = function(){
		$.post("apps/sales/xhr/action-edit-order.php",$("form[name=form_editorder]").serialize(),function(response){
			if(response.success){
				$("#tblOrder").DataTable().draw();
				$("#dialog_edit_order").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
