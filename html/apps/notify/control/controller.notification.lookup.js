
	fn.app.notify.notification.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/notify/view/dialog.notification.lookup.php",
			data : {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_lookup_notification").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_lookup_notification").modal('show');
       
			}	
		});
	};


