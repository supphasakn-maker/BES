

	fn.app.match.fifousd.lookup = function(){
		$.post("apps/match/xhr/action-lookup-fifousd.php",$("form[name=form_lookupfifousd]").serialize(),function(response){
			if(response.success){
				$("#tblFifousd").DataTable().draw();
				$("#dialog_lookup_fifousd").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
