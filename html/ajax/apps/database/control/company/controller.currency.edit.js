
	fn.app.database.company.currency.dialog_edit = function(id) {
		$.ajax({
			url: "apps/database/view/company/dialog.currency.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_currency"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};
	
	fn.app.database.company.currency.edit = function(){
		$.post('apps/database/xhr/company/action-edit-currency.php',$('form[name=form_editcurrency]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_edit_currency").modal('hide');
			}else{
				Swal.fire("Oops...", response.msg, "error");
			}
			
		},'json');
		return false;
	};
