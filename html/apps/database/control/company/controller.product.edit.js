
	fn.app.database.company.product.dialog_edit = function(id) {
		$.ajax({
			url: "apps/database/view/company/dialog.product.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_product"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};
	
	fn.app.database.company.product.edit = function(){
		$.post('apps/database/xhr/company/action-edit-product.php',$('form[name=form_editproduct]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_edit_product").modal('hide');
			}else{
				Swal.fire("Oops...", response.msg, "error");
			}
			
		},'json');
		return false;
	};
