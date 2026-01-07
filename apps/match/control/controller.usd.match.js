fn.app.match.usd.dialog_match = function (id) {
	var purchase_spot_selected = $("#tblPurchaseSpot").data("selected");
	var purchase_usd_selected = $("#tblPurchaseUSD").data("selected");
	$.ajax({
		url: "apps/match/view/dialog.usd.match.php",
		data: { purchase_spot: purchase_spot_selected, purchase_usd: purchase_usd_selected },
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			fn.ui.modal.setup({ dialog_id: "#dialog_match_usd" });
			fn.app.match.usd.match_calculation();
		}
	});
};

fn.app.match.usd.match_calculation = function () {
	let total_spot_amount = 0;
	$("[xname=spot_amount]").each(function () {
		total_spot_amount += parseFloat($(this).val());
	});
	$("#usd_total_match_spot").html(fn.ui.numberic.format(total_spot_amount, 2));

	let total_purchase_amount = 0;
	$("[xname=purchase_amount]").each(function () {
		total_purchase_amount += parseFloat($(this).val());
	});
	$("#usd_total_match_usd").html(fn.ui.numberic.format(total_purchase_amount, 2));
}

fn.app.match.usd.match = function () {
	$.post("apps/match/xhr/action-match-usd.php", $("form[name=form_matchusd]").serialize(), function (response) {
		if (response.success) {
			fn.app.match.usd.clear_selection();
			$("#dialog_match_usd").modal("hide");
		} else {
			fn.notify.warnbox(response.msg, "Oops...");
		}
	}, "json");
	return false;
};


fn.app.match.usd.clear_selection = function () {

	$("#tblUSD [control=chk_usd]").removeClass("fa-check-square").addClass("fa-square");
	$("#tblPurchaseSpot [control=chk_purchase_spot]").removeClass("fa-check-square").addClass("fa-square");
	$("#tblPurchaseUSD [control=chk_purchase_ีหก]").removeClass("fa-check-square").addClass("fa-square");

	$("#tblUSD").data("selected", []);
	$("#tblPurchaseSpot").data("selected", []);
	$("#tblPurchaseUSD").data("selected", []);

	$("#tblUSD").DataTable().draw();
	$("#tblPurchaseSpot").DataTable().draw();
	$("#tblPurchaseUSD").DataTable().draw();
}

