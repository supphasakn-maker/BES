
	fn.app.database.country.dialog_edit = function(id) {
		$.ajax({
			url: "apps/database/view/geography/dialog.country.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_country"});
			}	
		});
	};
	
	fn.app.database.country.edit = function(){
		$.post('apps/database/xhr/geography/action-edit-country.php',$('form[name=form_editcountry]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_edit_country").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
