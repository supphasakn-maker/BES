
	fn.app.accctrl.user.dialog_edit = function(id) {
		$.ajax({
			url: "apps/accctrl/view/dialog.user.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_user"});
				$('select[name=country]').select2();
				
				fn.app.contact.address.initial(
					"form[name=form_edituser] select[name=country]",
					"form[name=form_edituser] select[name=city]",
					"form[name=form_edituser] select[name=district]",
					"form[name=form_edituser] select[name=subdistrict]");
			}	
		});
	};
	
	fn.app.accctrl.user.edit = function(){
		$.post('apps/accctrl/xhr/action-edit-user.php',$('form[name=form_edituser]').serialize(),function(response){
			if(response.success){
				$("#tblUser").DataTable().draw();
				$("#dialog_edit_user").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
