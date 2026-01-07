	fn.app.defer_split.spot.dialog_remove = function(id) {

		$.ajax({
			url: "apps/defer_split/view/dialog.split.remove.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_split").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_split").modal("show");
			}
		});
	};

	fn.app.defer_split.spot.remove = function(id){
		$.post("apps/defer_split/xhr/action-remove-split.php",{id:id},function(response){
			
			$("#tblDefer").DataTable().draw();
			$("#tblSplitted").DataTable().draw();
			
			$("#dialog_remove_split").modal("hide");
		});
	};
