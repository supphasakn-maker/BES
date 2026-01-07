fn.app.product_type_bwd.font.dialog_edit = function(id) {
    $.ajax({
        url: "apps/product_type_bwd/view/dialog.font.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_font"});
        }
    });
};

fn.app.product_type_bwd.font.edit = function(){
    $.post("apps/product_type_bwd/xhr/action-edit-font.php",$("form[name=form_editfont]").serialize(),function(response){
        if(response.success){
            $("#tblFont").DataTable().draw();
            $("#dialog_edit_font").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
