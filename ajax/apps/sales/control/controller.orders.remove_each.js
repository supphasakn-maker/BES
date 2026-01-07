	fn.app.sales.orders.dialog_remove_each = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.orders.remove_each.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_each_orders"});
			}
		});
	};

	fn.app.sales.orders.remove_each = function(){
		$.post("apps/sales/xhr/action-remove_each-orders.php",$("form[name=form_remove_eachorders]").serialize(),function(response){
			if(response.success){
				$("#tblOrders").DataTable().draw();
				$("#dialog_remove_each_orders").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
