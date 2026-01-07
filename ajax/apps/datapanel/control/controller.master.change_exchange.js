	fn.app.datapanel.master.dialog_change_exchange = function(id) {
		$.ajax({
			url: "apps/datapanel/view/dialog.master.change_exchange.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_change_exchange_master"});
			}
		});
	};

	fn.app.datapanel.master.change_exchange = function(){
		$.post("apps/datapanel/xhr/action-change_exchange-master.php",$("form[name=form_change_exchangemaster]").serialize(),function(response){
			if(response.success){
				$("#tblMaster").DataTable().draw();
				$("#dialog_change_exchange_master").modal("hide");
				fn.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
