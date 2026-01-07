fn.app.production_silverplate.prepare.dialog_remove = function () {
	var item_selected = $("#tblPrepare").data("selected");
	$.ajax({
		url: "apps/production_silverplate/view/dialog.prepare.remove.php",
		data: { item: item_selected },
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			$("#dialog_remove_prepare").on("hidden.bs.modal", function () {
				$(this).remove();
			});
			$("#dialog_remove_prepare").modal("show");
			$("#dialog_remove_prepare .btnConfirm").click(function () {
				fn.app.production_silverplate.prepare.remove();
			});
		}
	});
};

fn.app.production_silverplate.prepare.remove = function (id) {
	bootbox.confirm("Are you sure to remove?", function (result) {
		if (result) {
			$.post("apps/production_silverplate/xhr/action-remove-prepare.php", { id: id }, function (response) {
				$("#tblPrepare").data("selected", []);
				$("#tblPrepare").DataTable().draw();
				$("#dialog_remove_prepare").modal("hide");
			});
		}
	});
	/*
	var item_selected = $("#tblPrepare").data("selected");
	$.post("apps/production_silverplate/xhr/action-remove-prepare.php",{items:item_selected},function(response){
		$("#tblPrepare").data("selected",[]);
		$("#tblPrepare").DataTable().draw();
		$("#dialog_remove_prepare").modal("hide");
	});
	*/
};

/*
$(".btn-area").append(fn.ui.button({
	class_name : "btn btn-light has-icon",
	icon_type : "material",
	icon : "delete",
	onclick : "fn.app.production_silverplate.prepare.dialog_remove()",
	caption : "Remove"
}));
*/
