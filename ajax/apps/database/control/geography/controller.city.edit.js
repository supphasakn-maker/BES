
	fn.app.database.city.dialog_edit = function(id) {
		$.ajax({
			url: "apps/database/view/geography/dialog.city.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_city"});
				$("select[name=country]").select2({width:"100%"});
			}	
		});
	};
	
	fn.app.database.city.edit = function(){
		$.post('apps/database/xhr/geography/action-edit-city.php',$('form[name=form_editcity]').serialize(),function(response){
			if(response.success){
				$("#tblDatabase").DataTable().draw();
				$("#dialog_edit_city").modal('hide');
			}else{
				Swal.fire("Oops...", response.msg, "error");
			}
			
		},'json');
		return false;
	};
