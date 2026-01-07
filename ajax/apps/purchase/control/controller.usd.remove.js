	fn.app.purchase.usd.dialog_remove = function() {
		var item_selected = $("#tblUsd").data("selected");
		$.ajax({
			url: "apps/purchase/view/dialog.usd.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_usd").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_usd").modal("show");
				$("#dialog_remove_usd .btnConfirm").click(function(){
					fn.app.purchase.usd.remove();
				});
			}
		});
	};

	fn.app.purchase.usd.remove = function(id){
		if(typeof id != "undefined"){
			fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
				$.post("apps/purchase/xhr/action-remove-usd.php",{item:id},function(response){
					$("#tblPurchase").DataTable().draw();
					$("#tblPending").DataTable().draw();
					fn.notify.successbox("","Remove Success");
				});
			});
		}else{
			var item_selected = $("#tblUsd").data("selected");
			$.post("apps/purchase/xhr/action-remove-usd.php",{items:item_selected},function(response){
				$("#tblUsd").data("selected",[]);
				$("#tblUsd").DataTable().draw();
				$("#dialog_remove_spot").modal("hide");
			});
		}
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.purchase.usd.dialog_remove()",
		caption : "Remove"
	}));
