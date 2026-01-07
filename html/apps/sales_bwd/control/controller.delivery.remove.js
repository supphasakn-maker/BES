fn.app.sales_bwd.delivery.dialog_remove = function() {
    var item_selected = $("#tblDelivery").data("selected");
    $.ajax({
        url: "apps/sales_bwd/view/dialog.delivery.remove.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_remove_delivery").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_remove_delivery").modal("show");
            $("#dialog_remove_delivery .btnConfirm").click(function(){
                fn.app.sales_bwd.delivery.remove();
            });
        }
    });
};

fn.app.sales_bwd.delivery.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/sales_bwd/xhr/action-remove-delivery.php",{item:id},function(response){
                $("#tblDelivery").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }else{
        var item_selected = $("#tblDelivery").data("selected");
        $.post("apps/sales_bwd/xhr/action-remove-delivery.php",{items:item_selected},function(response){
            $("#tblDelivery").data("selected",[]);
            $("#tblDelivery").DataTable().draw();
            $("#dialog_remove_delivery").modal("hide");
        });
    }
};
$(".btn-area").append(fn.ui.button({
    class_name : "btn btn-light has-icon",
    icon_type : "material",
    icon : "delete",
    onclick : "fn.app.sales_bwd.delivery.dialog_remove()",
    caption : "REMOVE"
}));
