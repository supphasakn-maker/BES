fn.app.production_blackdust.prepare.dialog_approve = function(id) {
    $.ajax({
        url: "apps/production_blackdust/view/dialog.prepare.approve.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_approve_prepare"});
        }
    });
};

fn.app.production_blackdust.prepare.approve = function(){
    $.post("apps/production_blackdust/xhr/action-approve-prepare.php",$("form[name=form_approveprepare]").serialize(),function(response){
        if(response.success){
            $("#tblPrepare").DataTable().draw();
            $("#dialog_approve_prepare").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
