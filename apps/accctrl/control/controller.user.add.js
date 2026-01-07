
	fn.app.accctrl.user.dialog_add = function() {
		$.ajax({
			url: "apps/accctrl/view/dialog.user.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_user"});
				
				fn.app.contact.address.initial(
					"form[name=form_adduser] select[name=country]",
					"form[name=form_adduser] select[name=city]",
					"form[name=form_adduser] select[name=district]",
					"form[name=form_adduser] select[name=subdistrict]");
				fn.app.contact.address.load_country("form[name=form_adduser] select[name=country]");
				
			}	
		});
	};

	fn.app.accctrl.user.add = function(){
		$.post('apps/accctrl/xhr/action-add-user.php',$('form[name=form_adduser]').serialize(),function(response){
			if(response.success){
				$("#tblUser").DataTable().draw();
				$("#dialog_add_user").modal('hide');
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
		onclick : "fn.app.accctrl.user.dialog_add()",
		caption : "Add"
	}));
	
