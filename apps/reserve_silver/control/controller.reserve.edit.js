	fn.app.reserve_silver.reserve.dialog_edit = function(id) {
		$.ajax({
			url: "apps/reserve_silver/view/dialog.reserve.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_reserve"});
			}
		});
	};

	fn.app.reserve_silver.reserve.edit = function(){
		$.post("apps/reserve_silver/xhr/action-edit-reserve.php",$("form[name=form_editreserve]").serialize(),function(response){
			if(response.success){
				$("#tblReserve").DataTable().draw();
				$("#dialog_edit_reserve").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
