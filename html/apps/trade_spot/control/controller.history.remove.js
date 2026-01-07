	fn.app.trade_spot.history.dialog_remove = function() {
		var item_selected = $("#tblHistory").data("selected");
		$.ajax({
			url: "apps/trade_spot/view/dialog.history.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_history").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_history").modal("show");
				$("#dialog_remove_history .btnConfirm").click(function(){
					fn.app.trade_spot.history.remove();
				});
			}
		});
	};

	fn.app.trade_spot.history.remove = function(){
		var item_selected = $("#tblHistory").data("selected");
		$.post("apps/trade_spot/xhr/action-remove-history.php",{items:item_selected},function(response){
			$("#tblHistory").data("selected",[]);
			$("#tblHistory").DataTable().draw();
			$("#dialog_remove_history").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.trade_spot.history.dialog_remove()",
		caption : "Remove"
	}));
