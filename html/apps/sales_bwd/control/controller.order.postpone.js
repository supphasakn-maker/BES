fn.app.sales_bwd.order.dialog_postpone = function(id) {
    $.ajax({
        url: "apps/sales_bwd/view/dialog.order.postpone.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_postpone_order"});
        }
    });
};

fn.app.sales_bwd.order.postpone = function(){
    $.post("apps/sales_bwd/xhr/action-postpone-order.php",$("form[name=form_postponeorder]").serialize(),function(response){
        if(response.success){
            $("#tblOrder").DataTable().draw();
            $("#dialog_postpone_order").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
