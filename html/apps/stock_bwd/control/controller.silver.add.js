fn.app.stock_bwd.silver.dialog_add = function () {
    $.ajax({
        url: "apps/stock_bwd/view/dialog.stock_silver.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_silver" });

            $("form[name=form_addsilver] select[name=product_type]").select2();
            $("form[name=form_addsilver] select[name=product_type]").bind().change(function () {
                $.post("apps/stock_bwd/xhr/action-load-type.php", { product_type: $(this).val() }, function (response) {
                    $("form[name=form_addsilver] input[name=prefix]").val(response.products.code);
                    if (response.next_start) {
                        $("form[name=form_addsilver] input[name=start]").val(response.next_start);
                        console.log("Next start:", response.next_start);
                    }
                }, "json");
            }).change();
        }
    });
};


fn.app.stock_bwd.silver.add = function (id) {
    $.post("apps/stock_bwd/xhr/action-add-silver.php", $("form[name=form_addsilver]").serialize(), function (response) {
        if (response.success) {
            $("#tblStockSilver").DataTable().draw();
            $("#dialog_add_silver").modal("hide");
            var s = '';

            s += '<div>จำนวนทีเพิ่ม ' + response.created.length + ' รายการ</div>';
            s += '<div>จำนวนที่ซ้ำ ' + response.redundant.length + ' รายการ</div>';


            fn.notify.warnbox(s, "Result");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};

