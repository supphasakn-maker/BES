	fn.app.sales.order.dialog_remove = function() {
		var item_selected = $("#tblOrder").data("selected");
		$.ajax({
			url: "apps/sales/view/dialog.order.remove.php",
			data: {items:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_order").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_order").modal("show");
				$("#dialog_remove_order .btnConfirm").click(function(){
					fn.app.sales.order.remove();
				});
			}
		});
	};

	fn.app.sales.order.remove = function(id){
		
		if(typeof id != "undefined"){
			fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
				$.post("apps/sales/xhr/action-remove-order.php",{item:id},function(response){
					$("#tblOrder").DataTable().draw();
					fn.notify.successbox("","Remove Success");
				});
			});
		}else{
			var item_selected = $("#tblOrder").data("selected");
				$.post("apps/sales/xhr/action-remove-order.php",{items:item_selected},function(response){
					$("#tblOrder").data("selected",[]);
					$("#tblOrder").DataTable().draw();
					$("#dialog_remove_order").modal("hide");
				});
		}
	};
	
	fn.app.sales.order.dialog_soft_remove = function() {
		var item_selected = $("#tblOrder").data("selected");
		$.ajax({
			url: "apps/sales/view/dialog.order.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_order").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_order").modal("show");
				$("#dialog_remove_order .btnConfirm").click(function(){
					fn.app.sales.order.remove();
				});
			}
		});
	};

	fn.app.sales.order.remove_soft = function(id){
		
		if(typeof id != "undefined"){
			fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
				$.post("apps/sales/xhr/action-remove-order.php",{item:id},function(response){
					$("#tblOrder").DataTable().draw();
					fn.notify.successbox("","Remove Success");
				});
			});
		}else{
			var item_selected = $("#tblOrder").data("selected");
				$.post("apps/sales/xhr/action-remove-order.php",{items:item_selected},function(response){
					$("#tblOrder").data("selected",[]);
					$("#tblOrder").DataTable().draw();
					$("#dialog_remove_order").modal("hide");
				});
		}
	};
	
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sales.order.dialog_remove()",
		caption : "Remove"
	}));
