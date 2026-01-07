fn.app.sales_back_bwd.sale_back.add = function () {
    $.post("apps/sales_back_bwd/xhr/action-add-sale_back.php", $("form[name=form_addquick_order]").serialize(), function (response) {
        fn.dialog.confirmbox("Confirmation", "Are you sure to Add", function () {
            if (response.success) {
                $("#tblsaleback").DataTable().draw();
                $("form[name=form_addquick_order]")[0].reset();
                fn.reload();

            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        });
    }, "json");
    return false;
};