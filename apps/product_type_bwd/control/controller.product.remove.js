fn.app.product_type_bwd.product.dialog_remove = function() {
    var item_selected = $("#tblProduct").data("selected");
    $.ajax({
        url: "apps/product_type_bwd/view/dialog.product.remove.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_remove_product").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_remove_product").modal("show");
            $("#dialog_remove_product .btnConfirm").click(function(){
                fn.app.product_type_bwd.product.remove();
            });
        }
    });
};

fn.app.product_type_bwd.product.remove = function(){
    var item_selected = $("#tblProduct").data("selected");
    $.post("apps/product_type_bwd/xhr/action-remove-product.php",{items:item_selected},function(response){
        $("#tblProduct").data("selected",[]);
        $("#tblProduct").DataTable().draw();
        $("#dialog_remove_product").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name : "btn btn-light has-icon",
    icon_type : "material",
    icon : "delete",
    onclick : "fn.app.product_type_bwd.product.dialog_remove()",
    caption : "REMOVE"
}));
