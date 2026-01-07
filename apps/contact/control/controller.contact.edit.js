
	fn.app.contact.contact.dialog_edit = function(id) {
		$.ajax({
			url: "apps/contact/view/dialog.contact.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_contact"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_editcontact] select[name=country]",
					"form[name=form_editcontact] select[name=city]",
					"form[name=form_editcontact] select[name=district]",
					"form[name=form_editcontact] select[name=subdistrict]");
			}	
		});
	};
	
	fn.app.contact.contact.edit = function(){
		$.post('apps/contact/xhr/action-edit-contact.php',$('form[name=form_editcontact]').serialize(),function(response){
			if(response.success){
				$("#tblContact").DataTable().ajax.reload(null,false);
				$("#dialog_edit_contact").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};

