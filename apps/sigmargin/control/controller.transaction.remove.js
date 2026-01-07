	fn.app.sigmargin.transaction.dialog_remove = function() {
		var item_selected = $("#tblTransaction").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.transaction.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_transaction").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_transaction").modal("show");
				$("#dialog_remove_transaction .btnConfirm").click(function(){
					fn.app.sigmargin.transaction.remove();
				});
			}
		});
	};

	fn.app.sigmargin.transaction.remove = function(){
		var item_selected = $("#tblTransaction").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-transaction.php",{items:item_selected},function(response){
			$("#tblTransaction").data("selected",[]);
			$("#tblTransaction").DataTable().draw();
			$("#dialog_remove_transaction").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.transaction.dialog_remove()",
		caption : "Remove"
	}));
