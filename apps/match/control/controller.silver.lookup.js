	fn.app.match.silver.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.silver.lookup.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_lookup_silver"});
			}
		});
	};

	fn.app.match.silver.lookup = function(){
		$.post("apps/match/xhr/action-lookup-silver.php",$("form[name=form_lookupsilver]").serialize(),function(response){
			if(response.success){
				$("#tblSilver").DataTable().draw();
				$("#dialog_lookup_silver").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
