	fn.app.packing.packing.dialog_submit = function(id) {
		$.ajax({
			url: "apps/packing/view/dialog.packing.submit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_submit_packing"});
			}
		});
	};

	fn.app.packing.packing.submit = function(){
		$.post("apps/packing/xhr/action-submit-packing.php",$("form[name=form_submitpacking]").serialize(),function(response){
			if(response.success){
				$("#tblPacking").DataTable().draw();
				$("#dialog_submit_packing").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
