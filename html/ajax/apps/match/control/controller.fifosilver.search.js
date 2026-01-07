
	fn.app.match.fifosilver.search = function(){
		$.post("apps/match/xhr/action-search-fifosilver.php",$("form[name=filter]").serialize(),function(response){
			$("#output").html(response);
		},"html");
		return false;
	};
