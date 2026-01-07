fn.app.profit_loss.trade.search = function () {
    $.post("apps/profit_loss/xhr/action-search-trade.php", $("form[name=filter]").serialize(), function (response) {
        $("#output").html(response);
    }, "html");
    return false;
};
