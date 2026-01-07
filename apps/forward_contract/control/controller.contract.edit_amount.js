	fn.app.forward_contract.contract.dialog_edit_amount = function(id) {
		$.ajax({
			url: "apps/forward_contract/view/dialog.contract.edit_amount.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_contract_amount"});
			}
		});
	};

	fn.app.forward_contract.contract.edit_amount = function(){
		$.post("apps/forward_contract/xhr/action-edit-contract_amount.php",$("form[name=form_editamountcontract]").serialize(),function(response){
			if(response.success){
				$("#tblContract").DataTable().draw();
				$("#dialog_edit_contract_amount").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
