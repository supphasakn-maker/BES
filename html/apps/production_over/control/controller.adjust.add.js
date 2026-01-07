fn.app.production_over.adjust.dialog_add = function () {
    $.ajax({
        url: "apps/production_over/view/dialog.adjust.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_adjust" });
        }
    });
};

fn.app.production_over.adjust.add = function () {
    $.post("apps/production_over/xhr/action-add-adjust.php", $("form[name=form_addadjust]").serialize(), function (response) {
        if (response.success) {
            $("#tblAdjust").DataTable().draw();
            $("#dialog_add_adjust").modal("hide");
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
    onclick: "fn.app.production_over.adjust.dialog_add()",
    caption: "Add"
}));
