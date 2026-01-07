fn.app.sigmargin_stx.transfer.dialog_remove = function () {
    var item_selected = $("#tblTransfer").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.transfer.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_transfer").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_transfer").modal("show");
            $("#dialog_remove_transfer .btnConfirm").click(function () {
                fn.app.sigmargin_stx.transfer.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.transfer.remove = function () {
    var item_selected = $("#tblTransfer").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-transfer.php", { items: item_selected }, function (response) {
        $("#tblTransfer").data("selected", []);
        $("#tblTransfer").DataTable().draw();
        $("#dialog_remove_transfer").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.transfer.dialog_remove()",
    caption: "Remove"
}));
