
	fn.app.sales_screen.quickorder.dialog_remove = function(id) {
		$.ajax({
			url: "apps/sales_screen/view/dialog.quickorder.remove.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_order"});
			}
		});
	};
	
	fn.app.sales_screen.quickorder.remove = function(id){
		$.post('apps/sales_screen/xhr/action-remove-quickorder.php',$('form[name=form_removeorder]').serialize(),function(response){
			if(response.success){
				$("#tblQuickOrder").DataTable().draw();
				$("#dialog_remove_order").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};

