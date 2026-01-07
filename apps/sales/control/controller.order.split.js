fn.app.sales.order.dialog_split = function (id) {
	$.ajax({
		url: "apps/sales/view/dialog.order.split.php",
		data: { id: id },
		type: "POST",
		dataType: "html",
		success: function (html) {
			$("body").append(html);
			fn.ui.modal.setup({ dialog_id: "#dialog_split_order" });
		},
		error: function(xhr, status, error) {
			fn.notify.warnbox("ไม่สามารถโหลด Dialog ได้: " + error, "Error");
		}
	});
};

fn.app.sales.order.append_split = function () {
	var s = '';
	s += '<tr>';
	s += '<td><label>ใบแยก</label></td>';
	s += '<td><input data-name="amount" name="amount[]" type="number" step="0.01" min="0.01" class="form-control text-right" value="" placeholder="0.00"></td>';
	s += '<td><label>วันส่ง</label></td>';
	s += '<td><input data-name="date" name="date[]" type="date" class="form-control text-right" value=""></td>';
	s += '<td class="p-0"><a href="javascript:void(0);" onclick="fn.app.sales.order.remove_split(this)" class="btn btn-danger">X</a></td>';
	s += '</tr>';
	$("form[name=form_splitorder] table tbody").append(s);
};

fn.app.sales.order.remove_split = function(element) {
	var tbody = $("form[name=form_splitorder] table tbody");
	var rowCount = tbody.find("tr").length;
	
	if (rowCount <= 2) {
		fn.notify.warnbox("ต้องมีอย่างน้อย 2 รายการ", "Warning");
		return false;
	}
	
	$(element).closest("tr").remove();
	
	fn.app.sales.order.calculate_split_total();
};

fn.app.sales.order.calculate_split_total = function() {
	var total = 0;
	var expected = parseFloat($("input[name=total]").val()) || 0;
	
	$("input[data-name=amount]").each(function() {
		var val = parseFloat($(this).val()) || 0;
		total += val;
	});
	
	var difference = total - expected;
	var status = Math.abs(difference) < 0.01 ? "ถูกต้อง ✓" : "ไม่ตรง (" + difference.toFixed(2) + ")";
	
	if ($("#split_total_display").length) {
		$("#split_total_display").html("ผลรวม: " + total.toFixed(2) + " / " + expected.toFixed(2) + " - " + status);
	}
	
	return total;
};

fn.app.sales.order.split = function () {
	var total = 0;
	var has_error = false;
	var error_messages = [];
	var row_number = 0;
	
	$("input[data-name=amount]").each(function() {
		row_number++;
		var val = parseFloat($(this).val());
		
		if (isNaN(val) || val <= 0 || $(this).val().trim() === '') {
			has_error = true;
			error_messages.push("รายการที่ " + row_number + ": กรุณาระบุจำนวนที่ถูกต้อง");
			$(this).addClass("is-invalid"); 
			return; 
		}
		
		$(this).removeClass("is-invalid");
		total += val;
	});

	row_number = 0;
	$("input[data-name=date]").each(function() {
		row_number++;
		var date_val = $(this).val().trim();
		
		if (date_val !== "" && !Date.parse(date_val)) {
			has_error = true;
			error_messages.push("รายการที่ " + row_number + ": รูปแบบวันที่ไม่ถูกต้อง");
			$(this).addClass("is-invalid");
		} else {
			$(this).removeClass("is-invalid");
		}
	});

	if (has_error) {
		fn.notify.warnbox(error_messages.join("<br>"), "กรุณาตรวจสอบข้อมูล");
		return false;
	}

	var expected_total = parseFloat($("input[name=total]").val());
	
	if (Math.abs(total - expected_total) > 0.01) {
		fn.notify.warnbox(
			"จำนวนรวม " + total.toFixed(2) + " ไม่ตรงกับต้นฉบับ " + expected_total.toFixed(2) + 
			"<br>ต้องการรวมกัน " + expected_total.toFixed(2) + " แต่ได้ " + total.toFixed(2),
			"ผลรวมไม่ถูกต้อง"
		);
		return false;
	}

	var delivery_count = parseInt($("input[name=delivery_id]").val()) || 0;
	if (delivery_count > 0) {
		fn.notify.warnbox(
			"มีการใส่ถุงในระบบขนส่งแล้ว กรุณาแจ้งขนส่ง และแจ้ง Trader ด้วยเพื่อไม่ให้มีปัญหาภายหลัง", 
			"คำเตือน"
		);
		return false;
	}

	if (typeof fn.ui.loading !== 'undefined') {
		fn.ui.loading.show();
	}

	$.ajax({
		url: "apps/sales/xhr/action-split-order.php",
		data: $("form[name=form_splitorder]").serialize(),
		type: "POST",
		dataType: "json",
		success: function(response) {
			if (typeof fn.ui.loading !== 'undefined') {
				fn.ui.loading.hide();
			}
			
			if (response.success) {
				fn.notify.successbox("แยก Order สำเร็จ", "สำเร็จ");
				
				// Refresh table
				if ($("#tblOrder").length && $.fn.DataTable.isDataTable("#tblOrder")) {
					$("#tblOrder").DataTable().draw();
				}
				
				// Close dialog
				$("#dialog_split_order").modal("hide");
				
			} else {
				fn.notify.warnbox(response.msg || "เกิดข้อผิดพลาด", "Oops...");
			}
		},
		error: function(xhr, status, error) {
			if (typeof fn.ui.loading !== 'undefined') {
				fn.ui.loading.hide();
			}
			
			var errorMsg = "เกิดข้อผิดพลาดในการส่งข้อมูล";
			
			try {
				var response = JSON.parse(xhr.responseText);
				if (response.msg) {
					errorMsg = response.msg;
				}
			} catch(e) {
				errorMsg += ": " + error;
			}
			
			fn.notify.warnbox(errorMsg, "Error");
		}
	});

	return false;
};

$(document).on('keyup change', 'input[data-name=amount]', function() {
	fn.app.sales.order.calculate_split_total();
});