fn.app.announce.difference.dialog_insure_150 = function (id) {
    $.ajax({
        url: "apps/announce/view/dialog.difference.insure_150.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_insure_150" });
        }
    });
};

fn.app.announce.difference.insure_150 = function () {
    $.post("apps/announce/xhr/action-insure_150.php", $("form[name=form_insure_150]").serialize(), function (response) {
        if (response.success) {
            $("#dialog_insure_150").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
