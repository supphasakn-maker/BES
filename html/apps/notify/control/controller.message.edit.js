
	fn.app.notify.message.dialog_edit = function(id) {
		$.ajax({
			url: "apps/notify/view/dialog.message.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_message"});
				
			}	
		});
	};
	
	fn.app.notify.message.edit = function(){
		$.post('apps/notify/xhr/action-edit-message.php',$('form[name=form_editmessage]').serialize(),function(response){
			if(response.success){
				$("#tblMessage").DataTable().draw();
				$("#dialog_edit_message").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
