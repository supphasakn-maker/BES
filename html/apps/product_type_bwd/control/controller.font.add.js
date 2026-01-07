fn.app.product_type_bwd.font.dialog_add = function() {
    $.ajax({
        url: "apps/product_type_bwd/view/dialog.font.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_font"});
        }
    });
};

fn.app.product_type_bwd.font.add = function(){
    $.post("apps/product_type_bwd/xhr/action-add-font.php",$("form[name=form_addfont]").serialize(),function(response){
        if(response.success){
            $("#tblFont").DataTable().draw();
            $("#dialog_font_product").modal("hide");
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
    onclick : "fn.app.product_type_bwd.font.dialog_add()",
    caption : "ADD"
}));
