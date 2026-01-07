	fn.app.packing.packing.dialog_remove = function() {
		var item_selected = $("#tblPacking").data("selected");
		$.ajax({
			url: "apps/packing/view/dialog.packing.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_packing").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_packing").modal("show");
				$("#dialog_remove_packing .btnConfirm").click(function(){
					fn.app.packing.packing.remove();
				});
			}
		});
	};

	fn.app.packing.packing.remove = function(){
		var item_selected = $("#tblPacking").data("selected");
		$.post("apps/packing/xhr/action-remove-packing.php",{items:item_selected},function(response){
			$("#tblPacking").data("selected",[]);
			$("#tblPacking").DataTable().draw();
			$("#dialog_remove_packing").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.packing.packing.dialog_remove()",
		caption : "Remove"
	}));
