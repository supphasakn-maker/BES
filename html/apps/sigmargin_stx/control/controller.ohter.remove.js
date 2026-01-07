fn.app.sigmargin_stx.ohter.dialog_remove = function () {
    var item_selected = $("#tblOhter").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.ohter.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_ohter").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_ohter").modal("show");
            $("#dialog_remove_ohter .btnConfirm").click(function () {
                fn.app.sigmargin_stx.ohter.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.ohter.remove = function () {
    var item_selected = $("#tblOhter").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-ohter.php", { items: item_selected }, function (response) {
        $("#tblOhter").data("selected", []);
        $("#tblOhter").DataTable().draw();
        $("#dialog_remove_ohter").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.ohter.dialog_remove()",
    caption: "Remove"
}));
