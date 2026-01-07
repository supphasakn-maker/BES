
	fn.app.accctrl.account.dialog_remove = function() {
		var item_selected = $("#tblAccount").data("selected");
		$.ajax({
			url: "apps/accctrl/view/dialog.account.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_account"});
			}	
		});
	};
	
	fn.app.accctrl.account.remove = function(){
		var item_selected = $("#tblAccount").data("selected");
		$.post('apps/accctrl/xhr/action-remove-account.php',{items:item_selected},function(response){
			$("#tblAccount").data("selected",[]);
			$("#tblAccount").DataTable().draw();
			$('#dialog_remove_account').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.accctrl.account.dialog_remove()",
		caption : lang.main.remove
	}));
	
	