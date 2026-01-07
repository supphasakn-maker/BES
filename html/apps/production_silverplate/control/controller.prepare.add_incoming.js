


fn.app.production_silverplate.prepare.dialog_add_incoming = function (table) {
    $.ajax({
        url: "apps/production_silverplate/view/dialog.prepare.incoming.php",
        data: { table: table },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#ddialog.prepare.incoming" });
        }
    });
};

fn.app.production_silverplate.prepare.add_incoming = function () {
    $.post("apps/production_silverplate/xhr/action-import-incoming.php", $("form[name=form_incoming]").serialize(), function (response) {
        if (response.success) {
            $("#tblIncomingplan").DataTable().draw();
            $("#dialog.prepare.incoming").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};

$("form[name=incoming] input[type=file]").change(function () {
    var data = new FormData($("form[name=incoming]")[0]);
    jQuery.ajax({
        url: 'apps/production_silverplate/xhr/action-upload-incoming.php',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                fn.app.production_silverplate.prepare.dialog_add_incoming(response.table);
            } else {
                fn.notify.warnbox(response.msg, "Oops...");
            }
        }
    });

});


