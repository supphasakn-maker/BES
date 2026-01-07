fn.app.production_pmr.recycle.dialog_add = function () {
    $.ajax({
        url: "apps/production_pmr/view/dialog.recycle.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_recycle" });
        }
    });
};

fn.app.production_pmr.recycle.add = function (id) {
    $.post("apps/production_pmr/xhr/action-add-recycle.php", $("form[name=form_addrecycle]").serialize(), function (response) {
        if (response.success) {
            $("#tblRecycle").DataTable().draw();
            $("#dialog_add_recycle").modal("hide");
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
    onclick: "fn.app.production_pmr.recycle.dialog_add()",
    caption: "เพิ่มการส่งผลิต"
}));
