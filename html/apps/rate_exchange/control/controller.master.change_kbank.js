fn.app.rate_exchange.master.dialog_change_kbank = function(id) {
    $.ajax({
        url: "apps/rate_exchange/view/dialog.master.change_kbank.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_change_kbank_master"});
        }
    });
};

fn.app.rate_exchange.master.change_kbank = function(){
    $.post("apps/rate_exchange/xhr/action-change_kbank-master.php",$("form[name=form_change_kbankmaster]").serialize(),function(response){
        if(response.success){
            $("#tblMaster").DataTable().draw();
            $("#dialog_change_kbank_master").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
