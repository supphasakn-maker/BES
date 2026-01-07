
	fn.app.profile.dialog_setquote = function() {
		$.ajax({
			url: "apps/profile/view/dialog.profile.quote.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_quote"});
			}	
		});
	};
	
	fn.app.profile.setquote = function(){
		$.post('apps/profile/xhr/action-edit-quote.php',$('form[name=form_editquote]').serialize(),function(response){
			if(response.success){
				$("#dialog_edit_quote").modal('hide');
				window.location.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};