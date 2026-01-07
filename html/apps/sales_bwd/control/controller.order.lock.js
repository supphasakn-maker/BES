fn.app.sales_bwd.order.dialog_lock = function(id) {
    $.ajax({
        url: "apps/sales_bwd/view/dialog.order.lock.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_lock_order"});
        }
    });
};

fn.app.sales_bwd.order.lock = function(){
    $.post("apps/sales_bwd/xhr/action-lock-order.php",$("form[name=form_lockorder]").serialize(),function(response){
        if(response.success){
            $("#tblOrder").DataTable().draw();
            $("#dialog_lock_order").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
