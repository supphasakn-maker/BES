
fn.app.defer_adjust.usd.add = function () {
    $.post("apps/defer_adjust/xhr/action-add_usd.php", $("form[name=form_addusd]").serialize(), function (response) {
        fn.dialog.confirmbox("Confirmation", "Are you sure to Add", function () {
            if (response.success) {
                $("#tblUSD").DataTable().draw();
                fn.reload();
            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        });
    }, "json");


    return false;
};
