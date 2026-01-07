fn.app.production_blackdust.prepare.dialog_add = function () {
    $.ajax({
        url: "apps/production_blackdust/view/dialog.prepare.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_prepare" });
        }
    });
};

fn.app.production_blackdust.prepare.add = function () {
    $.post("apps/production_blackdust/xhr/action-add-prepare.php", $("form[name=form_addprepare]").serialize(), function (response) {
        if (response.success) {
            $("#tblPrepare").DataTable().draw();
            $("#dialog_add_prepare").modal("hide");


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
    onclick: "fn.app.production_blackdust.prepare.dialog_add()",
    caption: "Add"
}));
