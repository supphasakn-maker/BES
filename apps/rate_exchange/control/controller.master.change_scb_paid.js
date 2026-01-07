fn.app.rate_exchange.master.change_scb_paid = function(){
    $.post('apps/rate_exchange/xhr/action-scb_paid.php',$('form[name=rate]').serialize(),function(response){
        if(response.success){
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },'json');
    return false;
};