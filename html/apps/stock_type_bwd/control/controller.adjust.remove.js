fn.app.stock_type_bwd.adjust.dialog_remove = function() {
    var item_selected = $("#tblAdjust").data("selected");
    $.ajax({
        url: "apps/stock_type_bwd/view/dialog.adjust.remove.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_remove_adjust").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_remove_adjust").modal("show");
            $("#dialog_remove_adjust .btnConfirm").click(function(){
                fn.app.stock_type_bwd.adjust.remove();
            });
        }
    });
};

fn.app.stock_type_bwd.adjust.remove = function(){
    var item_selected = $("#tblAdjust").data("selected");
    $.post("apps/stock_type_bwd/xhr/action-remove-adjust.php",{items:item_selected},function(response){
        $("#tblAdjust").data("selected",[]);
        $("#tblAdjust").DataTable().draw();
        $("#dialog_remove_adjust").modal("hide");
    });
};
$(".btn-area").append(fn.ui.button({
    class_name : "btn btn-light has-icon",
    icon_type : "material",
    icon : "delete",
    onclick : "fn.app.stock_type_bwd.adjust.dialog_remove()",
    caption : "REMOVE ADJUST"
}));
