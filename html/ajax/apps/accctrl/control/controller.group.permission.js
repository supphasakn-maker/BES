

	fn.app.accctrl.group.dialog_permission = function(id) {
		$.ajax({
			url: "apps/accctrl/view/dialog.group.permission.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$('#btnSelectAll').click(function(){
					var all_selected = true;
					$("form[name=form_edit_permission] input[type=checkbox]").each(function(){
						if(!$(this).is(':checked')){
							all_selected = false;
						}
					});
					
					if(all_selected){
						$("form[name=form_edit_permission] input[type=checkbox]").prop('checked', false);
					}else{
						$("form[name=form_edit_permission] input[type=checkbox]").prop('checked', true);
					}
				});
				
				$('#dialog_edit_group').on('shown.bs.modal', function () {
					$("#txtName").focus();
				});
				$("#dialog_edit_group").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_edit_group").modal('show');
				
				$(".checkrow").click(function(){
					var tr = $(this).parent();
					var unchecked = 0;
					$(tr).find('.custom-control input:checkbox').each(function(){
						if(!$(this).prop('checked')){
							unchecked++;
						}
					})
					if(unchecked>0){
						$(tr).find('input:checkbox').prop('checked', true );
					}else{
						$(tr).find('input:checkbox').prop('checked', false );
					}
				});
				
			}
		});
	};
	
	fn.app.accctrl.group.save_permission = function(){
		$.post('apps/accctrl/xhr/action-edit-group-permission.php',$('form[name=form_edit_permission]').serialize(),function(response){
			if(response.success){
				$("#tblGroup").DataTable().draw();
				$("#dialog_edit_group").modal('hide');
			}else{
				fn.engine.alert("Alert",response.msg);
			}
			
		},'json');
		return false;
	};

