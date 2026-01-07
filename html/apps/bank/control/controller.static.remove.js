	fn.app.bank.static.dialog_remove = function() {
		var item_selected = $("#tblStatic").data("selected");
		$.ajax({
			url: "apps/bank/view/dialog.static.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_static").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_static").modal("show");
				$("#dialog_remove_static .btnConfirm").click(function(){
					fn.app.bank.static.remove();
				});
			}
		});
	};

	fn.app.bank.static.remove = function(){
		var item_selected = $("#tblStatic").data("selected");
		$.post("apps/bank/xhr/action-remove-static.php",{items:item_selected},function(response){
			$("#tblStatic").data("selected",[]);
			$("#tblStatic").DataTable().draw();
			$("#dialog_remove_static").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.bank.static.dialog_remove()",
		caption : "Remove"
	}));
