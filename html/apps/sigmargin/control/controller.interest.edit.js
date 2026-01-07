	fn.app.sigmargin.interest.dialog_edit = function(id) {
		$.ajax({
			url: "apps/sigmargin/view/dialog.interest.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_interest"});
			}
		});
	};

	fn.app.sigmargin.interest.edit = function(){
		$.post("apps/sigmargin/xhr/action-edit-interest.php",$("form[name=form_editinterest]").serialize(),function(response){
			if(response.success){
				$("#tblInterest").DataTable().draw();
				$("#dialog_edit_interest").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
