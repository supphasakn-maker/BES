
	fn.app.accctrl.group.dialog_remove = function() {
		var item_selected = $("#tblGroup").data("selected");
		$.ajax({
			url: "apps/accctrl/view/dialog.group.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_group"});
				
				
			}	
		});
	};
	
	fn.app.accctrl.group.remove = function(){
		var item_selected = $("#tblGroup").data("selected");
		$.post('apps/accctrl/xhr/action-remove-group.php',{items:item_selected},function(response){
			$("#tblGroup").data("selected",[]);
			$("#tblGroup").DataTable().draw();
			$('#dialog_remove_group').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.accctrl.group.dialog_remove()",
		caption : "Remove"
	}));
	
