	fn.app.match.usd.dialog_unmatch = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.usd.unmatch.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_unmatch_usd"});
			}
		});
	};


	
	fn.app.match.usd.unmatch = function(id){
		bootbox.confirm({
			message: "Are sure to unmatch this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/match/xhr/action-unmatch-usd.php",{id:id},function(response){
						if(response.success){
							fn.app.match.usd.clear_selection();
						}else{
							fn.notify.warnbox(response.msg,"Oops...");
						}
					},"json");
				}
			}
		});
		
		
		
	};
	
	fn.app.match.usd.spot_item_remove = function(id){
		bootbox.confirm({
			message: "Are sure to remove this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/match/xhr/action-remove-usd-spot.php",{id:id},function(response){
						if(response.success){
							fn.app.match.usd.clear_selection();
						}else{
							fn.notify.warnbox(response.msg,"Oops...");
						}
					},"json");
				}
			}
		});
		
		
		
	};
	
	
	
