
	fn.app.accctrl.user.dialog_remove = function() {
		var item_selected = $("#tblUser").data("selected");
		$.ajax({
			url: "apps/accctrl/view/dialog.user.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_user"});
			}	
		});
		
	};
	
	fn.app.accctrl.user.remove = function(){
		var item_selected = $("#tblUser").data("selected");
		$.post('apps/accctrl/xhr/action-remove-user.php',{items:item_selected},function(response){
			$("#tblUser").data("selected",[]);
			$("#tblUser").DataTable().draw();
			$('#dialog_remove_user').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.accctrl.user.dialog_remove()",
		caption : "Remove"
	}));
	