	fn.app.customer.customer.dialog_remove = function() {
		var item_selected = $("#tblCustomer").data("selected");
		$.ajax({
			url: "apps/customer/view/dialog.customer.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_customer").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_customer").modal("show");
				$("#dialog_remove_customer .btnConfirm").click(function(){
					fn.app.customer.customer.remove();
				});
			}
		});
	};

	fn.app.customer.customer.remove = function(){
		var item_selected = $("#tblCustomer").data("selected");
		$.post("apps/customer/xhr/action-remove-customer.php",{items:item_selected},function(response){
			$("#tblCustomer").data("selected",[]);
			$("#tblCustomer").DataTable().draw();
			$("#dialog_remove_customer").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.customer.customer.dialog_remove()",
		caption : "Remove"
	}));
