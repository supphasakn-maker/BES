fn.app.sigmargin_stx.int_rollover.dialog_remove = function () {
    var item_selected = $("#tblInt_rollover").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.int_rollover.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_int_rollover").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_int_rollover").modal("show");
            $("#dialog_remove_int_rollover .btnConfirm").click(function () {
                fn.app.sigmargin_stx.int_rollover.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.int_rollover.remove = function () {
    var item_selected = $("#tblInt_rollover").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-int_rollover.php", { items: item_selected }, function (response) {
        $("#tblInt_rollover").data("selected", []);
        $("#tblInt_rollover").DataTable().draw();
        $("#dialog_remove_int_rollover").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.int_rollover.dialog_remove()",
    caption: "Remove"
}));
