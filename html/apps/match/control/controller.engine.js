
fn.app.match.engine.regenitem = function(){
	bootbox.confirm("Are your sure to regen", function(result){
		if(result){
			$.post("apps/match/xhr/action-regen-db.php",function(html){
				$("#regen_output").html(html);
			},"html");
		}
	})
};

fn.app.match.engine.regen = function(){
	bootbox.confirm("Run Now", function(result){
		if(result){
			$.post("apps/match/xhr/action-regen-now.php",function(html){
				$("#regen_output").html(html);
			},"html");
		}
	})
};


