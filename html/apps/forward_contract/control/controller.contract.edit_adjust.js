	fn.app.forward_contract.contract.dialog_edit_adjust = function(id) {
		$.ajax({
			url: "apps/forward_contract/view/dialog.contract.edit_adjust.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_contract_adjust"});
			}
		});
	};

	fn.app.forward_contract.contract.edit_adjust = function(){
		$.post("apps/forward_contract/xhr/action-edit-contract_adjust.php",$("form[name=form_editadjustcontract]").serialize(),function(response){
			if(response.success){
				$("#tblContract").DataTable().draw();
				$("#dialog_edit_contract_adjust").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
