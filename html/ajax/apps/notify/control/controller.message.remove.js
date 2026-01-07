
	fn.app.notify.message.dialog_remove = function() {
		var item_selected = $("#tblMessage").data("selected");
		$.ajax({
			url: "apps/notify/view/dialog.message.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_message"});
				
			}	
		});
	};
	
	fn.app.notify.message.remove = function(){
		var item_selected = $("#tblMessage").data("selected");
		$.post('apps/notify/xhr/action-remove-message.php',{items:item_selected},function(response){
			$("#tblMessage").data("selected",[]);
			$("#tblMessage").DataTable().draw();
			$('#dialog_remove_message').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.notify.message.dialog_remove()",
		caption : "Remove"
	}));
	

