
fn.app.defer_spot.defer.add = function () {
    $.post("apps/defer_spot/xhr/action-add_defer.php", $("form[name=form_adddefer]").serialize(), function (response) {
        fn.dialog.confirmbox("Confirmation", "Are you sure to Add", function () {
            if (response.success) {
                $("#tblDefer").DataTable().draw();
                fn.reload();
            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        });
    }, "json");


    return false;
};
