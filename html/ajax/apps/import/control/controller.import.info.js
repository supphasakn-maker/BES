	fn.app.import.import.dialog_info = function(id) {
		
		$.ajax({
			url: "apps/import/view/dialog.import.info.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_info_import").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_info_import").modal("show");
				
			}
		});
	};

