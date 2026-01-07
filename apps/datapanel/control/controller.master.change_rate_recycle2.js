fn.app.datapanel.master.dialog_change_rate_recycle2 = function(id) {
    $.ajax({
        url: "apps/datapanel/view/dialog.master.change_rate_recycle2.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_change_rate_recycle2"});
        }
    });
};

fn.app.datapanel.master.change_rate_recycle2 = function(){
    $.post("apps/datapanel/xhr/action-change_rate_recycle2.php",$("form[name=form_change_rate_recycle2]").serialize(),function(response){
        if(response.success){
            $("#tblMaster").DataTable().draw();
            $("#dialog_change_rate_recycle2").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
