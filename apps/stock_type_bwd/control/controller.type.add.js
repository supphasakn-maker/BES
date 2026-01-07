fn.app.stock_type_bwd.type.dialog_add = function() {
    $.ajax({
        url: "apps/stock_type_bwd/view/dialog.type.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_type"});
        }
    });
};

fn.app.stock_type_bwd.type.add = function(){
    $.post("apps/stock_type_bwd/xhr/action-add-type.php",$("form[name=form_addtype]").serialize(),function(response){
        if(response.success){
            $("#tblType").DataTable().draw();
            $("#dialog_add_type").modal("hide");
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
    onclick : "fn.app.stock_type_bwd.type.dialog_add()",
    caption : "ADD TYPE"
}));
