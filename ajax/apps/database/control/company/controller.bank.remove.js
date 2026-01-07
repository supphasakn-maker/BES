
	fn.app.database.company.bank.dialog_remove = function() {
		var item_selected = $("#tblDatabase").data("selected");
		$.ajax({
			url: "apps/database/view/company/dialog.bank.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_bank"});
			}	
		});
	};
	
	fn.app.database.company.bank.remove = function(){
		var item_selected = $("#tblDatabase").data("selected");
		$.post('apps/database/xhr/company/action-remove-bank.php',{items:item_selected},function(response){
			$("#tblDatabase").data("selected",[]);
			$("#tblDatabase").DataTable().draw();
			$('#dialog_remove_bank').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.database.company.bank.dialog_remove()",
		caption : "Remove"
	}));
	
	