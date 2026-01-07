	fn.app.sigmargin.silver.dialog_remove = function() {
		var item_selected = $("#tblSilver").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.silver.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_silver").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_silver").modal("show");	
				$("#dialog_remove_silver .btnConfirm").click(function(){
					fn.app.sigmargin.silver.remove();
				});
			}
		});
	};

	fn.app.sigmargin.silver.remove = function(){
		var item_selected = $("#tblSilver").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-silver.php",{items:item_selected},function(response){
			$("#tblSilver").data("selected",[]);
			$("#tblSilver").DataTable().draw();
			$("#dialog_remove_silver").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.silver.dialog_remove()",
		caption : "Remove"
	}));
