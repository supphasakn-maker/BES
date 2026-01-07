
fn.app.defer_cost.defer.remove = function (id) {
    bootbox.confirm({
        title: "Are you sure to remove?",
        message: "ต้องการลบข้อมูล",
        buttons: {

            confirm: {
                label: '<i class="fa fa-times"></i> ลบเลย'
            }
        },
        callback: function (result) {
            if (result) {
                $.post("apps/defer_cost/xhr/action-remove-defer.php", { id: id }, function (response) {

                    $("#tblDeferc").DataTable().draw();
                    $("#tblPurchase").DataTable().draw();
                    $("#tblPurchaseDefer").DataTable().draw();

                });
            }
            console.log('This was logged in the callback: ' + result);
        }
    });


};
$(".btn-area").append(fn.ui.button({
    class_name: "btn btn-light has-icon",
    icon_type: "material",
    icon: "delete",
    onclick: "fn.app.defer_cost.defer.dialog_remove()",
    caption: "Remove"
}));
