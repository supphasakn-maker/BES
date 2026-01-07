	fn.app.stock_type.data.dialog_view = function(id) {
		$.ajax({
			url: "apps/stock_type/view/dialog.data.view.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_view_data"});
			}
		});
	};

	fn.app.stock_type.data.view = function(){
		$.post("apps/stock_type/xhr/action-view-data.php",$("form[name=form_viewdata]").serialize(),function(response){
			if(response.success){
				$("#tblData").DataTable().draw();
				$("#dialog_view_data").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
