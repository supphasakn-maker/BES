
	fn.app.setting.company.save_core = function(){
		bootbox.confirm('Please confirm to save?', function(result){
			if(result){
				$.post('apps/setting/xhr/action-save-company-core.php',$('form[name=form_setting]').serialize(),function(response){
					if(response.success){
						window.location.reload();
					}else{
						fn.notify.warnbox(response.msg,"Oops...");
					}
				},'json');
			}
		});
	};
	
	
	$(function(){
		$("#fIcon").change(function(){
			var data = new FormData($("#form_change_icon")[0]);
			jQuery.ajax({
				url: 'apps/setting/xhr/action-upload-icon.php',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				type: 'POST',
				dataType: 'json',
				success: function(response){
					if(response.success){
						fn.navigate("setting","view=company");
					}else{
						fn.engine.alert("Alert",response.msg);
					}	
				}
			});
		});
		
		$("#btnChangeIcon").click(function(){
			$("#dialogChangeIcon").modal("hide");
			$("#fIcon").click();
		});
	});
	
	
	
	fn.app.setting.company.change_icon = function(){
		$("#dialogChangeIcon").modal("show");
	};

	fn.app.setting.company.remove_icon = function(){
		fn.confirmbox("Confirmation?","Are you sure to remove icon.",function(confirmed){
			if(confirmed){
				$.post('apps/setting/xhr/action-remove-icon.php',function(json){
					$("#dialogChangeIcon").modal("hide");
					if(json.success){
						$("#trademark_icon").attr("src","img/default/organization.png");
					}
				},"json");
			}
		});
	};
	
	fn.app.setting.company.dialog_edit = function(id) {
		$.ajax({
			url: "apps/setting/view/dialog.company.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_company"});
				$('select[name=cbbCountry]').select2();
				
				fn.app.setting.address.initial(
					"form[name=form_edituser] select[name=cbbCountry]",
					"form[name=form_edituser] select[name=cbbCity]",
					"form[name=form_edituser] select[name=cbbDistrict]",
					"form[name=form_edituser] select[name=cbbSubdistrict]");
			}	
		});
	};
	
	fn.app.setting.company.edit = function(){
		$.post('apps/setting/xhr/action-edit-company.php',$('form[name=form_editcompany]').serialize(),function(response){
			if(response.success){
				$("#tblUser").DataTable().draw();
				$("#dialog_edit_user").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};

	
	
		
	
	