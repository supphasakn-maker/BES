	fn.app.match.fifosilver.dialog_filter = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.fifosilver.filter.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_filter_fifosilver"});
			}
		});
	};

	fn.app.match.fifosilver.filter = function(){
		$.post("apps/match/xhr/action-filter-fifosilver.php",$("form[name=form_filterfifosilver]").serialize(),function(response){
			if(response.success){
				$("#tblFifosilver").DataTable().draw();
				$("#dialog_filter_fifosilver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
