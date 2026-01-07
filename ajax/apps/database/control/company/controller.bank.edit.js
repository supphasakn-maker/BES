
	fn.app.database.company.bank.dialog_edit = function(id) {
		$.ajax({
			url: "apps/database/view/company/dialog.bank.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_bank"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};
	
	fn.app.database.company.bank.edit = function(){
		$.post('apps/database/xhr/company/action-edit-bank.php',$('form[name=form_editbank]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_edit_bank").modal('hide');
			}else{
				Swal.fire("Oops...", response.msg, "error");
			}
			
		},'json');
		return false;
	};
