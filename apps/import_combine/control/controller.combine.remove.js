	fn.app.import_combine.combine.dialog_remove = function(id) {

		$.ajax({
			url: "apps/import_combine/view/dialog.combine.remove.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_import").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_import").modal("show");
			}
		});
	};

	fn.app.import_combine.combine.remove = function(id){
		var item_selected = $("#tblImport").data("selected");
		$.post("apps/import_combine/xhr/action-remove-combine.php",{id:id},function(response){
			$("#tblImport").data("selected",[]);
			$("#tblImport").DataTable().draw();
			$("#tblCombine").DataTable().draw();
			$("#tblSpot").DataTable().draw();
			$("#dialog_remove_import").modal("hide");
		});
	};
