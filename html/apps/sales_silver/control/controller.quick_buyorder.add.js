fn.app.sales_silver.quick_buyorder.dialog_add = function() {
    $.ajax({
        url: "apps/sales_silver/view/dialog.quick_buyorder.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_quick_buyorder"});
        }
    });
};

fn.app.sales_silver.quick_buyorder.add = function(){
    $.post("apps/sales_silver/xhr/action-add-quick_buyorder.php",$("form[name=form_addquickbuy_order]").serialize(),function(response){
        if(response.success){
            $("#tblQuickBuyOrder").DataTable().draw();
            $("form[name=form_addquickbuy_order]")[0].reset();
            $("form[name=form_addquickbuy_order] select[name=customer_id]").val("").trigger('change.select2');;
            $("#dialog_add_quick_buyorder").modal("hide");
            fn.reload();
            
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};