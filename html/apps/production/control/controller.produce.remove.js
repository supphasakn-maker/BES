	fn.app.production.produce.dialog_remove = function() {
		var item_selected = $("#tblProduce").data("selected");
		$.ajax({
			url: "apps/production/view/dialog.produce.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_produce").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_produce").modal("show");
				$("#dialog_remove_produce .btnConfirm").click(function(){
					fn.app.production.produce.remove();
				});
			}
		});
	};

	fn.app.production.produce.remove = function(){
		var item_selected = $("#tblProduce").data("selected");
		$.post("apps/production/xhr/action-remove-produce.php",{items:item_selected},function(response){
			$("#tblProduce").data("selected",[]);
			$("#tblProduce").DataTable().draw();
			$("#dialog_remove_produce").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.production.produce.dialog_remove()",
		caption : "Remove"
	}));
