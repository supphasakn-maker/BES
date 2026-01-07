fn.app.announce.difference.dialog_pmdc_grains = function (id) {
    $.ajax({
        url: "apps/announce/view/dialog.difference.pmdc_grains.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_pmdc_grains" });
        }
    });
};

fn.app.announce.difference.pmdc_grains = function () {
    $.post("apps/announce/xhr/action-pmdc_grains.php", $("form[name=form_pmdc_grains]").serialize(), function (response) {
        if (response.success) {
            $("#dialog_pmdc_grains").modal("hide");
            fn.reload();
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
