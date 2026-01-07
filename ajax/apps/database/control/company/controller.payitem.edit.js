
	fn.app.database.company.payitem.dialog_edit = function(id) {
		$.ajax({
			url: "apps/database/view/company/dialog.payitem.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_payitem"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};
	
	fn.app.database.company.payitem.edit = function(){
		$.post('apps/database/xhr/company/action-edit-payitem.php',$('form[name=form_editpayitem]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_edit_payitem").modal('hide');
			}else{
				Swal.fire("Oops...", response.msg, "error");
			}
			
		},'json');
		return false;
	};
