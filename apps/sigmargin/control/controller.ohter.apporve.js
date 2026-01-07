	fn.app.sigmargin.ohter.dialog_apporve = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.ohter.apporve.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_apporve_ohter"});
			}
		});
	};

	fn.app.sigmargin.ohter.apporve = function(){
		$.post("apps/sigmargin/xhr/action-apporve-ohter.php",$("form[name=form_apporveohter]").serialize(),function(response){
			if(response.success){
				$("#tblOhter").DataTable().draw();
				$("#dialog_apporve_ohter").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
