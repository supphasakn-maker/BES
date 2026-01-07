	fn.app.sales.order.dialog_remove_each = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.order.remove_each.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_each_order"});
			}
		});
	};

	fn.app.sales.order.remove_each = function(){
		$.post("apps/sales/xhr/action-remove_each-order.php",$("form[name=form_remove_eachorder]").serialize(),function(response){
			if(response.success){
				$("#tblOrder").DataTable().draw();
				$("#dialog_remove_each_order").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
