
	fn.app.profile.mail.dialog_sendmail = function() {
		$.ajax({
			url: "apps/profile/view/dialog.mail.sendmail.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_sendmail"});
			}	
		});
	};
	
	fn.app.profile.mail.sendmail = function(){
		$.post('apps/profile/xhr/action-send-mail.php',$('form[name=form_sendmail]').serialize(),function(response){
			if(response.success){
				$("#dialog_sendmail").modal('hide');
				window.location.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};