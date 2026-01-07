fn.app.sigmargin_stx.cash.dialog_remove = function () {
    var item_selected = $("#tblCash").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.cash.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_cash").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_cash").modal("show");
            $("#dialog_remove_cash .btnConfirm").click(function () {
                fn.app.sigmargin_stx.cash.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.cash.remove = function () {
    var item_selected = $("#tblCash").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-cash.php", { items: item_selected }, function (response) {
        $("#tblCash").data("selected", []);
        $("#tblCash").DataTable().draw();
        $("#dialog_remove_cash").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.cash.dialog_remove()",
    caption: "Remove"
}));
