	fn.app.trade_spot.trading.dialog_remove = function() {
		var item_selected = $("#tblTrading").data("selected");
		$.ajax({
			url: "apps/trade_spot/view/dialog.trading.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_trading").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_trading").modal("show");
				$("#dialog_remove_trading .btnConfirm").click(function(){
					fn.app.trade_spot.trading.remove();
				});
			}
		});
	};

	fn.app.trade_spot.trading.remove = function(){
		var item_selected = $("#tblTrading").data("selected");
		$.post("apps/trade_spot/xhr/action-remove-trading.php",{items:item_selected},function(response){
			$("#tblTrading").data("selected",[]);
			$("#tblTrading").DataTable().draw();
			$("#tblPurchase").DataTable().draw();
			$("#tblSales").DataTable().draw();
			$("#dialog_remove_trading").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.trade_spot.trading.dialog_remove()",
		caption : "Remove"
	}));
