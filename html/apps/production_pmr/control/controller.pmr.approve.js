fn.app.production_pmr.pmr.dialog_approve = function(id) {
    $.ajax({
        url: "apps/production_pmr/view/dialog.pmr.approve.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_approve_pmr"});
        }
    });
};

fn.app.production_pmr.pmr.approve = function(){
    $.post("apps/production_pmr/xhr/action-approve-pmr.php",$("form[name=form_approvepmr]").serialize(),function(response){
        if(response.success){
            $("#tblPmr").DataTable().draw();
            $("#dialog_approve_pmr").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
