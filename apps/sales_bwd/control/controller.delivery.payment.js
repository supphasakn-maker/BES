fn.app.sales_bwd.delivery.dialog_payment = function(id) {
    $.ajax({
        url: "apps/sales_bwd/view/dialog.delivery.payment.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_payment_delivery"});
        }
    });
};

fn.app.sales_bwd.delivery.payment = function(){
    $.post("apps/sales_bwd/xhr/action-payment-delivery.php",$("form[name=form_paymentdelivery]").serialize(),function(response){
        if(response.success){
            $("#tblDelivery").DataTable().draw();
            $("#dialog_payment_delivery").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
