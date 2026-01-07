

	fn.app.match.profit.search = function(){
		$.post("apps/match/xhr/action-search-profit.php",$("form[name=filter]").serialize(),function(response){
			$("#output").html(response);
		},"html");
		return false;
	};
