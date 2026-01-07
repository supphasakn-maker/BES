fn.app.defer_adjust.physical.add = function () {
    $.post("apps/defer_adjust/xhr/action-add_physical_adjust.php", $("form[name=form_addphysical]").serialize(), function (response) {
        fn.dialog.confirmbox("Confirmation", "Are you sure to Add", function () {
            if (response.success) {
                $("#tblPhysical").DataTable().draw();
                fn.reload();
            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        });
    }, "json");


    return false;
};

