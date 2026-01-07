
fn.app.defer_adjust.deposit.add = function () {
    $.post("apps/defer_adjust/xhr/action-add_deposit.php", $("form[name=form_adddeposit]").serialize(), function (response) {
        fn.dialog.confirmbox("Confirmation", "Are you sure to Add", function () {
            if (response.success) {
                $("#tblDeposit").DataTable().draw();
                fn.reload();
            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        });
    }, "json");


    return false;
};
