fn.app.profit_loss.profit.search = function () {
    $.post("apps/profit_loss/xhr/action-search-profit.php", $("form[name=filter]").serialize(), function (response) {
        $("#output").html(response);
    }, "html");
    return false;
};
