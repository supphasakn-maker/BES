
	fn.app.notify.message.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/notify/view/dialog.message.lookup.php",
			data : {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_lookup_message").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_lookup_message").modal('show');
       
			}	
		});
	};


