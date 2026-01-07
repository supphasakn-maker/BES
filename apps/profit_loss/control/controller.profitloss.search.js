fn.app.profit_loss.profitloss.search = function(){
    $.post("apps/profit_loss/xhr/action-search-profitloss.php",$("form[name=filter]").serialize(),function(response){
        $("#output").html(response);
    },"html");
    return false;
};
