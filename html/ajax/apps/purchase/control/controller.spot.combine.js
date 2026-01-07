	fn.app.purchase.spot.dialog_combine = function(id) {
		$.ajax({
			url: "apps/purchase/view/dialog.spot.combine.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_combine_spot"});
			}
		});
	};

	fn.app.purchase.spot.combine = function(){
		$.post("apps/purchase/xhr/action-combine-spot.php",$("form[name=form_combinespot]").serialize(),function(response){
			if(response.success){
				$("#tblSpot").DataTable().draw();
				$("#dialog_combine_spot").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
