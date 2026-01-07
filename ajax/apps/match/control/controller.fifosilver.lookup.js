	fn.app.match.fifosilver.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.fifosilver.lookup.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_lookup_fifosilver"});
			}
		});
	};

	fn.app.match.fifosilver.lookup = function(){
		$.post("apps/match/xhr/action-lookup-fifosilver.php",$("form[name=filter]").serialize(),function(response){
			if(response.success){
				$("#tblFifosilver").DataTable().draw();
				$("#dialog_lookup_fifosilver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
