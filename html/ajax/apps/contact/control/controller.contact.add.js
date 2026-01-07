
	fn.app.contact.contact.dialog_add = function() {
		$.ajax({
			url: "apps/contact/view/dialog.contact.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_contact"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_addcontact] select[name=country]",
					"form[name=form_addcontact] select[name=city]",
					"form[name=form_addcontact] select[name=district]",
					"form[name=form_addcontact] select[name=subdistrict]");
				fn.app.contact.address.load_country("form[name=form_addcontact] select[name=country]");
			}	
		});
	};

	fn.app.contact.contact.add = function(){
		$.post('apps/contact/xhr/action-add-contact.php',$('form[name=form_addcontact]').serialize(),function(response){
			if(response.success){
				$("#tblContact").DataTable().ajax.reload(null,false);
				$("#dialog_add_contact").modal('hide');
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
		onclick : "fn.app.contact.contact.dialog_add()",
		caption : "Add"
	}));

