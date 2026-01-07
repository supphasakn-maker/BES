fn.app.rate_exchange.master.dialog_change_scb = function(id) {
    $.ajax({
        url: "apps/rate_exchange/view/dialog.master.change_scb.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_change_scb_master"});
        }
    });
};

fn.app.rate_exchange.master.change_scb = function(){
    $.post("apps/rate_exchange/xhr/action-change_scb-master.php",$("form[name=form_change_scbmaster]").serialize(),function(response){
        if(response.success){
            $("#tblMaster").DataTable().draw();
            $("#dialog_change_scb_master").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
