	fn.app.match.silver.dialog_unmatch = function(id) {
		$.ajax({
			url: "apps/match/view/dialog.silver.unmatch.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_unmatch_silver"});
			}
		});
	};

	fn.app.match.silver.unmatch = function(id){
		bootbox.confirm({
			message: "Are sure to unmatch this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/match/xhr/action-unmatch-silver.php",{id:id},function(response){
						if(response.success){
							fn.app.match.silver.clear_selection();
						}else{
							fn.notify.warnbox(response.msg,"Oops...");
						}
					},"json");
				}
			}
		});
		
		
		
	};
