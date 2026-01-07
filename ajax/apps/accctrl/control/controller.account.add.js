
	fn.app.accctrl.account.dialog_add = function() {
		$.ajax({
			url: "apps/accctrl/view/dialog.account.add.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_account"});			
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_addaccount] select[name=country]",
					"form[name=form_addaccount] select[name=city]",
					"form[name=form_addaccount] select[name=district]",
					"form[name=form_addaccount] select[name=subdistrict]");
				fn.app.contact.address.load_country("form[name=form_addaccount] select[name=country]");
				
				$("select[name=option]").change(function(){
					if($(this).val()=="1"){
						$('#new_org').hide();
					}else{
						$('#new_org').show();
					}
				}).change();

			}	
		});
	};

	fn.app.accctrl.account.add = function(){
		$.post('apps/accctrl/xhr/action-add-account.php',$('form[name=form_addaccount]').serialize(),function(response){
			if(response.success){
				$("#tblAccount").DataTable().draw();
				$("#dialog_add_account").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};
	
	fn.app.accctrl.account.select_organization = function(json){
		$("input[name=org_id]").val(json.id);
		$("input[name=org_name]").val(json.name);
	}
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "add_circle_outline",
		onclick : "fn.app.accctrl.account.dialog_add()",
		caption : lang.main.add
	}));
	
