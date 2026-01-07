fn.app.production_crucible.crucible.dialog_approve = function(id) {
    $.ajax({
        url: "apps/production_crucible/view/dialog.crucible.approve.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_approve_crucible"});
        }
    });
};

fn.app.production_crucible.crucible.approve = function(){
    $.post("apps/production_crucible/xhr/action-approve-crucible.php",$("form[name=form_approvecrucible]").serialize(),function(response){
        if(response.success){
            $("#tblCrucible").DataTable().draw();
            $("#dialog_approve_crucible").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
