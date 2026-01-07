fn.app.profit_loss.usd.search = function () {
    $.post("apps/profit_loss/xhr/action-search-usd.php", $("form[name=filter]").serialize(), function (response) {
        $("#output").html(response);
    }, "html");
    return false;
};
