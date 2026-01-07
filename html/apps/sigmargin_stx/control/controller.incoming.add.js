fn.app.sigmargin_stx.incoming.dialog_add = function () {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.incoming.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_incoming" });
        }
    });
};

fn.app.sigmargin_stx.incoming.add = function () {
    $.post("apps/sigmargin_stx/xhr/action-add-incoming.php", $("form[name=form_addincoming]").serialize(), function (response) {
        if (response.success) {
            $("#tblIncoming").DataTable().draw();
            $("#dialog_add_incoming").modal("hide");
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
    onclick: "fn.app.sigmargin_stx.incoming.dialog_add()",
    caption: "Add"
}));
