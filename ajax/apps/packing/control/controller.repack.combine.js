	
	fn.app.packing.repack.dialog_combine = function(id) {
		var item_selected = $("#tblRepack").data("selected");
		$.ajax({
			url: "apps/packing/view/dialog.repack.combine.php",
			data: {items:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_combine_repack"});
			}
		});
	};

	fn.app.packing.repack.combine = function(){
		$.post("apps/packing/xhr/action-combine-repack.php",$("form[name=form_combine]").serialize(),function(response){
			
	
			if(response.success){
				$("#tblRepack").data("selected",[]);
				$("#tblRepack").DataTable().draw();
				$("#dialog_combine_repack").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
	
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "content_copy",
		onclick : "fn.app.packing.repack.dialog_combine()",
		caption : "Combine"
	}));