fn.app.production_round.round.dialog_deapprove = function(id) {
    $.ajax({
        url: "apps/production_round/view/dialog.round.deapprove.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_approve_round"});
        }
    });
};

fn.app.production_round.round.deapprove = function(){
    $.post("apps/production_round/xhr/action-deapprove-round.php",$("form[name=form_approveround]").serialize(),function(response){
        if(response.success){
            $("#tblPlan").DataTable().draw();
            $("#dialog_approve_payment").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
