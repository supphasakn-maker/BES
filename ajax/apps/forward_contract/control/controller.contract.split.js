	fn.app.forward_contract.contract.dialog_split = function(id) {
		$.ajax({
			url: "apps/forward_contract/view/dialog.contract.split.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_split_contract"});
			}
		});
	};

	fn.app.forward_contract.contract.split = function(){
		$.post("apps/forward_contract/xhr/action-split-contract.php",$("form[name=form_splitcontract]").serialize(),function(response){
			if(response.success){
				$("#tblContract").DataTable().draw();
				$("#dialog_split_contract").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
