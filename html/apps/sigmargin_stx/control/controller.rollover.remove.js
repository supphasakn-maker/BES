fn.app.sigmargin_stx.rollover.dialog_remove = function () {
    var item_selected = $("#tblRollover").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.rollover.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_rollover").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_rollover").modal("show");
            $("#dialog_remove_rollover .btnConfirm").click(function () {
                fn.app.sigmargin_stx.rollover.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.rollover.remove = function () {
    var item_selected = $("#tblRollover").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-rollover.php", { items: item_selected }, function (response) {
        $("#tblRollover").data("selected", []);
        $("#tblRollover").DataTable().draw();
        $("#dialog_remove_rollover").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.rollover.dialog_remove()",
    caption: "Remove"
}));
