fn.app.production_over.adjust.dialog_remove = function () {
    var item_selected = $("#tblAdjust").data("selected");
    $.ajax({
        url: "apps/production_over/view/dialog.adjust.remove.php",
        data: { item: item_selected },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_remove_adjust").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_remove_adjust").modal("show");
            $("#dialog_remove_adjust .btnConfirm").click(function () {
                fn.app.production_over.adjust.remove();
            });
        }
    });
};

fn.app.production_over.adjust.remove = function () {
    var item_selected = $("#tblAdjust").data("selected");
    $.post("apps/production_over/xhr/action-remove-adjust.php", { items: item_selected }, function (response) {
        $("#tblAdjust").data("selected", []);
        $("#tblAdjust").DataTable().draw();
        $("#dialog_remove_adjust").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.production_over.adjust.dialog_remove()",
    caption: "Remove"
}));
