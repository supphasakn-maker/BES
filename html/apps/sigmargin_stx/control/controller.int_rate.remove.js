fn.app.sigmargin_stx.int_rate.dialog_remove = function () {
    var item_selected = $("#tblInt_rate").data("selected");
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.int_rate.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_int_rate").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_int_rate").modal("show");
            $("#dialog_remove_int_rate .btnConfirm").click(function () {
                fn.app.sigmargin_stx.int_rate.remove();
            });
        }
    });
};

fn.app.sigmargin_stx.int_rate.remove = function () {
    var item_selected = $("#tblInt_rate").data("selected");
    $.post("apps/sigmargin_stx/xhr/action-remove-int_rate.php", { items: item_selected }, function (response) {
        $("#tblInt_rate").data("selected", []);
        $("#tblInt_rate").DataTable().draw();
        $("#dialog_remove_int_rate").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.sigmargin_stx.int_rate.dialog_remove()",
    caption: "Remove"
}));
