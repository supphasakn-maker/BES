
	fn.app.notify.notification.dialog_remove = function() {
		var item_selected = $("#tblNotification").data("selected");
		$.ajax({
			url: "apps/notify/view/dialog.notification.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_notification"});
				
			}	
		});
	};
	
	fn.app.notify.notification.remove = function(){
		var item_selected = $("#tblNotification").data("selected");
		$.post('apps/notify/xhr/action-remove-notification.php',{items:item_selected},function(response){
			$("#tblNotification").data("selected",[]);
			$("#tblNotification").DataTable().draw();
			$('#dialog_remove_notification').modal('hide');
		});
		
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.notify.notification.dialog_remove()",
		caption : "Remove"
	}));
	
