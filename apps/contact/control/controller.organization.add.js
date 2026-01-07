
	fn.app.contact.organization.dialog_add = function() {
		$.ajax({
			url: "apps/contact/view/dialog.organization.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_organization"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_addorganization] select[name=country]",
					"form[name=form_addorganization] select[name=city]",
					"form[name=form_addorganization] select[name=district]",
					"form[name=form_addorganization] select[name=subdistrict]");
				fn.app.contact.address.load_country("form[name=form_addorganization] select[name=country]");
				
			}	
		});
	};

	fn.app.contact.organization.add = function(){
		$.post('apps/contact/xhr/action-add-organization.php',$('form[name=form_addorganization]').serialize(),function(response){
			if(response.success){
				$("#tblOrganization").DataTable().ajax.reload(null,false);
				$("#dialog_add_organization").modal('hide');
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
		onclick : "fn.app.contact.organization.dialog_add()",
		caption : "Add"
	}));

	