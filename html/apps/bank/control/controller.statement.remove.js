	fn.app.bank.statement.dialog_remove = function() {
		var item_selected = $("#tblStatement").data("selected");
		$.ajax({
			url: "apps/bank/view/dialog.statement.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_statement").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_statement").modal("show");
				$("#dialog_remove_statement .btnConfirm").click(function(){
					fn.app.bank.statement.remove();
				});
			}
		});
	};

	fn.app.bank.statement.remove = function(){
		var item_selected = $("#tblStatement").data("selected");
		$.post("apps/bank/xhr/action-remove-statement.php",{items:item_selected},function(response){
			$("#tblStatement").data("selected",[]);
			$("#tblStatement").DataTable().draw();
			$("#dialog_remove_statement").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.bank.statement.dialog_remove()",
		caption : "Remove"
	}));
