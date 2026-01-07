	fn.app.trade_spot.trading.dialog_add = function() {
		$.ajax({
			url: "apps/trade_spot/view/dialog.trading.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_trading"});
			}
		});
	};

	fn.app.trade_spot.trading.add = function(){
		$.post("apps/trade_spot/xhr/action-add-trading.php",{
			purchase : $("#tblPurchase").data("selected"),
			sales : $("#tblSales").data("selected"),
			date : $("form[name=adding] input[name=date]").val()
		},function(response){
			if(response.success){
				$("#tblTrading").data( "selected", [] );
				$("#tblSales").data( "selected", [] );
				$("#tblPurchase").data( "selected", [] );
				$("#tblTrading").DataTable().draw();
				$("#tblSales").DataTable().draw();
				$("#tblPurchase").DataTable().draw();
				fn.notify.successbox(response.msg,"Complete");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.trade_spot.trading.dialog_add()",
		caption : "Add"
	}));
