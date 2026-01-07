	fn.app.supplier.group.dialog_remove = function() {
		var item_selected = $("#tblGroup").data("selected");
		$.ajax({
			url: "apps/supplier/view/dialog.group.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_group").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_group").modal("show");
				$("#dialog_remove_group .btnConfirm").click(function(){
					fn.app.supplier.group.remove();
				});
			}
		});
	};

	fn.app.supplier.group.remove = function(){
		var item_selected = $("#tblGroup").data("selected");
		$.post("apps/supplier/xhr/action-remove-group.php",{items:item_selected},function(response){
			$("#tblGroup").data("selected",[]);
			$("#tblGroup").DataTable().draw();
			$("#dialog_remove_group").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.supplier.group.dialog_remove()",
		caption : "Remove"
	}));
