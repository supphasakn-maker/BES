fn.app.sigmargin_stx.int_rate.dialog_add = function () {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.int_rate.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_int_rate" });
        }
    });
};

fn.app.sigmargin_stx.int_rate.add = function () {
    $.post("apps/sigmargin_stx/xhr/action-add-int_rate.php", $("form[name=form_addint_rate]").serialize(), function (response) {
        if (response.success) {
            $("#tblInt_rate").DataTable().draw();
            $("#dialog_add_int_rate").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "add_circle_outline",
    onclick: "fn.app.sigmargin_stx.int_rate.dialog_add()",
    caption: "Add"
}));
