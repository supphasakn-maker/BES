
	fn.app.notify.notification.dialog_edit = function(id) {
		$.ajax({
			url: "apps/notify/view/dialog.notification.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_notification"});
				
			}	
		});
	};
	
	fn.app.notify.notification.edit = function(){
		$.post('apps/notify/xhr/action-edit-notification.php',$('form[name=form_editnotification]').serialize(),function(response){
			if(response.success){
				$("#tblNotification").DataTable().draw();
				$("#dialog_edit_notification").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
