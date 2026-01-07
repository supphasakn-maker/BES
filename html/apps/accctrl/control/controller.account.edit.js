
	fn.app.accctrl.account.dialog_edit = function(id) {
		$.ajax({
			url: "apps/accctrl/view/dialog.account.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_account"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_editaccount] select[name=country]",
					"form[name=form_editaccount] select[name=city]",
					"form[name=form_editaccount] select[name=district]",
					"form[name=form_editaccount] select[name=subdistrict]");
				
			}
		});
	};
	
	fn.app.accctrl.account.edit = function(){
		$.post('apps/accctrl/xhr/action-edit-account.php',$('form[name=form_editaccount]').serialize(),function(response){
			if(response.success){
				$("#tblAccount").DataTable().draw();
				$("#dialog_edit_account").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
