	fn.app.sigmargin.interest.dialog_remove = function() {
		var item_selected = $("#tblInterest").data("selected");
		$.ajax({
			url: "apps/sigmargin/view/dialog.interest.remove.php",
			data: {item:item_selected},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_remove_interest").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_remove_interest").modal("show");
				$("#dialog_remove_interest .btnConfirm").click(function(){
					fn.app.sigmargin.interest.remove();
				});
			}
		});
	};

	fn.app.sigmargin.interest.remove = function(){
		var item_selected = $("#tblInterest").data("selected");
		$.post("apps/sigmargin/xhr/action-remove-interest.php",{items:item_selected},function(response){
			$("#tblInterest").data("selected",[]);
			$("#tblInterest").DataTable().draw();
			$("#dialog_remove_interest").modal("hide");
		});
	};
	$(".btn-area").append(fn.ui.button({
		class_name : "btn btn-light has-icon",
		icon_type : "material",
		icon : "delete",
		onclick : "fn.app.sigmargin.interest.dialog_remove()",
		caption : "Remove"
	}));
