
	fn.app.contact.organization.dialog_edit = function(id) {
		$.ajax({
			url: "apps/contact/view/dialog.organization.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_organization"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_editorganization] select[name=country]",
					"form[name=form_editorganization] select[name=city]",
					"form[name=form_editorganization] select[name=district]",
					"form[name=form_editorganization] select[name=subdistrict]");
				
			}
		});
	};
	
	fn.app.contact.organization.edit = function(){
		$.post('apps/contact/xhr/action-edit-organization.php',$('form[name=form_editorganization]').serialize(),function(response){
			if(response.success){
				$("#tblOrganization").DataTable().ajax.reload(null,false);
				$("#dialog_edit_organization").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};

