	fn.app.forward_contract.contract.dialog_edit = function(id) {
		$.ajax({
			url: "apps/forward_contract/view/dialog.contract.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_contract"});
			}
		});
	};

	fn.app.forward_contract.contract.edit = function(){
		$.post("apps/forward_contract/xhr/action-edit-contract.php",$("form[name=form_editcontract]").serialize(),function(response){
			if(response.success){
				$("#tblContract").DataTable().draw();
				$("#dialog_edit_contract").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
