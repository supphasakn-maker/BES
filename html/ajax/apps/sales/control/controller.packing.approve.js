	fn.app.sales.packing.dialog_approve = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.packing.approve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_approve_packing"});
			}
		});
	};

	fn.app.sales.packing.approve = function(id){
		$.post("apps/sales/xhr/action-approve-packing.php",{id:id},function(response){
			if(response.success){
				$("#tblPacking").DataTable().draw();
				$("#dialog_approve_packing").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	fn.app.sales.packing.dialog_view = function(id) {
		$.ajax({
			url: "apps/sales/view/dialog.packing.view.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_view_packing"});
			}
		});
	};
