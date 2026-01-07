fn.app.profit_loss_bwd.profit.search = function () {
    $.post("apps/profit_loss_bwd/xhr/action-search-profit.php", $("form[name=filter]").serialize(), function (response) {
        $("#output").html(response);
    }, "html");
    return false;
};
