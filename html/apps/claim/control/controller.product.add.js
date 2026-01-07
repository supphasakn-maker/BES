fn.app.claim.product.dialog_add = function() {
    $.ajax({
        url: "apps/claim/view/dialog.product.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_product"});
            
            $("form[name=form_addproduct] select[name=order_id]").select2();
            $("form[name=form_addproduct] select[name=order_id]").unbind().change(function(){
                $.post("apps/claim/xhr/action-load-order.php",{order_id:$(this).val()},function(response){
                    $("form[name=form_addproduct] input[name=org_name]").val(response.customer.org_name);
                    $("form[name=form_addproduct] input[name=product_id]").val(response.order.product_id);
                },"json");
            }).change();
        }
    });
};

fn.app.claim.product.add = function(){
    $.post("apps/claim/xhr/action-add-product.php",$("form[name=form_addproduct]").serialize(),function(response){
        if(response.success){
            $("#tblProduct").DataTable().draw();
            $("#dialog_add_product").modal("hide");
            fn.reload();
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
    onclick : "fn.app.claim.product.dialog_add()",
    caption : "Add"
}));
