
	fn.app.logger.log.dialog_view = function(id) {
		$.ajax({
			url: "apps/logger/view/dialog.log.view.php",
			data : {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_view_log"});
			}	
		});
	};


