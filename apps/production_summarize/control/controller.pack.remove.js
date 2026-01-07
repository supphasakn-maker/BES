	

	fn.app.production_summarize.pack.remove = function(id){
		bootbox.confirm("Are you sure to remove?", function(result){ 
			if(result){
				$.post("apps/production_summarize/xhr/action-remove-pack.php",{id:id},function(response){
					$("#tblPacking").DataTable().draw();
				});
			}
		});

	};
	