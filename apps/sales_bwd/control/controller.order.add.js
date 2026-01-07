fn.app.sales_bwd.order.dialog_add = function() {
    $.ajax({
        url: "apps/sales_bwd/view/dialog.order.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_order"});
            $("input[name=delivery_lock]").change(function(){
                $("input[name=delivery_date]").prop('readOnly',$(this).prop('checked'));
            });
        }
    });
};

fn.app.sales_bwd.order.add = function(){
    $.post("apps/sales_bwd/xhr/action-add-order.php",$("form[name=form_addorder]").serialize(),function(response){
        if(response.success){
            $("#tblOrder").DataTable().draw();
            $("#dialog_add_order").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
$(".btn-area").append(fn.ui.button({
    class_name : "btn btn-light has-icon",
    icon_type : "material",
    icon : "add_circle_outline",
    onclick : "fn.app.sales_bwd.order.dialog_add()",
    caption : "Add"
}));
