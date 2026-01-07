fn.app.production_pmr.out.dialog_add = function () {
    $.ajax({
        url: "apps/production_pmr/view/dialog.out.add.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_out" });
        }
    });
};

fn.app.production_pmr.out.add = function (id) {
    $.post("apps/production_pmr/xhr/action-add-out.php", $("form[name=form_addout]").serialize(), function (response) {
        if (response.success) {
            $("#tblOut").DataTable().draw();
            $("#dialog_add_out").modal("hide");
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
    onclick: "fn.app.production_pmr.out.dialog_add()",
    caption: "เพิ่ม"
}));
