fn.app.product_type_bwd.product.dialog_add = function() {
    $.ajax({
        url: "apps/product_type_bwd/view/dialog.product.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_product"});
        }
    });
};

fn.app.product_type_bwd.product.add = function(){
    $.post("apps/product_type_bwd/xhr/action-add-product.php",$("form[name=form_addproduct]").serialize(),function(response){
        if(response.success){
            $("#tblProduct").DataTable().draw();
            $("#dialog_add_product").modal("hide");
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
    onclick : "fn.app.product_type_bwd.product.dialog_add()",
    caption : "ADD"
}));
