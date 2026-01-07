	fn.app.sales.order.dialog_print = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.order.print.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_print_order"});
			}
		});
	};

	fn.app.sales.order.print = function(){
		$.post("apps/sales/xhr/action-print-order.php",$("form[name=form_printorder]").serialize(),function(response){
			if(response.success){
				$("#tblOrder").DataTable().draw();
				$("#dialog_print_order").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
