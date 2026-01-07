fn.app.forward_contract.contract.dialog_edit_interest = function (id) {
    $.ajax({
        url: "apps/forward_contract/view/dialog.contract.edit_interest.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_edit_interest" });
        }
    });
};

fn.app.forward_contract.contract.edit_interest = function () {
    $.post("apps/forward_contract/xhr/action-edit_interest.php", $("form[name=form_edit_interest]").serialize(), function (response) {
        if (response.success) {
            $("#tblContract").DataTable().draw();
            $("#dialog_edit_interest").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};
