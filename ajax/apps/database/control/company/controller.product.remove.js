
	fn.app.database.company.product.dialog_remove = function() {
		var item_selected = $("#tblDatabase").data("selected");
		$.ajax({
			url: "apps/database/view/company/dialog.product.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_product"});
			}	
		});
	};
	
	fn.app.database.company.product.remove = function(){
		var item_selected = $("#tblDatabase").data("selected");
		$.post('apps/database/xhr/company/action-remove-product.php',{items:item_selected},function(response){
			$("#tblDatabase").data("selected",[]);
			$("#tblDatabase").DataTable().draw();
			$('#dialog_remove_product').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.database.company.product.dialog_remove()",
		caption : "Remove"
	}));
	
	