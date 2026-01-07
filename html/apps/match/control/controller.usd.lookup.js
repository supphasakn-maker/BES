	fn.app.match.usd.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.usd.lookup.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_lookup_usd"});
			}
		});
	};

	fn.app.match.usd.lookup = function(){
		$.post("apps/match/xhr/action-lookup-usd.php",$("form[name=form_lookupusd]").serialize(),function(response){
			if(response.success){
				$("#tblUsd").DataTable().draw();
				$("#dialog_lookup_usd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
		fn.app.match.usd.dialog_lookup_usd_map = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.usd.lookup.usd_map.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_lookup_usd"});
			}
		});
	};



