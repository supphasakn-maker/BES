	fn.app.repack.repack.dialog_restore = function(id) {
		$.ajax({
			url: "apps/repack/view/dialog.repack.restore.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_packing_restore").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_packing_restore").modal("show");
				
			}
		});
	};

	fn.app.repack.repack.restore = function(){
		
		$.post("apps/repack/xhr/action-restore-repack.php",$("form[name=form_restorerepack]").serialize(),function(response){
			if(response.success){
				$("#tblRepack").DataTable().draw();
				$("#dialog_packing_restore").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		

	};
	/*
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.production.produce.dialog_remove()",
		caption : "Remove"
	}));
	*/
