
	
	fn.app.profile.dialog_setting = function() {
		$.ajax({
			url: "apps/profile/view/dialog.profile.setting.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_setting"});
			}	
		});
	};
	
	fn.app.profile.setting = function(){
		$.post('apps/profile/xhr/action-edit-setting.php',$('form[name=form_editsetting]').serialize(),function(response){
			if(response.success){
				$("#dialog_edit_setting").modal('hide');
				window.location.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
	