	fn.app.sales.quick_order.dialog_remove = function() {
		var item_selected = $("#tblQuick_order").data("selected");
		$.ajax({
			url: "apps/sales/view/dialog.quick_order.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_quick_order").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_quick_order").modal("show");
				$("#dialog_remove_quick_order .btnConfirm").click(function(){
					fn.app.sales.quick_order.remove();
				});
			}
		});
	};

	fn.app.sales.quick_order.remove = function(id){
		if(typeof id != "undefined"){
			fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
				$.post("apps/sales/xhr/action-remove-quick_order.php",{item:id},function(response){
					$("#tblQuickOrder").DataTable().draw();
					fn.notify.successbox("","Remove Success");
				});
			});
		}else{
			var item_selected = $("#tblQuick_order").data("selected");
			$.post("apps/sales/xhr/action-remove-quick_order.php",{items:item_selected},function(response){
				$("#tblQuick_order").data("selected",[]);
				$("#tblQuick_order").DataTable().draw();
				$("#dialog_remove_quick_order").modal("hide");
			});
		}
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sales.quick_order.dialog_remove()",
		caption : "Remove"
	}));
