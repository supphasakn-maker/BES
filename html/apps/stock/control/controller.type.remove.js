	fn.app.stock.type.dialog_remove = function() {
		var item_selected = $("#tblType").data("selected");
		$.ajax({
			url: "apps/stock/view/dialog.type.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_type").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_type").modal("show");
				$("#dialog_remove_type .btnConfirm").click(function(){
					fn.app.stock.type.remove();
				});
			}
		});
	};

	fn.app.stock.type.remove = function(){
		var item_selected = $("#tblType").data("selected");
		$.post("apps/stock/xhr/action-remove-type.php",{items:item_selected},function(response){
			$("#tblType").data("selected",[]);
			$("#tblType").DataTable().draw();
			$("#dialog_remove_type").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.stock.type.dialog_remove()",
		caption : "Remove"
	}));
