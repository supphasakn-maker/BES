fn.app.sigmargin_stx.incoming.dialog_remove = function () {
    var item_selected = $("#tblIncoming").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.incoming.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_incoming").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_incoming").modal("show");
            $("#dialog_remove_incoming .btnConfirm").click(function () {
                fn.app.sigmargin_stx.incoming.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.incoming.remove = function () {
    var item_selected = $("#tblIncoming").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-incoming.php", { items: item_selected }, function (response) {
        $("#tblIncoming").data("selected", []);
        $("#tblIncoming").DataTable().draw();
        $("#dialog_remove_incoming").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.incoming.dialog_remove()",
    caption: "Remove"
}));
