
	fn.app.notify.notification.dialog_add = function() {
		$.ajax({
			url: "apps/notify/view/dialog.notification.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_notification"});
				
			}	
		});
	};

	fn.app.notify.notification.add = function(){
		$.post('apps/notify/xhr/action-add-notification.php',$('form[name=form_addnotification]').serialize(),function(response){
			if(response.success){
				$("#tblNotification").DataTable().draw();
				$("#dialog_add_notification").modal('hide');
				$("#form_addnotification")[0].reset();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.notify.notification.dialog_add()",
		caption : "Add"
	}));

