

	fn.app.match.fifousd.search = function(){
		$.post("apps/match/xhr/action-search-fifousd.php",$("form[name=filter]").serialize(),function(response){
			$("#output").html(response);
		},"html");
		return false;
	};
