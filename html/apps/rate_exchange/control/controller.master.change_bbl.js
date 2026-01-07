fn.app.rate_exchange.master.dialog_change_bbl = function(id) {
    $.ajax({
        url: "apps/rate_exchange/view/dialog.master.change_bbl.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_change_bbl_master"});
        }
    });
};

fn.app.rate_exchange.master.change_bbl = function(){
    $.post("apps/rate_exchange/xhr/action-change_bbl-master.php",$("form[name=form_change_bblmaster]").serialize(),function(response){
        if(response.success){
            $("#tblMaster").DataTable().draw();
            $("#dialog_change_bbl_master").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
