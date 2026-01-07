fn.app.sigmargin_stx.interest.dialog_add = function () {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.interest.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_interest" });
        }
    });
};

fn.app.sigmargin_stx.interest.add = function () {
    $.post("apps/sigmargin_stx/xhr/action-add-interest.php", $("form[name=form_addinterest]").serialize(), function (response) {
        if (response.success) {
            $("#tblInterest").DataTable().draw();
            $("#dialog_add_interest").modal("hide");
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
    onclick: "fn.app.sigmargin_stx.interest.dialog_add()",
    caption: "Add"
}));
