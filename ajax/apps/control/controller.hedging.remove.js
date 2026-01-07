	fn.app.purchase.hedging.dialog_remove = function() {
		var item_selected = $("#tblHedging").data("selected");
		$.ajax({
			url: "apps/purchase/view/dialog.hedging.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_hedging").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_hedging").modal("show");
				$("#dialog_remove_hedging .btnConfirm").click(function(){
					fn.app.purchase.hedging.remove();
				});
			}
		});
	};

	fn.app.purchase.hedging.remove = function(){
		var item_selected = $("#tblHedging").data("selected");
		$.post("apps/purchase/xhr/action-remove-hedging.php",{items:item_selected},function(response){
			$("#tblHedging").data("selected",[]);
			$("#tblHedging").DataTable().draw();
			$("#dialog_remove_hedging").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.purchase.hedging.dialog_remove()",
		caption : "Remove"
	}));
