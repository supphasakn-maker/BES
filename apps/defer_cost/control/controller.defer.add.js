fn.app.defer_cost.defer.add = function () {

    $.post("apps/defer_cost/xhr/action-add-defer.php", {
        purchase: $("#tblPurchase").data("selected"),
        purchase_defer: $("#tblPurchaseDefer").data("selected"),
        date: $("form[name=adding] input[name=date]").val()
    }, function (response) {
        if (response.success) {
            $("#tblPurchase").data("selected", []);
            $("#tblPurchaseDefer").data("selected", []);
            $("#tblDefer").DataTable().draw();
            $("#tblPurchase").DataTable().draw();
            $("#tblPurchaseDefer").DataTable().draw();
            fn.notify.successbox(response.msg, "Complete");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};