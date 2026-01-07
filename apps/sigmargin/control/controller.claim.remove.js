	fn.app.sigmargin.claim.dialog_remove = function() {
		var item_selected = $("#tblClaim").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.claim.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_claim").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_claim").modal("show");
				$("#dialog_remove_claim .btnConfirm").click(function(){
					fn.app.sigmargin.claim.remove();
				});
			}
		});
	};

	fn.app.sigmargin.claim.remove = function(){
		var item_selected = $("#tblClaim").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-claim.php",{items:item_selected},function(response){
			$("#tblClaim").data("selected",[]);
			$("#tblClaim").DataTable().draw();
			$("#dialog_remove_claim").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.claim.dialog_remove()",
		caption : "Remove"
	}));
