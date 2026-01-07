	fn.app.packing.packing.dialog_cancel = function(id) {
		$.ajax({
			url: "apps/packing/view/dialog.packing.cancel.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_cancel_packing"});
			}
		});
	};

	fn.app.packing.packing.cancel = function(){
		$.post("apps/packing/xhr/action-cancel-packing.php",$("form[name=form_cancelpacking]").serialize(),function(response){
			if(response.success){
				$("#tblPacking").DataTable().draw();
				$("#dialog_cancel_packing").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
