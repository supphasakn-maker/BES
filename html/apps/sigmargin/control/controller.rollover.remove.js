	fn.app.sigmargin.rollover.dialog_remove = function() {
		var item_selected = $("#tblRollover").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.rollover.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_rollover").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_rollover").modal("show");
				$("#dialog_remove_rollover .btnConfirm").click(function(){
					fn.app.sigmargin.rollover.remove();
				});
			}
		});
	};

	fn.app.sigmargin.rollover.remove = function(){
		var item_selected = $("#tblRollover").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-rollover.php",{items:item_selected},function(response){
			$("#tblRollover").data("selected",[]);
			$("#tblRollover").DataTable().draw();
			$("#dialog_remove_rollover").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.rollover.dialog_remove()",
		caption : "Remove"
	}));
