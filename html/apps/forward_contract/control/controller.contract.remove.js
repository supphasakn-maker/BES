	fn.app.forward_contract.contract.dialog_remove = function() {
		var item_selected = $("#tblContract").data("selected");
		$.ajax({
			url: "apps/forward_contract/view/dialog.contract.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_contract").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_contract").modal("show");
				$("#dialog_remove_contract .btnConfirm").click(function(){
					fn.app.forward_contract.contract.remove();
				});
			}
		});
	};

	fn.app.forward_contract.contract.remove = function(){
		var item_selected = $("#tblContract").data("selected");
		$.post("apps/forward_contract/xhr/action-remove-contract.php",{items:item_selected},function(response){
			$("#tblContract").data("selected",[]);
			$("#tblContract").DataTable().draw();
			$("#dialog_remove_contract").modal("hide");
		});
	};
	
	$(".btn-area-transfer").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.forward_contract.contract.dialog_remove()",
		caption : "Remove"
	}));
