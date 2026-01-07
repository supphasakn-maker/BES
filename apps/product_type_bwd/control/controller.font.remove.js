fn.app.product_type_bwd.font.dialog_remove = function() {
    var item_selected = $("#tblFont").data("selected");
    $.ajax({
        url: "apps/product_type_bwd/view/dialog.font.remove.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_remove_font").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_remove_font").modal("show");
            $("#dialog_remove_font .btnConfirm").click(function(){
                fn.app.product_type_bwd.font.remove();
            });
        }
    });
};

fn.app.product_type_bwd.font.remove = function(){
    var item_selected = $("#tblFont").data("selected");
    $.post("apps/product_type_bwd/xhr/action-remove-font.php",{items:item_selected},function(response){
        $("#tblFont").data("selected",[]);
        $("#tblFont").DataTable().draw();
        $("#dialog_remove_font").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name : "btn btn-light has-icon",
    icon_type : "material",
    icon : "delete",
    onclick : "fn.app.product_type_bwd.font.dialog_remove()",
    caption : "REMOVE"
}));
