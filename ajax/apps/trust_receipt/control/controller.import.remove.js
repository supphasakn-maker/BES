	fn.app.import.import.dialog_remove = function() {
		var item_selected = $("#tblImport").data("selected");
		$.ajax({
			url: "apps/import/view/dialog.import.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_import").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_import").modal("show");
				$("#dialog_remove_import .btnConfirm").click(function(){
					fn.app.import.import.remove();
				});
			}
		});
	};

	fn.app.import.import.remove = function(){
		var item_selected = $("#tblImport").data("selected");
		$.post("apps/import/xhr/action-remove-import.php",{items:item_selected},function(response){
			$("#tblImport").data("selected",[]);
			$("#tblImport").DataTable().draw();
			$("#tblSpot").DataTable().draw();
			$("#dialog_remove_import").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.import.import.dialog_remove()",
		caption : "Remove"
	}));
